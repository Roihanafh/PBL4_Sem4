@empty($mahasiswa)
    <div id="modal-delete" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data mahasiswa tidak ditemukan
                </div>
                <a href="{{ url('/mahasiswa') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div class="modal-header">
        <h5 class="modal-title">Detail Mahasiswa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <table class="table table-sm table-bordered table-striped">
            <tr>
                <th class="text-right col-4">NIM:</th>
                <td class="col-8">{{ $mahasiswa->mhs_nim }}</td>
            </tr>
            <tr>
                <th class="text-right">Nama Lengkap:</th>
                <td>{{ $mahasiswa->full_name }}</td>
            </tr>
            <tr>
                <th class="text-right">Alamat:</th>
                <td>{{ $mahasiswa->alamat }}</td>
            </tr>
            <tr>
                <th class="text-right">No. Telepon:</th>
                <td>{{ $mahasiswa->telp }}</td>
            </tr>
            <tr>
                <th class="text-right">Program Studi:</th>
                <td class="col-9">{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Angkatan:</th>
                <td>{{ $mahasiswa->angkatan }}</td>
            </tr>
            <tr>
                <th class="text-right">Jenis Kelamin:</th>
                <td>{{ $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <th class="text-right">IPK:</th>
                <td>{{ $mahasiswa->ipk ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Status Magang:</th>
                <td>{{ ucfirst($mahasiswa->status_magang) }}</td>
            </tr>
            <tr>
                <th class="text-right">Username:</th>
                <td>{{ $mahasiswa->user->username ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Password:</th>
                <td class="col-9">********</td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button onclick="modalAction('{{ url('/mahasiswa/' . $mahasiswa->mhs_nim . '/edit_ajax') }}')" class="btn btn-success btn-sm">
            Edit
        </button>
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" aria-label="Close">
            Close
        </button>
    </div>
@endempty
