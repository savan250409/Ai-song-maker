<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AppUser;
use App\Models\Song;

class SongApiController extends Controller
{
    // ---------------------------------------------------------------
    // Helper: decode a Base64 image string and save it to disk.
    // Returns the relative path  (upload/profiles/{userId}/filename)
    // or null if no data is provided.
    // ---------------------------------------------------------------
    private function saveProfileImage(?string $base64String, string $userId): ?string
    {
        if (!$base64String) {
            return null;
        }

        try {
            // Strip optional data-URI prefix:  data:image/png;base64,<data>
            $imageData = $base64String;
            if (str_contains($base64String, ',')) {
                [, $imageData] = explode(',', $base64String, 2);
            }

            $decoded = base64_decode(str_replace(' ', '+', $imageData), strict: true);

            if ($decoded === false) {
                return null; // not valid base64
            }

            // Auto-detect extension from the binary signature (magic bytes)
            $extension = 'jpg'; // safe default
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($decoded);
            $map = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];
            if (isset($map[$mime])) {
                $extension = $map[$mime];
            }

            // Build target directory: public/upload/profiles/{userId}/
            $dir = public_path('upload/profiles/' . $userId);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $extension;
            file_put_contents($dir . '/' . $filename, $decoded);

            return $filename; // Return only filename

        } catch (\Exception $e) {
            return null;
        }
    }

    // ---------------------------------------------------------------
    // Helper: build the correct public song URL from the stored value.
    // song_url in DB can be:
    //   (a) just a filename  → build local asset URL
    //   (b) a full http URL  → return as-is (download failed fallback)
    // ---------------------------------------------------------------
    private function buildSongUrl(?string $songUrl, ?string $apiUserId): ?string
    {
        if (!$songUrl || !$apiUserId)
            return null;

        // Strip surrounding or trailing quotes/whitespace that may have crept in
        $songUrl = trim($songUrl, " \t\n\r\0\x0B\"'");

        // If it's a full URL (external/old-server), extract just the filename
        if (str_starts_with($songUrl, 'http://') || str_starts_with($songUrl, 'https://')) {
            $path = parse_url($songUrl, PHP_URL_PATH) ?? '';
            $filename = basename($path);
            $filename = trim($filename, "\"'"); // remove any stray quotes
        } else {
            $filename = $songUrl;
        }

        // Decode URL-encoded chars (e.g. %20 → space) then sanitize → underscores
        $filename = urldecode($filename);
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);
        $filename = trim($filename, '_');

        if (!$filename)
            return null;

        return $apiUserId . '/' . $filename;
    }

    private function getDefaultProfileImage(): string
    {
        $dir = public_path('upload/user-image');
        $images = collect(scandir($dir))
            ->filter(fn($f) => !in_array($f, ['.', '..']))
            ->sortBy(fn($f) => (int) pathinfo($f, PATHINFO_FILENAME))
            ->values();

        $total = $images->count();
        if ($total === 0)
            return '';

        // Round-robin: use current user count mod total images
        $index = AppUser::count() % $total;

        return $images[$index]; // Return only filename
    }

    // ---------------------------------------------------------------
    // Helper: build the correct relative profile path for response
    // ---------------------------------------------------------------
    private function buildProfileUrl(?string $filename, ?string $userId): ?string
    {
        if (!$filename)
            return null;

        // If it's a default image (e.g. 1.webp, 2.webp...)
        if (preg_match('/^\d+\.webp$/', $filename)) {
            return 'user-image/' . $filename;
        }

        // Otherwise it's an uploaded profile image
        return 'profiles/' . $userId . '/' . $filename;
    }

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

        // If no profile uploaded, auto-assign a default image (round-robin)
        $profileFilename = $request->user_profile
            ? $this->saveProfileImage($request->user_profile, $request->user_id)
            : $this->getDefaultProfileImage();

        $user = AppUser::create([
            'api_user_id' => $request->user_id,
            'username' => $request->username,
            'email_address' => $request->email ?? $request->email_address, // Use 'email' if passed
            'password' => $request->password ? bcrypt($request->password) : null,
            'user_profile' => $profileFilename,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User saved successfully',
            'data' => [
                'id' => $user->id,
                'api_user_id' => $user->api_user_id,
                'username' => $user->username,
                'email' => $user->email_address, // Return as 'email' for consistency
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'user_profile' => $this->buildProfileUrl($user->user_profile, $user->api_user_id),
            ],
        ]);
    }

    public function saveSong(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $user = AppUser::where('api_user_id', $request->user_id)->first();

        if (!$user) {
            $profileFilename = $request->user_profile
                ? $this->saveProfileImage($request->user_profile, $request->user_id)
                : $this->getDefaultProfileImage();
            $user = AppUser::create([
                'api_user_id' => $request->user_id,
                'email_address' => $request->email ?? $request->email_address, // Store email if provided
                'user_profile' => $profileFilename,
            ]);
        }

        $songUrl = $request->song_url;
        $localPath = null;

        if ($songUrl) {
            try {
                $destinationDir = public_path('upload/' . $request->user_id);
                if (!file_exists($destinationDir)) {
                    mkdir($destinationDir, 0777, true);
                }
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
            'data' => [
                'id' => $song->id,
                'title' => $song->title,
                'genre' => $song->genre,
                'mood' => $song->mood,
                'lyrics' => $song->lyrics,
                'created_at' => $song->created_at,
                'updated_at' => $song->updated_at,
            ],
        ]);
    }

    public function getUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $user = AppUser::with('songs')->where('api_user_id', $request->user_id)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Build songs list without upload paths
        $songs = $user->songs->map(fn($s) => [
            'id' => $s->id,
            'title' => $s->title,
            'genre' => $s->genre,
            'mood' => $s->mood,
            'lyrics' => $s->lyrics,
            'created_at' => $s->created_at,
            'updated_at' => $s->updated_at,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User detail fetched successfully',
            'data' => [
                'id' => $user->id,
                'api_user_id' => $user->api_user_id,
                'username' => $user->username,
                'email' => $user->email_address, // Return as 'email'
                'user_profile' => $this->buildProfileUrl($user->user_profile, $user->api_user_id),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'songs' => $songs,
            ],
        ]);
    }

    // ---------------------------------------------------------------
    // Get songs filtered by user_id, genre, and/or mood (all optional)
    // ---------------------------------------------------------------
    public function getSongsByFilter(Request $request)
    {
        $query = Song::with('appUser');

        // Filter by user_id
        if ($request->filled('user_id')) {
            $query->whereHas('appUser', function ($q) use ($request) {
                $q->where('api_user_id', $request->user_id);
            });
        }

        // Filter by genre
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        // Filter by mood
        if ($request->filled('mood')) {
            $query->where('mood', $request->mood);
        }

        $songs = $query->orderBy('id', 'desc')->get()->map(function ($song) {
            $songUrl = $this->buildSongUrl(
                $song->song_url,
                $song->appUser?->api_user_id
            );
            return [
                'id' => $song->id,
                'user_name' => $song->appUser ? ($song->appUser->username ?? $song->appUser->api_user_id) : null,
                'title' => $song->title,
                'genre' => $song->genre,
                'mood' => $song->mood,
                'lyrics' => $song->lyrics,
                'song_url' => $songUrl,
                'created_at' => $song->created_at,
                'updated_at' => $song->updated_at,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Songs fetched successfully',
            'total' => $songs->count(),
            'data' => $songs,
        ]);
    }

    // ---------------------------------------------------------------
    // Get 50 random songs with their full song URLs
    // ---------------------------------------------------------------
    public function getRandomSongs()
    {
        $songs = Song::with('appUser')
            ->inRandomOrder()
            ->limit(50)
            ->get()
            ->map(function ($song) {
                $songUrl = $this->buildSongUrl(
                    $song->song_url,
                    $song->appUser?->api_user_id
                );
                return [
                    'id' => $song->id,
                    'user_name' => $song->appUser ? ($song->appUser->username ?? $song->appUser->api_user_id) : null,
                    'title' => $song->title,
                    'genre' => $song->genre,
                    'mood' => $song->mood,
                    'song_url' => $songUrl,
                    'created_at' => $song->created_at,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Random songs fetched successfully',
            'total' => $songs->count(),
            'data' => $songs,
        ]);
    }

    // ---------------------------------------------------------------
    // Get all distinct moods (id + name)
    // ---------------------------------------------------------------
    public function getMoods()
    {
        $moods = Song::whereNotNull('mood')
            ->where('mood', '!=', '')
            ->distinct()
            ->orderBy('mood')
            ->pluck('mood')
            ->values()
            ->map(fn($mood, $index) => [
                'id' => $index + 1,
                'name' => $mood,
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Moods fetched successfully',
            'total' => $moods->count(),
            'data' => $moods,
        ]);
    }

    // ---------------------------------------------------------------
    // Get all distinct genres (id + name)
    // ---------------------------------------------------------------
    public function getGenres()
    {
        $genres = Song::whereNotNull('genre')
            ->where('genre', '!=', '')
            ->distinct()
            ->orderBy('genre')
            ->pluck('genre')
            ->values()
            ->map(fn($genre, $index) => [
                'id' => $index + 1,
                'name' => $genre,
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Genres fetched successfully',
            'total' => $genres->count(),
            'data' => $genres,
        ]);
    }

    // ---------------------------------------------------------------
    // Edit user profile (username and/or profile image)
    // ---------------------------------------------------------------
    public function editProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email', // Identifying by Email
        ]);

        $user = AppUser::where('email_address', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found with this email.',
            ], 404);
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        if ($request->has('user_profile') && !empty($request->user_profile)) {
            $profileFilename = $this->saveProfileImage($request->user_profile, $user->api_user_id);
            if ($profileFilename) {
                $user->user_profile = $profileFilename;
            }
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'api_user_id' => $user->api_user_id,
                'username' => $user->username,
                'email_address' => $user->email_address,
                'user_profile' => $this->buildProfileUrl($user->user_profile, $user->api_user_id),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }
}
