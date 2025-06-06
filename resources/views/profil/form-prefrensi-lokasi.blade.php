<div class="modal-dialog modal-dialog-centered" style="max-width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{$title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form id="form-tambah">
                @csrf
                <div class="form-group">
                    <div class="input-group">
                        <x-searchable-wilayah-dropdown-modal
                            id="prefrensi_lokasi_id"
                            name="prefrensi_lokasi_id"
                            class="flex-grow-1"
                        />
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-primary" type="button" id="btn-tambah">Tambah</button>
                        </div>
                    </div>
                </div>
            </form>
            <hr>
            <div id="tag-cross-delete">
                @include('partials._tag_cross_delete')
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#btn-tambah').click(function (e) {
            e.preventDefault();

            const formData = new FormData($('#form-tambah')[0]);

            $.ajax({
                url: "{{ route('profil.prefrensi-lokasi.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('#tag-cross-delete').html(response.html);
                        $('#prefrensi_lokasi_id-hidden').val('').trigger('change');
                        $('#prefrensi_lokasi_id-input').val('');
                        $('#prefrensi_lokasi_id-type').val('');
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
