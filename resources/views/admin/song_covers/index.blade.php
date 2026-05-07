@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-star"></i>
                </span> Song Covers
            </h3>
            <button type="button" class="btn btn-gradient-primary btn-icon-text" data-bs-toggle="modal"
                data-bs-target="#addVoiceModal">
                <i class="mdi mdi-plus btn-icon-prepend"></i> Add Voice
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Manage Song Cover Voices</h4>

                        <!-- Search Bar -->
                        <form id="songCoversFilterForm" action="{{ route('admin.song_covers') }}" method="GET" class="mb-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center mb-2">
                                    <span>Show</span>
                                    <select name="per_page" class="form-select form-select-sm mx-2" style="width: 80px;">
                                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    </select>
                                    <span>entries</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2">Search:</span>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="Name or ID..." value="{{ $search }}" style="width: 200px;"
                                        autocomplete="off">
                                </div>
                            </div>
                        </form>

                        <div id="songCoversContent" style="position: relative; transition: opacity .15s ease;">
                            @include('admin.song_covers._table', ['covers' => $covers])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Voice Modal -->
    <div class="modal fade" id="addVoiceModal" tabindex="-1" aria-labelledby="addVoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.song_covers.store') }}" method="POST" enctype="multipart/form-data" id="addVoiceForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addVoiceModalLabel">Add New Voice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add_voice_id" class="form-label">Voice ID</label>
                            <input type="text" name="voice_id" id="add_voice_id" class="form-control" required
                                placeholder="e.g. BrunoMars or UUID">
                        </div>
                        <div class="mb-3">
                            <label for="add_voice_name" class="form-label">Voice Name</label>
                            <input type="text" name="voice_name" id="add_voice_name" class="form-control" required
                                placeholder="e.g. Bruno Mars">
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <div class="me-3" id="add_image_preview_container" style="display: none;">
                                <label class="form-label d-block">Preview</label>
                                <img src="" id="add_image_preview" alt="Preview"
                                    style="width:60px;height:60px;object-fit:cover;border-radius:10px;border:1px solid #ddd;">
                            </div>
                            <div class="flex-grow-1">
                                <label for="add_image" class="form-label">Image (Only .webp allowed)</label>
                                <input type="file" name="image" id="add_image" class="form-control" accept=".webp" required>
                                <small class="text-danger webp-warning" style="display: none;">Only .webp images are allowed!</small>
                            </div>
                        </div>
                        <div class="form-check form-check-flat form-check-primary mb-3">
                            <label class="form-check-label text-muted">
                                <input type="checkbox" name="tts_only" class="form-check-input" disabled> TTS Only (Disabled)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-gradient-primary">Save Voice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Voice Modal -->
    <div class="modal fade" id="editVoiceModal" tabindex="-1" aria-labelledby="editVoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" enctype="multipart/form-data" id="editVoiceForm">
                    @csrf
                    <!-- Using POST with a specific route built in JS since we need the ID -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editVoiceModalLabel">Edit Voice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_voice_id" class="form-label">Voice ID</label>
                            <input type="text" name="voice_id" id="edit_voice_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_voice_name" class="form-label">Voice Name</label>
                            <input type="text" name="voice_name" id="edit_voice_name" class="form-control" required>
                        </div>
                        <div class="mb-4 d-flex align-items-center">
                            <div class="me-3">
                                <label class="form-label d-block">Current Image</label>
                                <img src="" id="current_image_preview" alt="Preview"
                                    style="width:60px;height:60px;object-fit:cover;border-radius:10px;border:1px solid #ddd;">
                            </div>
                            <div>
                                <label for="edit_image" class="form-label">Change Image (.webp only)</label>
                                <input type="file" name="image" id="edit_image" class="form-control" accept=".webp">
                                <small class="text-danger webp-warning" style="display: none;">Only .webp images are allowed!</small>
                            </div>
                        </div>
                        <div class="form-check form-check-flat form-check-primary mb-3">
                            <label class="form-check-label text-muted">
                                <input type="checkbox" name="tts_only" id="edit_tts_only" class="form-check-input" disabled> TTS Only (Disabled)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-gradient-primary">Update Voice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editForm = document.getElementById('editVoiceForm');
            const editVoiceId = document.getElementById('edit_voice_id');
            const editVoiceName = document.getElementById('edit_voice_name');
            const editTtsOnly = document.getElementById('edit_tts_only');
            const currentImagePreview = document.getElementById('current_image_preview');
            const content = document.getElementById('songCoversContent');

            // Delegated Edit Button Handler — survives AJAX content swaps
            content.addEventListener('click', function(e) {
                const button = e.target.closest('.edit-btn');
                if (!button) return;
                const id = button.getAttribute('data-id');
                editForm.action = `/admin/song-covers/${id}`;
                editVoiceId.value = button.getAttribute('data-voice_id');
                editVoiceName.value = button.getAttribute('data-voice_name');
                editTtsOnly.checked = button.getAttribute('data-tts_only') == '1';
                currentImagePreview.src = button.getAttribute('data-image');
            });

            // Delegated Delete Confirmation — survives AJAX content swaps
            content.addEventListener('submit', function(e) {
                const form = e.target.closest('.delete-form');
                if (!form) return;
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This voice and its image will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#fe7096',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // .webp validation & Image Preview (modal inputs only — not AJAX-swapped)
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const warning = this.nextElementSibling;
                    const file = this.files[0];
                    const isAdd = this.id === 'add_image';
                    const previewImg = isAdd ? document.getElementById('add_image_preview') : document.getElementById('current_image_preview');
                    const previewContainer = isAdd ? document.getElementById('add_image_preview_container') : null;

                    if (file) {
                        const extension = file.name.split('.').pop().toLowerCase();
                        if (extension !== 'webp') {
                            warning.style.display = 'block';
                            this.value = '';
                            if (previewContainer) previewContainer.style.display = 'none';
                        } else {
                            warning.style.display = 'none';
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImg.src = e.target.result;
                                if (previewContainer) previewContainer.style.display = 'block';
                            }
                            reader.readAsDataURL(file);
                        }
                    } else {
                        if (previewContainer) previewContainer.style.display = 'none';
                    }
                });
            });

            // Auto-hide alert messages after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                });
            }, 5000);

            // ----- AJAX search / per-page / pagination -----
            const baseUrl = @json(route('admin.song_covers'));
            const form = document.getElementById('songCoversFilterForm');
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

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                loadContent(buildUrl());
            });
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => loadContent(buildUrl({ page: 1 })), 300);
            });
            perPageSelect.addEventListener('change', function() {
                loadContent(buildUrl({ page: 1 }));
            });
            content.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination a');
                if (!link) return;
                e.preventDefault();
                if (link.href) loadContent(link.href);
            });
        });
    </script>
@endsection
