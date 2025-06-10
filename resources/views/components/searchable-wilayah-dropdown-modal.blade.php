@props([
    'id' => 'wilayahSearch',
    'name' => 'wilayah_id',
    'placeholder' => 'Cari Wilayah...',
    'selectedId' => null,
    'selectedName' => '',
    'selectedType' => '',
    'required' => false,
    'autofocus' => false,
    'class' => '',
    'readonly' => false,
])

<div class="search-container {{ $class }}" id="{{ $id }}-search-container">
    <div class="input-group">
        <input
            type="text"
            class="form-control search-input"
            placeholder="{{ $placeholder }}"
            aria-label="{{ $placeholder }}"
            id="{{ $id }}-input"
            name="{{ $id }}_input"
            value="{{ $selectedName }}"
            @if($required) required @endif
            @if($autofocus) autofocus @endif
            @if($readonly) readonly @endif
        >
        <small id="error-{{ $id }}-input" class="text-danger d-none"></small>
    </div>

    <input type="hidden" name="{{ $name }}" id="{{ $id }}-hidden" value="{{ $selectedId }}">
    <input type="hidden" name="{{ $name }}_type" id="{{ $id }}-type" value="{{ $selectedType }}">

    <div class="search-results d-none" id="{{ $id }}-results">
        <div class="list-group"></div>
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
        .search-results .list-group-item-type {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            margin-left: 0.5rem;
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
        $(document).ready(function () {
            window.initWilayahSearch = function(id) {
                const searchInput = document.getElementById(`${id}-input`);
                const searchResults = document.getElementById(`${id}-results`);
                const hiddenInput = document.getElementById(`${id}-hidden`);
                const resultsList = searchResults.querySelector('.list-group');
                const readonlyStatus = searchInput.hasAttribute('readonly');

                if (!searchInput || !searchResults || !hiddenInput || readonlyStatus) return;

                let searchTimeout = null;

                searchInput.addEventListener('input', function() {
                    const query = searchInput.value.trim();


                    if (searchTimeout) {
                        clearTimeout(searchTimeout);
                    }

                    if (query.length < 2) {
                        searchResults.classList.add('d-none');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetchWilayah(query);
                    }, 300);
                });

                function fetchWilayah(query) {
                    fetch(`{{ route('wilayah.search') }}?query=${encodeURIComponent(query)}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(response => {
                            if (response.status === 'success') {
                                updateResults(response.data);
                                searchResults.classList.remove('d-none');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching wilayah data:', error);
                        });
                }

                function updateResults(items) {
                    resultsList.innerHTML = '';

                    if (!items || items.length === 0) {
                        const noResults = document.createElement('div');
                        noResults.className = 'list-group-item no-results-message';
                        noResults.textContent = 'Data wilayah tidak ditemukan';
                        resultsList.appendChild(noResults);
                        return;
                    }

                    items.forEach(item => {
                        const listItem = document.createElement('a');
                        listItem.href = '#';
                        listItem.className = 'list-group-item list-group-item-action';
                        listItem.dataset.id = item.id;
                        listItem.dataset.name = item.value;
                        listItem.dataset.type = item.type;

                        const textSpan = document.createElement('span');
                        textSpan.textContent = item.value;
                        listItem.appendChild(textSpan);

                        resultsList.appendChild(listItem);
                    });
                }

                resultsList.addEventListener('click', function(e) {
                    if (e.target.classList.contains('list-group-item-action') ||
                        e.target.parentElement.classList.contains('list-group-item-action')) {

                        e.preventDefault();

                        const target = e.target.classList.contains('list-group-item-action') ?
                            e.target : e.target.parentElement;

                        const selectedId = target.dataset.id;
                        const selectedName = target.dataset.name;
                        const selectedType = target.dataset.type;

                        searchInput.value = selectedName;
                        hiddenInput.value = selectedId;

                        const typeInput = document.getElementById(`${id}-type`);
                        if (typeInput) {
                            typeInput.value = selectedType;
                        }

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

            const wilayahSearches = document.querySelectorAll('.search-container');
            wilayahSearches.forEach(container => {
                const inputElement = container.querySelector('.search-input');
                const id = inputElement.id.replace('-input', '');
                initWilayahSearch(id);
            });
        });
    </script>
@endonce
