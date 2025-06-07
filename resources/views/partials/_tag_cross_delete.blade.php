@foreach($tag as $t)
    <div class="d-inline-flex align-items-center bg-primary text-white rounded-pill px-3 py-1 mb-1">
        <span>{{ $t['value'] }}</span>
        <form action="{{ route($tag->route, $t['id']) }}" method="POST" class="ms-2">
            <button type="button" class="btn btn-close btn-close-white btn-sm text-white" id="btn-tag-cross-delete">X</button>
        </form>
    </div>
@endforeach
<script>
    $(document).on('click', '#btn-tag-cross-delete', function (e) {
        e.preventDefault();

        const deleteUrl = $(this).closest('form').attr('action');

        $.ajax({
            url: deleteUrl,
            type: "POST",
            data: {
                '_token': '{{ csrf_token() }}',
                '_method': 'DELETE'
            },
            success: function (response) {
                if (response.success) {
                    $('#tag-cross-delete').html(response.html);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    });
</script>
