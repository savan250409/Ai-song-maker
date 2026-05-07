@php
    $defaultAvatar = asset('admin_assets/assets/images/default-avatar.svg');
@endphp

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="bg-light">
            <tr>
                <th style="width: 80px;"> ID </th>
                <th style="width: 90px;"> Profile </th>
                <th> API User ID </th>
                <th> Username </th>
                <th style="width: 150px;"> Action </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td> {{ $user->id }} </td>
                    <td>
                        @php
                            $avatarSrc = $user->user_profile ? asset($user->user_profile) : $defaultAvatar;
                        @endphp
                        <img src="{{ $avatarSrc }}"
                             alt="Profile"
                             onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';"
                             style="width:45px;height:45px;object-fit:cover;border-radius:50%;border:2px solid #dee2e6;background:#f8f9fa;">
                    </td>
                    <td> {{ $user->api_user_id }} </td>
                    <td> {{ $user->username ?? 'N/A' }} </td>
                    <td>
                        <a href="{{ route('admin.user_songs', $user->id) }}"
                            class="btn btn-gradient-primary btn-xs py-1 px-3">View Songs</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No users found matching your search.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div class="text-muted small">
        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
    </div>
    <div class="pagination-container">
        {{ $users->links() }}
    </div>
</div>
