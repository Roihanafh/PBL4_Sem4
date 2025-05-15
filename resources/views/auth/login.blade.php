<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="x-ua-compatible" content="ie=edge" />
  <title>MagangIn</title>
  <!-- MDB icon -->
  <link rel="icon" href="img/mdb-favicon.ico" type="image/x-icon" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" />
  <!-- Google Fonts Roboto -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
  <!-- MDB -->
  <link rel="stylesheet" href="assets/css/bootstrap-login-form.min.css" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
  <!-- Start your project here-->

  <style>
    .gradient-custom-2 {
      /* fallback for old browsers */
      background: #fccb90;

      /* Chrome 10-25, Safari 5.1-6 */
      background: -webkit-linear-gradient(to right, #6a00f4, #c400cc, #ff4fa2, #ff8a65);

      /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
      background: linear-gradient(to right, #6a00f4, #c400cc, #ff4fa2, #ff8a65);
    }

    @media (min-width: 768px) {
      .gradient-form {
        height: 100vh !important;
      }
    }
    @media (min-width: 769px) {
      .gradient-custom-2 {
        border-top-right-radius: .3rem;
        border-bottom-right-radius: .3rem;
      }
    }
  </style>
  <section class="h-100 gradient-form" style="background-color: #eee;">
    <div class="container py-5 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-xl-10">
          <div class="card rounded-3 text-black">
            <div class="row g-0">
              <div class="col-lg-6">
                <div class="card-body p-md-5 mx-md-4">

                  <div class="text-center">
                    <img src="img/MagangIn.png" style="width: 185px;" alt="logo">
                  </div>

                <form action="{{ route('login') }}" method="POST" id="form-login">
                  @csrf
                  <p>Silahkan LogIn terlebih dahulu</p>
                  <div class="form-outline mb-4">
                      <input type="text" id="username" name="username" class="form-control" placeholder="Username">
                      <label class="form-label" for="username">Username</label>
                  </div>

                  <div class="form-outline mb-4">
                      <input type="password" id="password" name="password" class="form-control" />
                      <label class="form-label" for="password">Password</label>
                  </div>

                  <div class="text-center pt-1 mb-5 pb-1">
                      <button class="btn btn-primary btn-block gradient-custom-2 mb-3" type="submit">Log in</button>
                  </div>
              </form>

                    <div class="d-flex align-items-center justify-content-center pb-4">
                      <p class="mb-0 me-2">Belum Memiliki Akun?</p>
                      <button type="button" class="btn btn-outline-danger">Buat Akun Baru</button>
                    </div>


                </div>
              </div>
              <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                  <h4 class="mb-4">MagangIn JTI Polinema</h4>
                  <p class="small mb-0">platform resmi yang dirancang untuk memfasilitasi mahasiswa Jurusan Teknologi Informasi Politeknik Negeri Malang 
                    dalam proses pengajuan, pencatatan, dan pemantauan kegiatan magang atau kerja praktik. Melalui sistem ini, mahasiswa dapat mencari lowongan 
                    magang yang tersedia, mengajukan lamaran, mencatat aktivitas harian, hingga berkomunikasi langsung dengan dosen pembimbing dan perusahaan. 
                    MagangIn hadir untuk mendukung keterhubungan antara dunia akademik dan industri secara lebih terstruktur dan efisien.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- End your project here-->

  <!-- MDB -->
  <script type="text/javascript" src="assets/js/mdb.min.js"></script>
  <!-- Custom scripts -->
  <script type="text/javascript"></script>
 
  <!-- jQuery (wajib ada sebelum jQuery Validation) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- jQuery Validation -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

  <script> 
  $.ajaxSetup({ 
    headers: { 
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
    } 
  }); 
 
  $(document).ready(function() { 
    $("#form-login").validate({ 
      rules: { 
        username: {required: true, minlength: 4, maxlength: 20}, 
        password: {required: true, minlength: 2, maxlength: 20} 
      }, 
      submitHandler: function(form) { // ketika valid, maka bagian yg akan dijalankan 
        $.ajax({ 
          url: form.action, 
          type: form.method, 
          data: $(form).serialize(), 
          success: function(response) { 
            if(response.status){ // jika sukses 
              Swal.fire({ 
                  icon: 'success', 
                  title: 'Berhasil', 
                  text: response.message, 
              }).then(function() { 
                  window.location = response.redirect; 
              }); 
            }else{ // jika error 
              $('.error-text').text(''); 
              $.each(response.msgField, function(prefix, val) { 
                  $('#error-'+prefix).text(val[0]); 
              }); 
              Swal.fire({ 
                  icon: 'error', 
                  title: 'Terjadi Kesalahan', 
                  text: response.message 
              }); 
            } 
          } 
        }); 
        return false; 
      }, 
      errorElement: 'span', 
      errorPlacement: function (error, element) { 
        error.addClass('invalid-feedback'); 
        element.closest('.input-group').append(error); 
      }, 
      highlight: function (element, errorClass, validClass) { 
        $(element).addClass('is-invalid'); 
      }, 
      unhighlight: function (element, errorClass, validClass) { 
        $(element).removeClass('is-invalid'); 
      } 
    }); 
  }); 
</script> 

</body>

</html>