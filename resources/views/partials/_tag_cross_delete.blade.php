<div id="tag-cross-delete">
@foreach($tag['items'] ?? [] as $t)
    <div class="tag-item d-inline-flex align-items-center bg-primary text-white rounded-pill px-3 py-1 mb-1" data-id="{{ $t['id'] }}">
        <span>{{ $t['value'] ?? '-' }}</span>
        <form action="{{ route($tag['route'], $t['id']) }}" method="POST" class="ms-2">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-close btn-close-white btn-sm text-white btn-tag-cross-delete">X</button>
        </form>
    </div>
@endforeach
</div>


<script>
    $(document).on('click', '.btn-tag-cross-delete', function (e) {
        e.preventDefault();

        const $form = $(this).closest('form');
        const deleteUrl = $form.attr('action');
        const tagWrapper = $form.closest('.tag-item');

        $.ajax({
            url: deleteUrl,
            type: "POST",
            data: {
                '_token': '{{ csrf_token() }}',
                '_method': 'DELETE'
            },
            success: function (response) {
                if (response.success) {
                    // Fade out the tag element
                    tagWrapper.fadeOut(300, function () {
                        $(this).remove();
                    });

                    // Jika kamu ingin re-render semua tag:
                    // $('#tag-cross-delete').html(response.html);
                }
            },
            error: function (xhr) {
                console.error('Gagal menghapus:', xhr.responseText);
                alert('Gagal menghapus data');
            }
        });
    });
</script>
