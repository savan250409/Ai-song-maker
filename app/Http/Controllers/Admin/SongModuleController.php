<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AppUser;
use App\Models\Song;

class SongModuleController extends Controller
{
    public function dashboard()
    {
        $totalUsers = AppUser::count();
        $totalSongs = Song::count();
        return view('admin.dashboard', compact('totalUsers', 'totalSongs'));
    }
    public function appUsers(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = AppUser::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('api_user_id', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        return view('admin.app_users.index', compact('users', 'search', 'perPage'));
    }

    public function songs(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = Song::with('appUser');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('genre', 'LIKE', "%{$search}%")
                    ->orWhere('mood', 'LIKE', "%{$search}%")
                    ->orWhereHas('appUser', function ($sq) use ($search) {
                        $sq->where('api_user_id', 'LIKE', "%{$search}%")
                            ->orWhere('username', 'LIKE', "%{$search}%");
                    });
            });
        }

        $songs = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        return view('admin.songs.index', compact('songs', 'search', 'perPage'));
    }

    public function userSongs($id)
    {
        $user = AppUser::findOrFail($id);
        $songs = Song::where('app_user_id', $id)->orderBy('id', 'desc')->get();
        return view('admin.app_users.user_songs', compact('user', 'songs'));
    }
}
