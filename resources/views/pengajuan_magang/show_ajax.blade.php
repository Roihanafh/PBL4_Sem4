@if (@empty($lamaran) || @empty($prodi) || @empty($perusahaan))
    <div id="modal-delete" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1a2e4f; color: white;">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Kesalahan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="fas fa-ban fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading">Kesalahan!!!</h5>
                        <p>Data lamaran, lowongan, atau prodi tidak ditemukan</p>
                    </div>
                </div>
                <a href="{{ url('/pengajuan-magang') }}" class="btn btn-warning btn-sm" style="background-color: #f4b740; border-color: #f4b740; color: #1a2e4f;"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
            </div>
        </div>
    </div>
@else
    <div class="modal-header" style="background-color: #1a2e4f; color: white;">
        <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>Detail Lamaran Mahasiswa</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        {{-- Informasi Mahasiswa --}}
        <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-user-graduate me-2"></i>Informasi Mahasiswa</h6>
        <div class="card mb-4">
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <tr>
                        <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">NIM:</th>
                        <td class="col-8">{{ $lamaran->mahasiswa->mhs_nim }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Nama Lengkap:</th>
                        <td>{{ $lamaran->mahasiswa->full_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Alamat:</th>
                        <td>{{ $lamaran->mahasiswa->alamat }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">No. Telepon:</th>
                        <td>{{ $lamaran->mahasiswa->telp }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Program Studi:</th>
                        <td>{{ $prodi->nama_prodi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Status Lamaran:</th>
                        <td>
                            <span class="badge" style="{{ $lamaran->status == 'Diterima' ? 'background-color: #28a745; color: white;' : ($lamaran->status == 'Ditolak' ? 'background-color: #dc3545; color: white;' : 'background-color: #f4b740; color: #1a2e4f;') }}">
                                {{ $lamaran->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Informasi Lowongan --}}
        <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-briefcase me-2"></i>Informasi Lowongan</h6>
        <div class="card mb-4">
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <tr>
                        <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">Judul Lowongan:</th>
                        <td>{{ $lamaran->lowongan->judul ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Perusahaan:</th>
                        <td>{{ $perusahaan->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Lokasi:</th>
                        <td>{{ $perusahaan->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Tanggal Lamaran:</th>
                        <td>{{ $lamaran->tanggal_lamaran ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Dosen Pembimbing --}}
        @if ($lamaran->dosen)
            <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-chalkboard-teacher me-2"></i>Dosen Pembimbing</h6>
            <div class="card mb-4">
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <tr>
                            <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">Nama Dosen:</th>
                            <td>{{ $lamaran->dosen->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Email Dosen:</th>
                            <td>{{ $lamaran->dosen->email ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @else
            <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-chalkboard-teacher me-2"></i>Pilih Dosen Pembimbing</h6>
            <div class="card mb-4">
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <tr>
                            <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">Nama Dosen:</th>
                            <td>
                                <select name="dosen_id" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}" {{ $lamaran->dosen_id == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
    </div>
    <div class="modal-footer">
        <button onclick="modalAction('{{ url('/pengajuan-magang/' . $lamaran->lamaran_id . '/edit_ajax') }}')" class="btn btn-sm" style="background-color: #28a745; border-color: #28a745; color: white;">
            <i class="fas fa-check me-2"></i>Terima
        </button>
        <button onclick="modalAction('{{ url('/pengajuan-magang/' . $lamaran->lamaran_id . '/edit_ajax') }}')" class="btn btn-sm" style="background-color: #dc3545; border-color: #dc3545; color: white;">
            <i class="fas fa-times me-2"></i>Tolak
        </button>
    </div>
@endempty