{{-- @extends('adminlte::auth.login') --}}

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('login_assets/css/bootstrap.css'); }}">
    <link rel="stylesheet" href="{{ asset('login_assets/css/custom.css'); }}">
    <link rel="stylesheet" href="{{ asset('login_assets/css/custom.min.css'); }}">

    <link rel="stylesheet" href="{{ asset('login_assets/css/animate.css'); }}">
<!--     <link rel="stylesheet" href="css/prism-okaidia.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
     -->
    <script src="{{ asset('login_assets/js/wow.min.js'); }}"></script>
    <script>
    new WOW().init();
    </script>

  </head>
   <body class="body_bg_image">

<div class="container">

<div class="row">
  <div class="col-lg-2"></div>
   <div class="col-lg-4">

      <div class="wow fadeInLeft" style="text-align: center;">
          <img src="{{ asset('login_assets/img/Army_Logo_my.png'); }}" width="200px" align="centere" >
      <p width="350px" align="centere"  style="color:rgb(255, 165, 0); font-size:35px; text-align: center; font-family: sans-serif; text-shadow: 1px 1px 2px black;"  >

        {{-- {{ config('app.name') }} --}}
        Authority Management System
      </p>
          </div>
     <br>
   </div>
  <div class="col-lg-4 align-self-center wow fadeInRight">

        <form method="post" action="{{ url('/login') }}" autocomplete="off">
            @csrf

          <div class="form-group">
              <label class="form-label mt-4 login_title">Login </label>
              <div class="form-floating mb-3">
                <input type="text" class="form-control @error('email') is-invalid @enderror" id="floatingInput"
                placeholder="User name" name="email" autocomplete="off"  value="{{ old('email') }}"
                pattern="^[a-zA-Z0-9_-]+$">
                <label for="floatingInput">User name</label>
                @error('email')
                <span class="error invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
              <div class="form-floating">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="floatingPassword"
                placeholder="Password"  name="password" pattern="^[^'"\\/]*$">
                <label for="floatingPassword">Password</label>
                @error('password')
                <span class="error invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
          </div>
          <div class="form-group">
             <br>
             <input  type="submit" class="btn btn-outline-info" value="Login">
             {{-- <span class="text_white"title="" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Please type your valid user informations to login to system." data-bs-original-title="Help box">
                 Help
              </span> --}}

          </div>

      </form>
  </div>

</div>

</div>
<div class="newstyle fixed-bottom">
    <div class="container">
        <div class="row">
          <div class="col-lg-2"></div>
          <div class="col-lg-8">
              <center><a href="#" class="footer_text">Software Solution by Dte of IT - SL Army</a></center>
          </div>
          <div class="col-lg-2 "><a href="#" class="footer_text"> Version 1.0.0 </a></div>
        </div>
    </div>

    </div>


</body>
 <script src="{{ asset('login_assets/js/jquery.min.js') }}"></script>
 <script src="{{ asset('login_assets/js/bootstrap.bundle.min.js'); }}"></script>
 <script src="{{ asset('login_assets/js/prism.js'); }}" data-manual></script>
 <script src="{{ asset('login_assets/js/custom.js'); }}"></script>

</html>

