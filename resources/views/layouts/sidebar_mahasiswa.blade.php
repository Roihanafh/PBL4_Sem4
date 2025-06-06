<!-- Sidebar -->
      <div class="sidebar sidebar-style-2" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
              <img
                src="{{ asset('img/MagangIn.png') }}"
                alt="navbar brand"
                class="navbar-brand"
                height="100"
              />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item">
                <a href="{{ url('/dashboard-mahasiswa') }}">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts1">
                  <i class="fas fa-user-cog"></i>
                  <p>Manajemen Akun & Profil</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="charts1">
                  <ul class="nav nav-collapse">
                    <li>
                      <a>
                        <span class="sub-item">Profil akademik</span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="sub-item">Dokumen (CV, sertifikat)</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts2">
                  <i class="fas fa-briefcase"></i>
                  <p>Rekomendasi Magang</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="charts2">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="{{ route('lowongan.rekomendasi') }}">
                        <span class="sub-item">Rekomendasi berdasarkan profil</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#pengajuan">
                  <i class="fas fa-file-signature"></i>
                  <p>Pengajuan Magang</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="pengajuan">
                  <ul class="nav nav-collapse">
                    <li>
                      <a >
                        <span class="sub-item">Detail lowongan</span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="sub-item">Pencarian dan filter</span>14
                      </a>
                    </li>
                    <li>
                      <a href="{{ url('pengajuan-magang-mhs') }}">
                        <span class="sub-item">Ajukan lamaran</span>
                      </a>
                    </li>
                    <li>
                      <a href="{{ url('pengajuan-magang-mhs') }}">
                        <span class="sub-item">Status pengajuan</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a href="{{ url('/message') }}">
                  <i class="fas fa-envelope"></i>
                  <p>Message</p>
                </a>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts4">
                  <i class="fas fa-chart-line"></i>
                  <p>Monitoring dan Evaluasi Magang</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="charts4">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="{{ url('log-aktivitas-mhs') }}">
                        <span class="sub-item">Isi log harian</span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="sub-item">Unggah sertifikat</span>
                      </a>
                    </li>
                    <li>
                      <a href="{{ url('feedback-magang') }}">
                        <span class="sub-item">Feedback pengalaman</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->