@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <a href="{{ route('admin.app_users') }}" class="btn btn-gradient-primary btn-sm me-3">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-music"></i>
                </span> User Songs: {{ $user->username ?? $user->api_user_id }}
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.app_users') }}">App Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User Songs</li>
                </ul>
            </nav>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">User Details</h4>
                        <p class="card-description"> User ID: <code>{{ $user->api_user_id }}</code> | Username:
                            <code>{{ $user->username ?? 'N/A' }}</code> </p>
                    </div>
                </div>
            </div>
        </div>

        @if($songs->isEmpty())
            <div class="row">
                <div class="col-12 grid-margin">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="mdi mdi-music-off mdi-48px text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">No songs found for this user.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @foreach ($songs as $song)
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-gradient-primary rounded-circle p-2 text-white me-2">
                                        <i class="mdi mdi-music-note mdi-18px"></i>
                                    </div>
                                    <h5 class="card-title mb-0 text-primary">{{ $song->title ?? 'Untitled Song' }}</h5>
                                </div>
                                <div class="mb-2">
                                    <span class="badge badge-gradient-info badge-sm">{{ $song->genre ?? 'General' }}</span>
                                    <span class="badge badge-gradient-success badge-sm">{{ $song->mood ?? 'Happy' }}</span>
                                </div>
                                <div class="bg-light p-2 rounded mb-2 border-left border-primary border-3">
                                    <h6 class="font-weight-bold small text-uppercase text-primary mb-1" style="font-size: 0.7rem;">Full Lyrics:</h6>
                                    <p class="text-dark small mb-0" style="white-space: pre-line; line-height: 1.4; font-size: 0.8rem;">
                                        {{ $song->lyrics ?? 'No lyrics provided.' }}</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <small class="text-muted"><i class="mdi mdi-calendar-check"></i>
                                        {{ $song->created_at->format('M d, Y') }}</small>
                                    @if ($song->song_url)
                                        @if (filter_var($song->song_url, FILTER_VALIDATE_URL))
                                            <a href="{{ $song->song_url }}" target="_blank" class="btn btn-gradient-primary btn-sm px-4">
                                                <i class="mdi mdi-headphones me-1"></i> Listen Now
                                            </a>
                                        @else
                                            <a href="{{ asset('upload/' . $user->api_user_id . '/' . $song->song_url) }}" target="_blank"
                                                class="btn btn-gradient-primary btn-sm px-4">
                                                <i class="mdi mdi-headphones me-1"></i> Listen Now
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection