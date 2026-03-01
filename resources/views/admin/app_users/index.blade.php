@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-multiple"></i>
                </span> App Users
            </h3>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Registered Mobile API Users</h4>
                        
                        <!-- Search and Filter Bar (DataTables Style) -->
                        <form action="{{ route('admin.app_users') }}" method="GET" class="mb-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center mb-2">
                                    <span>Show</span>
                                    <select name="per_page" class="form-select form-select-sm mx-2" style="width: 80px;" onchange="this.form.submit()">
                                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                    <span>entries</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2">Search:</span>
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search users..." value="{{ $search }}" style="width: 200px;">
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 80px;"> ID </th>
                                        <th> API User ID </th>
                                        <th> Username </th>
                                        <th style="width: 150px;"> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td> {{ $user->id }} </td>
                                            <td> {{ $user->api_user_id }} </td>
                                            <td> {{ $user->username ?? 'N/A' }} </td>
                                            <td>
                                                <a href="{{ route('admin.user_songs', $user->id) }}"
                                                    class="btn btn-gradient-primary btn-xs py-1 px-3">View Songs</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No users found matching your search.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Footer Info and Pagination -->
                        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="text-muted small mb-2">
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
                            </div>
                            <div class="pagination-container mb-2">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection