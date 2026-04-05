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
                        <form action="{{ route('admin.song_covers') }}" method="GET" class="mb-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center mb-2">
                                    <span>Show</span>
                                    <select name="per_page" class="form-select form-select-sm mx-2" style="width: 80px;"
                                        onchange="this.form.submit()">
                                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    </select>
                                    <span>entries</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2">Search:</span>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="Name or ID..." value="{{ $search }}" style="width: 200px;">
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 70px;"> ID </th>
                                        <th style="width: 100px;"> Image </th>
                                        <th> Voice ID </th>
                                        <th> Voice Name </th>
                                        <th style="width: 100px;"> TTS Only </th>
                                        <th style="width: 150px;"> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($covers as $cover)
                                        <tr>
                                            <td> {{ $cover->id }} </td>
                                            <td>
                                                <img src="{{ asset($cover->image) }}" alt="Image"
                                                    style="width:50px;height:50px;object-fit:cover;border-radius:10px;">
                                            </td>
                                            <td> {{ $cover->voice_id }} </td>
                                            <td> {{ $cover->voice_name }} </td>
                                            <td>
                                                <span class="badge {{ $cover->tts_only ? 'badge-info' : 'badge-secondary' }}">
                                                    {{ $cover->tts_only ? 'true' : 'false' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-gradient-info btn-xs py-1 px-3 edit-btn"
                                                    data-bs-toggle="modal" data-bs-target="#editVoiceModal"
                                                    data-id="{{ $cover->id }}" 
                                                    data-voice_id="{{ $cover->voice_id }}"
                                                    data-voice_name="{{ $cover->voice_name }}"
                                                    data-tts_only="{{ $cover->tts_only }}"
                                                    data-image="{{ asset($cover->image) }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.song_covers.destroy', $cover->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-gradient-danger btn-xs py-1 px-3">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No voices found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="text-muted small mb-2">
                                Showing {{ $covers->firstItem() ?? 0 }} to {{ $covers->lastItem() ?? 0 }} of
                                {{ $covers->total() }} entries
                            </div>
                            <div class="pagination-container mb-2">
                                {{ $covers->links() }}
                            </div>
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
            // Edit Button Handler
            const editButtons = document.querySelectorAll('.edit-btn');
            const editForm = document.getElementById('editVoiceForm');
            const editVoiceId = document.getElementById('edit_voice_id');
            const editVoiceName = document.getElementById('edit_voice_name');
            const editTtsOnly = document.getElementById('edit_tts_only');
            const currentImagePreview = document.getElementById('current_image_preview');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const voice_id = this.getAttribute('data-voice_id');
                    const voice_name = this.getAttribute('data-voice_name');
                    const tts_only = this.getAttribute('data-tts_only') == '1';
                    const image = this.getAttribute('data-image');

                    // Laravel's POST route for update in this case uses a POST to /{id} with potential method spoofing, 
                    // but I registered it as Route::post('/admin/song-covers/{id}', ...) in web.php to simplify file upload handling.
                    editForm.action = `/admin/song-covers/${id}`;
                    editVoiceId.value = voice_id;
                    editVoiceName.value = voice_name;
                    editTtsOnly.checked = tts_only;
                    currentImagePreview.src = image;
                });
            });

            // .webp validation & Image Preview
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
                            this.value = ''; // clear input
                            if (previewContainer) previewContainer.style.display = 'none';
                        } else {
                            warning.style.display = 'none';
                            
                            // Show preview
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
                    // Use Bootstrap's fade class logic or just hide it
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150); // Small delay for transition
                });
            }, 5000);

            // SweetAlert Delete Confirmation
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
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
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
