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
                        <form id="songsFilterForm" action="{{ route('admin.songs') }}" method="GET">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center mb-0">
                                    <span class="small">Show</span>
                                    <select name="per_page" class="form-select form-select-sm mx-2" style="width: 80px;">
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
                                        placeholder="Search songs..." value="{{ $search }}" style="width: 200px;"
                                        autocomplete="off">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="songsContent" style="position: relative; transition: opacity .15s ease;">
            @include('admin.songs._grid', ['songs' => $songs])
        </div>
    </div>

    <script>
        (function () {
            const baseUrl = @json(route('admin.songs'));
            const form = document.getElementById('songsFilterForm');
            const content = document.getElementById('songsContent');
            const searchInput = form.querySelector('input[name="search"]');
            const perPageSelect = form.querySelector('select[name="per_page"]');
            let debounceTimer;
            let activeRequest;

            function buildUrl(overrides = {}) {
                const url = new URL(baseUrl, window.location.origin);
                const search = overrides.search ?? searchInput.value;
                const perPage = overrides.per_page ?? perPageSelect.value;
                const page = overrides.page ?? 1;
                if (search) url.searchParams.set('search', search);
                if (perPage) url.searchParams.set('per_page', perPage);
                if (page && page > 1) url.searchParams.set('page', page);
                return url.toString();
            }

            function loadContent(url) {
                if (activeRequest) activeRequest.abort();
                const controller = new AbortController();
                activeRequest = controller;
                content.style.opacity = '0.5';
                fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                        signal: controller.signal
                    })
                    .then(r => r.text())
                    .then(html => {
                        content.innerHTML = html;
                        content.style.opacity = '1';
                    })
                    .catch(err => {
                        if (err.name !== 'AbortError') content.style.opacity = '1';
                    });
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                loadContent(buildUrl());
            });

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => loadContent(buildUrl({ page: 1 })), 300);
            });

            perPageSelect.addEventListener('change', function () {
                loadContent(buildUrl({ page: 1 }));
            });

            content.addEventListener('click', function (e) {
                const link = e.target.closest('.pagination a');
                if (!link) return;
                e.preventDefault();
                if (link.href) loadContent(link.href);
            });
        })();
    </script>
@endsection
