@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-music"></i>
                </span> Generated Songs
            </h3>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body py-3">
                        <form action="{{ route('admin.songs') }}" method="GET">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center mb-0">
                                    <span class="small">Show</span>
                                    <select name="per_page" class="form-select form-select-sm mx-2" style="width: 80px;"
                                        onchange="this.form.submit()">
                                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                    <span class="small">entries</span>
                                </div>
                                <div class="d-flex align-items-center mb-0">
                                    <span class="me-2 small">Search:</span>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="Search songs..." value="{{ $search }}" style="width: 200px;">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if($songs->isEmpty())
            <div class="row">
                <div class="col-12 grid-margin">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="mdi mdi-magnify-close mdi-48px text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">No songs found matching your search.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @foreach ($songs as $song)
                    <div class="col-lg-3 col-md-4 col-sm-6 grid-margin stretch-card">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body p-3 d-flex flex-column h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-gradient-primary rounded-circle p-2 text-white me-2"
                                        style="width: 35px; height: 35px; min-width: 35px; display: flex; align-items: center; justify-content: center;">
                                        <i class="mdi mdi-music-note mdi-18px"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="card-title mb-0 text-truncate" title="{{ $song->title ?? 'Untitled Song' }}"
                                            style="font-size: 0.9rem;">{{ $song->title ?? 'Untitled Song' }}</h6>
                                        <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">By:
                                            {{ $song->appUser->username ?? $song->appUser->api_user_id ?? 'Unknown' }}</small>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <span class="badge badge-gradient-info py-1 px-2"
                                        style="font-size: 0.65rem;">{{ $song->genre ?? 'General' }}</span>
                                    <span class="badge badge-gradient-success py-1 px-2"
                                        style="font-size: 0.65rem;">{{ $song->mood ?? 'Happy' }}</span>
                                </div>

                                <div class="bg-light p-2 rounded mb-auto" style="height: 45px; overflow: hidden;">
                                    <p class="text-muted mb-0" style="font-size: 0.75rem; line-height: 1.2;">
                                        {{ Str::limit($song->lyrics, 60) ?? 'No lyrics.' }}
                                    </p>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                    <small class="text-muted" style="font-size: 0.7rem;"><i class="mdi mdi-calendar mdi-12px"></i>
                                        {{ $song->created_at->format('M d, y') }}</small>
                                    @if ($song->song_url)
                                        @if (filter_var($song->song_url, FILTER_VALIDATE_URL))
                                            <a href="{{ $song->song_url }}" target="_blank"
                                                class="btn btn-gradient-primary btn-xs py-1 px-2" style="font-size: 0.7rem;">
                                                <i class="mdi mdi-play"></i> Listen
                                            </a>
                                        @else
                                            <a href="{{ asset('upload/' . ($song->appUser->api_user_id ?? 'default') . '/' . $song->song_url) }}"
                                                target="_blank" class="btn btn-gradient-primary btn-xs py-1 px-2"
                                                style="font-size: 0.7rem;">
                                                <i class="mdi mdi-play"></i> Listen
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted small mb-2">
                        Showing {{ $songs->firstItem() ?? 0 }} to {{ $songs->lastItem() ?? 0 }} of {{ $songs->total() }} entries
                    </div>
                    <div class="pagination-container mb-2">
                        {{ $songs->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection