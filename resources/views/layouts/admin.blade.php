<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>NGD Admin</title>
  <link rel="stylesheet" href="{{ asset('admin_assets/assets') }}/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="{{ asset('admin_assets/assets') }}/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="{{ asset('admin_assets/assets') }}/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="{{ asset('admin_assets/assets') }}/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset('admin_assets/assets') }}/vendors/font-awesome/css/font-awesome.min.css" />
  <link rel="stylesheet"
    href="{{ asset('admin_assets/assets') }}/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="{{ asset('admin_assets/assets') }}/css/style.css">
  <link rel="shortcut icon" href="{{ asset('admin_assets/assets') }}/images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo" href="index.html"><img
            src="{{ asset('admin_assets/assets') }}/images/logo.svg" alt="logo" /></a>
        <a class="navbar-brand brand-logo-mini" href="index.html"><img
            src="{{ asset('admin_assets/assets') }}/images/logo-mini.svg" alt="logo" /></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="mdi mdi-menu"></span>
        </button>

        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown"
              aria-expanded="false">
              <div class="nav-profile-img">
                <img src="{{ asset('admin_assets/assets') }}/images/faces/face1.jpg" alt="image">
                <span class="availability-status online"></span>
              </div>
              <p class="mb-1 text-black">NGD Admin</p>
            </a>
            <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="#">
                <i class="mdi mdi-cached me-2 text-success"></i> Activity Log </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">
                <i class="mdi mdi-logout me-2 text-primary"></i> Signout </a>
            </div>
          </li>


        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
          data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>
    <div class="container-fluid page-body-wrapper">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
              <div class="nav-profile-image">
                <img src="{{ asset('admin_assets/assets') }}/images/faces/face1.jpg" alt="profile" />
                <span class="login-status online"></span>
              </div>
              <div class="nav-profile-text d-flex flex-column">
                <span class="font-weight-bold mb-2">NGD Admin</span>
                <span class="text-secondary text-small">NGD Team</span>
              </div>
              <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
              <span class="menu-title">Dashboard</span>
              <i class="mdi mdi-home menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.app_users') }}">
              <span class="menu-title">App Users</span>
              <i class="mdi mdi-account-multiple menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.songs') }}">
              <span class="menu-title">Songs</span>
              <i class="mdi mdi-music menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.api_list') }}">
              <span class="menu-title">API List</span>
              <i class="mdi mdi-api menu-icon"></i>
            </a>
          </li>

        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
        @yield('content')
        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2023 <a href="#"
                target="_blank">NGD</a>. All rights reserved.</span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <script src="{{ asset('admin_assets/assets') }}/vendors/js/vendor.bundle.base.js"></script>
  <script src="{{ asset('admin_assets/assets') }}/vendors/chart.js/chart.umd.js"></script>
  <script src="{{ asset('admin_assets/assets') }}/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <script src="{{ asset('admin_assets/assets') }}/js/off-canvas.js"></script>
  <script src="{{ asset('admin_assets/assets') }}/js/misc.js"></script>
  <script src="{{ asset('admin_assets/assets') }}/js/settings.js"></script>
  <script src="{{ asset('admin_assets/assets') }}/js/todolist.js"></script>
  <script src="{{ asset('admin_assets/assets') }}/js/jquery.cookie.js"></script>
  <script src="{{ asset('admin_assets/assets') }}/js/dashboard.js"></script>
</body>

</html>