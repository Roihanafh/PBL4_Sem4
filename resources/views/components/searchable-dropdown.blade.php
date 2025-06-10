@props([
    'id' => 'searchableDropdown',
    'name' => 'search_item',
    'placeholder' => 'Search...',
    'dataUrl' => '',
    'items' => [],
    'selectedId' => null,
    'selectedName' => '',
    'required' => false,
    'autofocus' => false,
    'class' => '',
    'readonly' => false,
])

<div class="search-container {{ $class }}" data-url="{{ $dataUrl }}" id="{{ $id }}-search-container">
    <div class="input-group">
        <input
            type="text"
            class="form-control search-input"
            placeholder="{{ $placeholder }}"
            aria-label="{{ $placeholder }}"
            id="{{ $id }}-input"
            name="{{ $id }}-input"
            value="{{ $selectedName }}"
            @if($required) required @endif
            @if($autofocus) autofocus @endif
            @if($readonly) readonly @endif
        >
        <small id="error-{{ $id }}-input" class="text-danger d-none"></small>
    </div>

    <input type="hidden" name="{{ $name }}" id="{{ $id }}-hidden" value="{{ $selectedId }}">

    <div class="search-results d-none" id="{{ $id }}-results">
        <div class="list-group">
            @if(count($items) > 0)
                @foreach ($items as $item)
                    <a href="#"
                       class="list-group-item list-group-item-action"
                       data-id="{{ $item['id'] }}"
                       data-name="{{ $item['value'] }}">
                        {{ $item['value'] }}
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</div>

@once
    <style>
        .search-results {
            max-height: 300px;
            overflow-y: auto;
            position: absolute;
            width: 100%;
            z-index: 1000;
            border: 1px solid #ddd;
            border-radius: 0 0 4px 4px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .search-results .list-group-item {
            cursor: pointer;
            border-left: none;
            border-right: none;
            padding: 0.75rem 1rem;
        }
        .search-results .list-group-item:first-child {
            border-top: none;
        }
        .search-results .list-group-item:hover {
            background-color: #f8f9fa;
        }
        .search-input {
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .search-container {
            position: relative;
        }
    </style>
@endonce

@once
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataCache = {};

            window.initSearchableDropdown = function(id, dataUrl) {
                const searchInput = document.getElementById(`${id}-input`);
                const searchResults = document.getElementById(`${id}-results`);
                const hiddenInput = document.getElementById(`${id}-hidden`);
                const resultsList = searchResults.querySelector('.list-group');
                const readonlyStatus = searchInput.hasAttribute('readonly');

                let items = [];

                const preRenderedItems = Array.from(resultsList.querySelectorAll('.list-group-item-action')).map(el => ({
                    id: el.dataset.id,
                    value: el.dataset.name
                }));

                if (preRenderedItems.length > 0) {
                    items = preRenderedItems;
                }

                if (!searchInput || !searchResults || !hiddenInput) return;

                if (dataUrl && preRenderedItems.length === 0) {
                    fetchData(dataUrl);
                }

                function fetchData(url) {
                    if (dataCache[url]) {
                        items = dataCache[url];
                        return;
                    }

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            items = data;
                            dataCache[url] = data;
                            items = data;

                            if (hiddenInput.value) {
                                const selectedItem = items.find(item => item.id == hiddenInput.value);
                                if (selectedItem) {
                                    searchInput.value = selectedItem.value;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                        })
                }

                if (readonlyStatus) {
                    return;
                }

                searchInput.addEventListener('focus', function() {
                    if (searchInput.value.length >= 2) {
                        updateResults();
                        searchResults.classList.remove('d-none');
                    }
                });

                searchInput.addEventListener('input', function() {
                    if (searchInput.value.length >= 2) {
                        updateResults();
                        searchResults.classList.remove('d-none');
                    } else {
                        searchResults.classList.add('d-none');
                    }
                });

                function updateResults() {
                    if (preRenderedItems.length > 0) {
                        const listItems = resultsList.querySelectorAll('.list-group-item-action');
                        let visibleCount = 0;

                        listItems.forEach(item => {
                            if (item.textContent.toLowerCase().includes(searchInput.value.toLowerCase())) {
                                item.style.display = 'block';
                                visibleCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        const existingNoResults = resultsList.querySelector('.no-results-message');
                        if (existingNoResults) {
                            existingNoResults.remove();
                        }

                        if (visibleCount === 0) {
                            const noResults = document.createElement('div');
                            noResults.className = 'list-group-item no-results-message';
                            noResults.textContent = 'Data tidak ditemukan';
                            resultsList.appendChild(noResults);
                        }

                        return;
                    }

                    resultsList.innerHTML = '';

                    if (!items.length) return;

                    const filteredItems = items.filter(item =>
                        item.value.toLowerCase().includes(searchInput.value.toLowerCase())
                    );

                    filteredItems.forEach(item => {
                        const listItem = document.createElement('a');
                        listItem.href = '#';
                        listItem.className = 'list-group-item list-group-item-action';
                        listItem.dataset.id = item.id;
                        listItem.dataset.name = item.value;
                        listItem.textContent = item.value;
                        resultsList.appendChild(listItem);
                    });

                    if (filteredItems.length === 0) {
                        const noResults = document.createElement('div');
                        noResults.className = 'list-group-item no-results-message';
                        noResults.textContent = 'No results found';
                        resultsList.appendChild(noResults);
                    }
                }

                resultsList.addEventListener('click', function(e) {
                    if (e.target.classList.contains('list-group-item-action')) {
                        e.preventDefault();
                        const selectedId = e.target.dataset.id;
                        const selectedName = e.target.dataset.name;

                        searchInput.value = selectedName;
                        hiddenInput.value = selectedId;
                        searchResults.classList.add('d-none');

                        const event = new Event('change', { bubbles: true });
                        hiddenInput.dispatchEvent(event);
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('d-none');
                    }
                });
            }

            const dropdowns = document.querySelectorAll('.search-container');
            dropdowns.forEach(dropdown => {
                const inputElement = dropdown.querySelector('.search-input');
                const id = inputElement.id.replace('-input', '');
                const dataUrl = dropdown.dataset.url || '';
                initSearchableDropdown(id, dataUrl);
            });
        });
    </script>
@endonce
