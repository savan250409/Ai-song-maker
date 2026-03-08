@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-api"></i>
                </span> API List
            </h3>
        </div>
        <div class="row">
            <!-- 1. Save User API -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title font-weight-bold">1. Save User</h4>
                        <p class="mb-4">
                            <span class="font-weight-medium">Method:</span>
                            <span class="badge badge-success px-3 py-2">POST</span>
                        </p>

                        <div class="bg-light p-3 rounded mb-4">
                            <span class="font-weight-medium">URL:</span><br>
                            <span class="text-danger">{{ url('/') }}/api/save-user</span>
                        </div>

                        <h5 class="font-weight-medium mb-3">Headers:</h5>
                        <p class="mb-4 text-muted">No authorization header required</p>

                        <h5 class="font-weight-medium mb-3">Description:</h5>
                        <p class="text-muted mb-3">
                            Creates a new App User. Accepts the parameters below.
                        </p>

                        <h5 class="font-weight-medium mb-2">Parameters:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>user_id</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-danger">Yes</span></td>
                                        <td>API User ID. If the user doesn't exist, a new one is created.</td>
                                    </tr>
                                    <tr>
                                        <td><code>email</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-secondary">No</span></td>
                                        <td>Email address of the user</td>
                                    </tr>
                                    <tr>
                                        <td><code>username</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-secondary">No</span></td>
                                        <td>Display name of the user</td>
                                    </tr>
                                    <tr>
                                        <td><code>password</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-secondary">No</span></td>
                                        <td>User's password (stored as bcrypt hash)</td>
                                    </tr>
                                    <tr>
                                        <td><code>user_profile</code></td>
                                        <td>string (Base64)</td>
                                        <td><span class="badge badge-secondary">No</span></td>
                                        <td>Profile image encoded as a Base64 string.
                                            The image is decoded and saved to
                                            <code>upload/profiles/{user_id}/</code>.
                                            <strong>Only the filename is stored in the database.</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Save Song API -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title font-weight-bold">2. Save Song</h4>
                        <p class="mb-4">
                            <span class="font-weight-medium">Method:</span>
                            <span class="badge badge-success px-3 py-2">POST</span>
                        </p>

                        <div class="bg-light p-3 rounded mb-4">
                            <span class="font-weight-medium">URL:</span><br>
                            <span class="text-danger">{{ url('/') }}/api/save-song</span>
                        </div>

                        <h5 class="font-weight-medium mb-3">Headers:</h5>
                        <p class="mb-4 text-muted">No authorization header required</p>

                        <h5 class="font-weight-medium mb-3">Description:</h5>
                        <p class="text-muted mb-4">
                            Saves a song directly linked to an API User. Accepts an absolute <code>song_url</code>,
                            automatically downloads the file to <code>public/upload/{user_id}/</code>, and stores local data
                            along with <code>genre</code>, <code>mood</code>, <code>lyrics</code>, and <code>title</code>.
                        </p>

                        <h5 class="font-weight-medium mb-2">Parameters:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>user_id</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-danger">Yes</span></td>
                                        <td>API User ID</td>
                                    </tr>
                                    <tr>
                                        <td><code>email</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-secondary">No</span></td>
                                        <td>Email address for new user creation (if applicable)</td>
                                    </tr>
                                    <tr>
                                        <td><code>song_url</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-secondary">No</span></td>
                                        <td>Absolute URL of the song file</td>
                                    </tr>
                                    <tr>
                                        <td><code>title</code>/<code>genre</code>/<code>mood</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-secondary">No</span></td>
                                        <td>Song metadata fields</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 3. Get Songs By Filter API -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title font-weight-bold">3. Get Songs By Filter</h4>
                        <p class="mb-4">
                            <span class="font-weight-medium">Method:</span>
                            <span class="badge badge-success px-3 py-2">POST</span>
                        </p>

                        <div class="bg-light p-3 rounded mb-4">
                            <span class="font-weight-medium">URL:</span><br>
                            <span class="text-danger">{{ url('/') }}/api/get-songs-by-filter</span>
                        </div>

                        <h5 class="font-weight-medium mb-3">Headers:</h5>
                        <p class="mb-4 text-muted">No authorization header required</p>

                        <h5 class="font-weight-medium mb-2">Parameters (all optional):</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>user_id</code></td>
                                        <td>string</td>
                                        <td>Filter songs by this API user ID</td>
                                    </tr>
                                    <tr>
                                        <td><code>genre</code></td>
                                        <td>string</td>
                                        <td>Filter songs by genre (e.g. Pop, Rock)</td>
                                    </tr>
                                    <tr>
                                        <td><code>mood</code></td>
                                        <td>string</td>
                                        <td>Filter songs by mood (e.g. Happy, Sad)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted mt-3 mb-0"><small>All filters are combinable. If none are passed, all songs are
                                returned.</small></p>
                    </div>
                </div>
            </div>

            <!-- 4. Get Random Songs API -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title font-weight-bold">4. Get Random Songs</h4>
                        <p class="mb-4">
                            <span class="font-weight-medium">Method:</span>
                            <span class="badge badge-primary px-3 py-2">GET</span>
                        </p>

                        <div class="bg-light p-3 rounded mb-4">
                            <span class="font-weight-medium">URL:</span><br>
                            <span class="text-danger">{{ url('/') }}/api/get-random-songs</span>
                        </div>

                        <h5 class="font-weight-medium mb-3">Headers:</h5>
                        <p class="mb-4 text-muted">No authorization header required</p>

                        <h5 class="font-weight-medium mb-3">Description:</h5>
                        <p class="text-muted mb-0">
                            Returns up to <strong>50 randomly selected songs</strong> with their full
                            <code>song_url</code> (direct playable link), <code>title</code>,
                            <code>genre</code>, and <code>mood</code>. No body parameters needed.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 5. Get All Moods API -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title font-weight-bold">5. Get All Moods</h4>
                        <p class="mb-4">
                            <span class="font-weight-medium">Method:</span>
                            <span class="badge badge-primary px-3 py-2">GET</span>
                        </p>

                        <div class="bg-light p-3 rounded mb-4">
                            <span class="font-weight-medium">URL:</span><br>
                            <span class="text-danger">{{ url('/') }}/api/get-moods</span>
                        </div>

                        <h5 class="font-weight-medium mb-3">Headers:</h5>
                        <p class="mb-4 text-muted">No authorization header required</p>

                        <h5 class="font-weight-medium mb-3">Description:</h5>
                        <p class="text-muted mb-0">
                            Returns all <strong>distinct mood names</strong> available in the system,
                            each with a sequential <code>id</code> and <code>name</code>.
                            No body parameters needed.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 6. Get All Genres API -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title font-weight-bold">6. Get All Genres</h4>
                        <p class="mb-4">
                            <span class="font-weight-medium">Method:</span>
                            <span class="badge badge-primary px-3 py-2">GET</span>
                        </p>

                        <div class="bg-light p-3 rounded mb-4">
                            <span class="font-weight-medium">URL:</span><br>
                            <span class="text-danger">{{ url('/') }}/api/get-genres</span>
                        </div>

                        <h5 class="font-weight-medium mb-3">Headers:</h5>
                        <p class="mb-4 text-muted">No authorization header required</p>

                        <h5 class="font-weight-medium mb-3">Description:</h5>
                        <p class="text-muted mb-0">
                            Returns all <strong>distinct genre names</strong> available in the system,
                            each with a sequential <code>id</code> and <code>name</code>.
                            No body parameters needed.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 7. Edit Profile API -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title font-weight-bold">7. Edit Profile</h4>
                        <p class="mb-4">
                            <span class="font-weight-medium">Method:</span>
                            <span class="badge badge-success px-3 py-2">POST</span>
                        </p>

                        <div class="bg-light p-3 rounded mb-4">
                            <span class="font-weight-medium">URL:</span><br>
                            <span class="text-danger">{{ url('/') }}/api/edit-profile</span>
                        </div>

                        <h5 class="font-weight-medium mb-3">Headers:</h5>
                        <p class="mb-4 text-muted">No authorization header required</p>

                        <h5 class="font-weight-medium mb-2">Parameters:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>email</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-primary">Yes</span></td>
                                        <td>The User's Email Address (used as identifier)</td>
                                    </tr>
                                    <tr>
                                        <td><code>username</code></td>
                                        <td>string</td>
                                        <td><span class="badge badge-secondary">No</span></td>
                                        <td>New username to update</td>
                                    </tr>
                                    <tr>
                                        <td><code>user_profile</code></td>
                                        <td>string (Base64)</td>
                                        <td><span class="badge badge-secondary">No</span></td>
                                        <td>New profile image as Base64 string.
                                            <strong>Only the filename is stored in the database.</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted mt-3 mb-0"><small>Updates only the provided fields. The <code>email</code> is
                                used to find the existing user record.</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection