
      <!-- Sidebar -->
      <div class="sidebar sidebar-style-2" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
              <img
                src="img/MagangIn.png"
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
                <a
                  data-bs-toggle="collapse"
                  href="#dashboard"
                  class="collapsed"
                  aria-expanded="false"
                >
                  <a href="{{ url('dashboard') }}">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#base">
                  <i class="fas fa-user-friends"></i>
                  <p>Manajemen Pengguna</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="base">
                  <ul class="nav nav-collapse">
                    <li>
                       <a href="{{ url('admin') }}">
                        <span class="sub-item">Data Admin</span>
                      </a>
                    </li>
                    <li>
                       <a href="{{ url('dosen') }}">
                        <span class="sub-item">Data Dosen</span>
                      </a>
                    </li>
                    <li>
                    <li>
                      <a href="{{ url('mahasiswa') }}">
                        <span class="sub-item">Data Mahasiswa</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#perusahaan">
                  <i class="fas fa-building"></i>
                  <p>Manajemen Perusahaan Magang</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="perusahaan">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="components/avatars.html">
                        <span class="sub-item">Data Perusahaan</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#forms">
                  <i class="fas fa-hourglass-half"></i>
                  <p>Manajemen Periode Magang</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="forms">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="{{ url('periode') }}">
                        <span class="sub-item">Data Periode Magang</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#tables">
                  <i class="fas fa-book"></i>
                  <p>Manajemen Program Studi</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="tables">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="{{ url('prodi') }}">
                        <span class="sub-item">Data Program Studi</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#maps">
                  <i class="fas fa-briefcase"></i>
                  <p>Manajemen Lowongan Magang</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="maps">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="maps/googlemaps.html">
                        <span class="sub-item">Data Lowongan Magang</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts">
                  <i class="fas fa-laptop-code"></i>
                  <p>Manajemen Kegiatan Magang</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="charts">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="charts/charts.html">
                        <span class="sub-item">Data Kegiatan Magang</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts1">
                  <i class="fas fa-laptop-code"></i>
                  <p>Manajemen Akun & Profile</p>
                </a>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts2">
                  <i class="fas fa-laptop-code"></i>
                  <p>Rekomendasi Magang</p>
                </a>
              </li>
               <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts3">
                  <i class="fas fa-laptop-code"></i>
                  <p>Pengajuan Magang</p>
                </a>
              </li>
               <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts4">
                  <i class="fas fa-laptop-code"></i>
                  <p>Monitoring dan Evaluasi Magang</p>
                </a>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts5">
                  <i class="fas fa-laptop-code"></i>
                  <p>Mahasiswa Bimbingan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="widgets.html">
                  <i class="fas fa-chart-bar"></i>
                  <p>Monitoring dan Statistik</p>
                  <span class="badge badge-success"></span>
                </a>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->