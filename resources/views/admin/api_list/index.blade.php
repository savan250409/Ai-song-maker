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
                        <p class="text-muted">
                            Creates or updates an App User. Accepts <code>user_id</code> (required), <code>username</code>,
                            and <code>password</code> parameters.
                        </p>
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
                        <p class="text-muted">
                            Saves a song directly linked to an API User. Accepts an absolute <code>song_url</code>,
                            automatically downloads the file to <code>public/upload/{user_id}/</code>, and stores local data
                            along with <code>genre</code>, <code>mood</code>, <code>lyrics</code>, and <code>title</code>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection