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

        <style>
            .stat-card {
                border-radius: 12px;
                height: 180px;
                border: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                transition: transform .2s ease, box-shadow .2s ease;
            }
            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            }
            .stat-card .card-body {
                position: relative;
                overflow: hidden;
                z-index: 1;
            }
            .stat-card .deco-circle {
                position: absolute;
                background: rgba(255, 255, 255, 0.12);
                border-radius: 50%;
                pointer-events: none;
                z-index: 0;
            }
            .stat-card .stat-content {
                position: relative;
                z-index: 2;
            }
            .stat-card .view-all-link {
                color: #fff;
                text-decoration: none;
                font-size: .9rem;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                position: relative;
                z-index: 3;
            }
            .stat-card .view-all-link:hover {
                color: #fff;
                text-decoration: underline;
            }
            .recent-card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            }
            .recent-card .card-header {
                background: #fff;
                border-bottom: 1px solid #f0f0f0;
                padding: 1rem 1.25rem;
                border-radius: 12px 12px 0 0;
            }
            .recent-list-item {
                padding: .75rem 1.25rem;
                border-bottom: 1px solid #f4f4f4;
            }
            .recent-list-item:last-child {
                border-bottom: none;
            }
            .recent-thumb {
                width: 42px;
                height: 42px;
                min-width: 42px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #e9ecef;
            }
            .recent-thumb-placeholder {
                width: 42px;
                height: 42px;
                min-width: 42px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 18px;
            }
        </style>

        <!-- Stat Cards -->
        <div class="row">
            <!-- Total Users Card -->
            <div class="col-lg-4 col-md-6 col-sm-12 grid-margin stretch-card">
                <div class="card stat-card text-white"
                    style="background: linear-gradient(135deg, #ffbf96, #fe7096);">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="deco-circle" style="width: 150px; height: 150px; top: -30px; right: -40px;"></div>
                        <div class="deco-circle" style="width: 110px; height: 110px; bottom: -40px; left: -20px;"></div>

                        <div class="stat-content d-flex flex-column h-100 justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="font-weight-normal mb-0" style="font-size: 1.05rem; opacity: .95;">Total Users</h5>
                                <i class="mdi mdi-account-multiple mdi-24px" style="opacity: .85;"></i>
                            </div>
                            <h1 class="mb-0" style="font-size: 2.6rem; font-weight: 600;">{{ $totalUsers }}</h1>
                            <a href="{{ route('admin.app_users') }}" class="view-all-link">
                                View All <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Songs Card -->
            <div class="col-lg-4 col-md-6 col-sm-12 grid-margin stretch-card">
                <div class="card stat-card text-white"
                    style="background: linear-gradient(135deg, #84d9d2, #07cdae);">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="deco-circle" style="width: 150px; height: 150px; top: -30px; right: -40px;"></div>
                        <div class="deco-circle" style="width: 110px; height: 110px; bottom: -40px; left: -20px;"></div>

                        <div class="stat-content d-flex flex-column h-100 justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="font-weight-normal mb-0" style="font-size: 1.05rem; opacity: .95;">Total Songs</h5>
                                <i class="mdi mdi-music-note mdi-24px" style="opacity: .85;"></i>
                            </div>
                            <h1 class="mb-0" style="font-size: 2.6rem; font-weight: 600;">{{ $totalSongs }}</h1>
                            <a href="{{ route('admin.songs') }}" class="view-all-link">
                                View All <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Song Covers Card -->
            <div class="col-lg-4 col-md-6 col-sm-12 grid-margin stretch-card">
                <div class="card stat-card text-white"
                    style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="deco-circle" style="width: 150px; height: 150px; top: -30px; right: -40px;"></div>
                        <div class="deco-circle" style="width: 110px; height: 110px; bottom: -40px; left: -20px;"></div>

                        <div class="stat-content d-flex flex-column h-100 justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="font-weight-normal mb-0" style="font-size: 1.05rem; opacity: .95;">Total Song Covers</h5>
                                <i class="mdi mdi-account-star mdi-24px" style="opacity: .85;"></i>
                            </div>
                            <h1 class="mb-0" style="font-size: 2.6rem; font-weight: 600;">{{ $totalCovers }}</h1>
                            <a href="{{ route('admin.song_covers') }}" class="view-all-link">
                                View All <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="row">
            <!-- Recent Users -->
            <div class="col-lg-6 col-md-12 grid-margin stretch-card">
                <div class="card recent-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="mdi mdi-account-multiple text-primary me-2"></i>Recent Users
                        </h5>
                        <a href="{{ route('admin.app_users') }}" class="btn btn-sm btn-gradient-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @php $defaultAvatar = asset('admin_assets/assets/images/default-avatar.svg'); @endphp
                        @forelse ($recentUsers as $user)
                            <div class="recent-list-item d-flex align-items-center">
                                @php
                                    $avatarSrc = $user->user_profile ? asset($user->user_profile) : $defaultAvatar;
                                @endphp
                                <img src="{{ $avatarSrc }}"
                                     alt="Profile"
                                     class="recent-thumb me-3"
                                     onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';"
                                     style="background:#f8f9fa;">

                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-0 text-truncate" style="font-size: .9rem;">
                                        {{ $user->username ?? 'N/A' }}
                                    </h6>
                                    <small class="text-muted text-truncate d-block" style="font-size: .75rem;">
                                        ID: {{ $user->api_user_id }}
                                    </small>
                                </div>
                                <a href="{{ route('admin.user_songs', $user->id) }}"
                                    class="btn btn-outline-primary btn-xs py-1 px-2" style="font-size: .7rem;">
                                    Songs
                                </a>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="mdi mdi-account-off mdi-24px d-block mb-2"></i>
                                No users yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Songs -->
            <div class="col-lg-6 col-md-12 grid-margin stretch-card">
                <div class="card recent-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="mdi mdi-music text-success me-2"></i>Recent Songs
                        </h5>
                        <a href="{{ route('admin.songs') }}" class="btn btn-sm btn-gradient-success">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @forelse ($recentSongs as $song)
                            <div class="recent-list-item d-flex align-items-center">
                                <span class="recent-thumb-placeholder bg-gradient-success me-3">
                                    <i class="mdi mdi-music-note"></i>
                                </span>
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-0 text-truncate" style="font-size: .9rem;">
                                        {{ $song->title ?? 'Untitled Song' }}
                                    </h6>
                                    <small class="text-muted text-truncate d-block" style="font-size: .75rem;">
                                        By: {{ $song->appUser->username ?? $song->appUser->api_user_id ?? 'Unknown' }}
                                        &middot; {{ $song->created_at->format('M d, Y') }}
                                    </small>
                                </div>
                                @if ($song->song_url)
                                    @if (filter_var($song->song_url, FILTER_VALIDATE_URL))
                                        <a href="{{ $song->song_url }}" target="_blank"
                                            class="btn btn-outline-success btn-xs py-1 px-2" style="font-size: .7rem;">
                                            <i class="mdi mdi-play"></i> Listen
                                        </a>
                                    @else
                                        <a href="{{ asset('upload/' . ($song->appUser->api_user_id ?? 'default') . '/' . $song->song_url) }}"
                                            target="_blank" class="btn btn-outline-success btn-xs py-1 px-2"
                                            style="font-size: .7rem;">
                                            <i class="mdi mdi-play"></i> Listen
                                        </a>
                                    @endif
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="mdi mdi-music-off mdi-24px d-block mb-2"></i>
                                No songs yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
