<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AppUser;
use App\Models\Song;

class SongApiController extends Controller
{
    public function saveUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $existingUser = AppUser::where('api_user_id', $request->user_id)->first();

        if ($existingUser) {
            return response()->json([
                'status' => false,
                'message' => 'User ID already exists.',
            ], 400);
        }

        $user = AppUser::create([
            'api_user_id' => $request->user_id,
            'username' => $request->username,
            'password' => $request->password ? bcrypt($request->password) : null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User saved successfully',
            'data' => $user
        ]);
    }

    public function saveSong(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $user = AppUser::where('api_user_id', $request->user_id)->first();

        // If user doesn't exist, create a shell user
        if (!$user) {
            $user = AppUser::create(['api_user_id' => $request->user_id]);
        }

        $songUrl = $request->song_url;
        $localPath = null;

        if ($songUrl) {
            try {
                // Ensure the public/upload/{user_id} directory exists
                $destinationDir = public_path('upload/' . $request->user_id);
                if (!file_exists($destinationDir)) {
                    mkdir($destinationDir, 0777, true);
                }

                // Extract original filename from URL or use a fallback
                $parsedUrl = parse_url($songUrl);
                $originalFilename = basename($parsedUrl['path'] ?? 'downloaded_song.mp3');

                // Sanitize filename (optional but recommended)
                $originalFilename = urldecode($originalFilename);
                $originalFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalFilename);

                $absoluteLocalPath = $destinationDir . '/' . $originalFilename;

                // Download the file stream and save it
                $fileContents = file_get_contents($songUrl);
                if ($fileContents !== false) {
                    file_put_contents($absoluteLocalPath, $fileContents);
                    // Store JUST the filename in the database
                    $localPath = $originalFilename;
                } else {
                    $localPath = $songUrl; // Fallback to original if download fails
                }
            } catch (\Exception $e) {
                // Fallback to storing original URL if something goes wrong (e.g., 404, connection issues)
                $localPath = $songUrl;
            }
        }

        $song = Song::create([
            'app_user_id' => $user->id,
            'genre' => $request->genre,
            'mood' => $request->mood,
            'lyrics' => $request->lyrics,
            'title' => $request->title,
            'song_url' => $localPath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Song saved successfully',
            'data' => $song
        ]);
    }
}
