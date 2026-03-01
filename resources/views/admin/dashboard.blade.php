@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="page-header mb-4">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Dashboard
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="row">
            <!-- Total Users Card -->
            <div class="col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-img-holder text-white"
                    style="background: linear-gradient(to right, #ffbf96, #fe7096); border-radius: 10px; height: 200px; border: none;">
                    <div class="card-body p-4 d-flex flex-column justify-content-between position-relative overflow-hidden">
                        <!-- Decorative Circles -->
                        <div
                            style="position: absolute; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -20px; right: -40px;">
                        </div>
                        <div
                            style="position: absolute; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; bottom: -40px; left: -20px;">
                        </div>

                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="font-weight-normal mb-0" style="font-size: 1.1rem; opacity: 0.9;">Total Users</h5>
                            <i class="mdi mdi-account-multiple mdi-24px opacity-7"></i>
                        </div>

                        <h1 class="mb-0" style="font-size: 3rem; font-weight: 500;">{{ $totalUsers }}</h1>

                        <a href="{{ route('admin.app_users') }}" class="text-white text-decoration-none"
                            style="font-size: 0.9rem; font-weight: 500;">
                            View All <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Songs Card -->
            <div class="col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-img-holder text-white"
                    style="background: linear-gradient(to right, #84d9d2, #07cdae); border-radius: 10px; height: 200px; border: none;">
                    <div class="card-body p-4 d-flex flex-column justify-content-between position-relative overflow-hidden">
                        <!-- Decorative Circles -->
                        <div
                            style="position: absolute; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -20px; right: -40px;">
                        </div>
                        <div
                            style="position: absolute; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; bottom: -40px; left: -20px;">
                        </div>

                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="font-weight-normal mb-0" style="font-size: 1.1rem; opacity: 0.9;">Total Songs</h5>
                            <i class="mdi mdi-music-note mdi-24px opacity-7"></i>
                        </div>

                        <h1 class="mb-0" style="font-size: 3rem; font-weight: 500;">{{ $totalSongs }}</h1>

                        <a href="{{ route('admin.songs') }}" class="text-white text-decoration-none"
                            style="font-size: 0.9rem; font-weight: 500;">
                            View All <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection