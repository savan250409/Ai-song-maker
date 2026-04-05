<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AppUser;
use App\Models\Song;
use App\Models\SongCover;
use Illuminate\Support\Facades\Log;

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

    private function saveSongCoverImage(?string $base64String, string $userId): ?string
    {
        if (!$base64String) {
            return null;
        }

        try {
            // Strip optional data-URI prefix: data:image/png;base64,<data>
            $imageData = $base64String;
            if (str_contains($base64String, ',')) {
                [, $imageData] = explode(',', $base64String, 2);
            }

            $decoded = base64_decode(str_replace(' ', '+', $imageData), strict: true);

            if ($decoded === false) {
                Log::warning('saveSongCoverImage: base64_decode failed', ['userId' => $userId]);
                return null;
            }

            $extension = 'jpg';
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

            $dir = public_path('upload/' . $userId);
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                    Log::error('saveSongCoverImage: failed to create directory', ['dir' => $dir]);
                    return null;
                }
            }

            $filename = 'cover_' . time() . '_' . uniqid() . '.' . $extension;
            $written = file_put_contents($dir . '/' . $filename, $decoded);

            if ($written === false) {
                Log::error('saveSongCoverImage: file_put_contents failed', ['path' => $dir . '/' . $filename]);
                return null;
            }

            return $filename;
        } catch (\Exception $e) {
            Log::error('saveSongCoverImage exception', ['error' => $e->getMessage(), 'userId' => $userId]);
            return null;
        }
    }

    // ---------------------------------------------------------------
    // Helper: download a cover image from a URL and save it locally.
    // Returns filename on success, null on failure.
    // ---------------------------------------------------------------
    private function saveSongCoverImageFromUrl(string $imageUrl, string $userId): ?string
    {
        try {
            $dir = public_path('upload/' . $userId);
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                    Log::error('saveSongCoverImageFromUrl: failed to create directory', ['dir' => $dir]);
                    return null;
                }
            }

            // Use cURL for reliable download with proper headers
            $ch = curl_init($imageUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; SongApp/1.0)',
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $contents = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($contents === false || $httpCode < 200 || $httpCode >= 300 || empty($contents)) {
                Log::warning('saveSongCoverImageFromUrl: download failed', [
                    'url' => $imageUrl,
                    'http_code' => $httpCode,
                    'curl_error' => $curlError,
                ]);
                return null;
            }

            // Detect extension from MIME type
            $extension = 'jpg';
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($contents);
            $map = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];
            if (isset($map[$mime])) {
                $extension = $map[$mime];
            } else {
                // Fallback: try to grab extension from URL path
                $urlPath = parse_url($imageUrl, PHP_URL_PATH) ?? '';
                $urlExt = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
                if (in_array($urlExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $extension = $urlExt === 'jpeg' ? 'jpg' : $urlExt;
                }
            }

            $filename = 'cover_' . time() . '_' . uniqid() . '.' . $extension;
            $written = file_put_contents($dir . '/' . $filename, $contents);

            if ($written === false) {
                Log::error('saveSongCoverImageFromUrl: file_put_contents failed', ['path' => $dir . '/' . $filename]);
                return null;
            }

            return $filename;
        } catch (\Exception $e) {
            Log::error('saveSongCoverImageFromUrl exception', ['error' => $e->getMessage(), 'url' => $imageUrl]);
            return null;
        }
    }

    private function buildCoverImageUrl(?string $filename, ?string $userId): ?string
    {
        if (!$filename || !$userId) {
            return null;
        }

        return asset('upload/' . $userId . '/' . $filename);
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

        return asset('upload/' . $apiUserId . '/' . $filename);
    }

    private function getDefaultProfileImage(): string
    {
        $dir = public_path('upload/user-image');

        if (!is_dir($dir) || !is_readable($dir)) {
            return '';
        }

        $images = collect(scandir($dir))
            ->filter(fn($f) => !in_array($f, ['.', '..']))
            ->sortBy(fn($f) => (int) pathinfo($f, PATHINFO_FILENAME))
            ->values();

        $total = $images->count();
        if ($total === 0) {
            return '';
        }

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
            return asset('upload/user-image/' . $filename);
        }

        // Otherwise it's an uploaded profile image
        return asset('upload/profiles/' . $userId . '/' . $filename);
    }

    private function normalizeProfileName(?string $profileName): ?string
    {
        if (!$profileName) {
            return null;
        }

        $profileName = trim($profileName);
        $profileName = preg_replace('/\s+/', '', $profileName);

        return $profileName ?: null;
    }

    public function saveUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'profile_name' => 'nullable|string',
        ]);

        $existingUser = AppUser::where('api_user_id', $request->user_id)->first();
        $email = $request->email ?? $request->email_address;

        if ($existingUser) {
            // "When 201 error occurs then check if email comes..."
            if ($email) {
                $userByEmail = AppUser::where('email_address', $email)->first();
                if ($userByEmail && $userByEmail->id !== $existingUser->id) {
                    // "...for that email in database replace new user id which comes from the user request"
                    // Do this without any conflict: 
                    // 1. Move songs from the temporary device user to the real email account
                    \DB::table('songs')
                        ->where('app_user_id', $existingUser->id)
                        ->update(['app_user_id' => $userByEmail->id]);
                    
                    // 2. Delete the temporary user to avoid database constraint or logic conflicts
                    $existingUser->delete();
                    
                    // 3. Update the real email account with the new user_id
                    $userByEmail->api_user_id = $request->user_id;
                    $userByEmail->save();
                } else if (!$userByEmail) {
                    // Update current user with the new email
                    $existingUser->email_address = $email;
                    $existingUser->save();
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'user already exist',
            ], 201);
        }

        // Also handle the edge case where the user_id is completely new but they send an existing email
        if ($email && !$existingUser) {
            $userByEmail = AppUser::where('email_address', $email)->first();
            if ($userByEmail) {
                $userByEmail->api_user_id = $request->user_id;
                $userByEmail->save();

                return response()->json([
                    'status'  => true,
                    'message' => 'user already exist',
                ], 201);
            }
        }

        // If no profile uploaded, auto-assign a default image (round-robin)
        $profileFilename = $request->user_profile
            ? $this->saveProfileImage($request->user_profile, $request->user_id)
            : $this->getDefaultProfileImage();

        $user = AppUser::create([
            'api_user_id' => $request->user_id,
            'username' => $request->username,
            'profile_name' => $this->normalizeProfileName($request->profile_name ?? $request->username),
            'email_address' => $request->email ?? $request->email_address, // Use 'email' if passed
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
                'profile_name' => $user->profile_name,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'user_profile' => $this->buildProfileUrl($user->user_profile, $user->api_user_id),
            ],
        ]);
    }

    public function saveSong(Request $request)
    {
        $saveSongStartTime = microtime(true);
        \Log::info('saveSong: START', [
            'user_id' => $request->user_id,
            'song_url' => $request->song_url,
            'has_cover' => $request->filled('cover_image'),
            'cover_is_url' => $request->filled('cover_image')
                ? (str_starts_with(trim($request->cover_image), 'http') ? 'yes' : 'no(base64)')
                : 'not provided',
        ]);

        $request->validate([
            'user_id' => 'required',
            'cover_image' => 'nullable|string',
        ]);

        $user = AppUser::where('api_user_id', $request->user_id)->first();

        if (!$user) {
            \Log::info('saveSong: user not found, creating new user', ['user_id' => $request->user_id]);
            $profileFilename = $request->user_profile
                ? $this->saveProfileImage($request->user_profile, $request->user_id)
                : $this->getDefaultProfileImage();
            $user = AppUser::create([
                'api_user_id' => $request->user_id,
                'profile_name' => $this->normalizeProfileName($request->profile_name ?? $request->username),
                'email_address' => $request->email ?? $request->email_address, // Store email if provided
                'user_profile' => $profileFilename,
            ]);
        }

        $songUrl = $request->song_url;
        $localPath = null;
        $coverImageFilename = null;

        // ---- COVER IMAGE SAVE ----
        if ($request->filled('cover_image')) {
            $coverRaw = trim($request->cover_image);
            \Log::info('saveSong: processing cover_image', ['type' => str_starts_with($coverRaw, 'http') ? 'url' : 'base64']);
            // If it's a URL (from API), download it; otherwise treat as base64
            if (str_starts_with($coverRaw, 'http://') || str_starts_with($coverRaw, 'https://')) {
                $coverImageFilename = $this->saveSongCoverImageFromUrl($coverRaw, $request->user_id);
            } else {
                $coverImageFilename = $this->saveSongCoverImage($coverRaw, $request->user_id);
            }
            \Log::info('saveSong: cover_image result', ['coverImageFilename' => $coverImageFilename]);
        } else {
            \Log::info('saveSong: no cover_image in request');
        }

        // ---- SONG DOWNLOAD ----
        if ($songUrl) {
            \Log::info('saveSong: starting song download', ['song_url' => $songUrl]);
            try {
                $destinationDir = public_path('upload/' . $request->user_id);
                \Log::info('saveSong: destination dir', ['dir' => $destinationDir, 'exists' => file_exists($destinationDir)]);

                if (!file_exists($destinationDir)) {
                    $mkdirResult = mkdir($destinationDir, 0755, true);
                    \Log::info('saveSong: mkdir result', ['result' => $mkdirResult, 'dir' => $destinationDir]);
                }

                $parsedUrl = parse_url($songUrl);
                $originalFilename = basename($parsedUrl['path'] ?? 'downloaded_song.mp3');

                // Sanitize filename (optional but recommended)
                $originalFilename = urldecode($originalFilename);
                $originalFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalFilename);

                $absoluteLocalPath = $destinationDir . '/' . $originalFilename;
                \Log::info('saveSong: downloading song file', ['filename' => $originalFilename, 'dest' => $absoluteLocalPath]);

                // Download the file stream and save it
                $downloadStartTime = microtime(true);
                $fileContents = file_get_contents($songUrl);
                $downloadDuration = round(microtime(true) - $downloadStartTime, 3);

                if ($fileContents !== false) {
                    // ---- PURE PHP ID3 TAGGING (NO EXTERNAL LIBS) ----
                    if ($coverImageFilename) {
                        try {
                            $coverPath = public_path('upload/' . $request->user_id . '/' . $coverImageFilename);
                            if (file_exists($coverPath)) {
                                \Log::info('saveSong: embedding cover purely via PHP ID3 insertion');
                                
                                $imgData = file_get_contents($coverPath);
                                $mime = mime_content_type($coverPath) ?: 'image/jpeg';
                                
                                $id3Frames = '';
                                
                                // APIC Frame (Cover Image)
                                if (!empty($imgData)) {
                                    $payload = chr(0) . $mime . chr(0) . chr(3) . chr(0) . $imgData;
                                    $id3Frames .= 'APIC' . pack('N', strlen($payload)) . chr(0) . chr(0) . $payload;
                                }
                                
                                // TIT2 Frame (Title)
                                if (!empty($request->title)) {
                                    $titlePayload = chr(1) . "\xFF\xFE" . mb_convert_encoding($request->title, 'UTF-16LE', 'UTF-8');
                                    $id3Frames .= 'TIT2' . pack('N', strlen($titlePayload)) . chr(0) . chr(0) . $titlePayload;
                                }
                                
                                // TPE1 Frame (Artist)
                                $artist = $user->username ?? 'AI Song Maker';
                                $artistPayload = chr(1) . "\xFF\xFE" . mb_convert_encoding($artist, 'UTF-16LE', 'UTF-8');
                                $id3Frames .= 'TPE1' . pack('N', strlen($artistPayload)) . chr(0) . chr(0) . $artistPayload;
                                
                                // TCON Frame (Genre)
                                if (!empty($request->genre)) {
                                    $genrePayload = chr(1) . "\xFF\xFE" . mb_convert_encoding($request->genre, 'UTF-16LE', 'UTF-8');
                                    $id3Frames .= 'TCON' . pack('N', strlen($genrePayload)) . chr(0) . chr(0) . $genrePayload;
                                }
                                
                                if ($id3Frames !== '') {
                                    $id3Size = strlen($id3Frames);
                                    $syncSafeSize = chr(($id3Size >> 21) & 0x7F) . chr(($id3Size >> 14) & 0x7F) . chr(($id3Size >> 7) & 0x7F) . chr($id3Size & 0x7F);
                                    $id3Header = 'ID3' . chr(3) . chr(0) . chr(0) . $syncSafeSize;
                                    
                                    // Prepend ID3 tag to downloaded file contents
                                    $fileContents = $id3Header . $id3Frames . $fileContents;
                                    \Log::info("saveSong: successfully injected pure PHP ID3 tag (Size: $id3Size bytes)");
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::error('saveSong: Pure PHP ID3 insertion exception', ['error' => $e->getMessage()]);
                        }
                    }

                    $written = file_put_contents($absoluteLocalPath, $fileContents);
                    \Log::info("saveSong: [TIMER] Song Download/Tagged saved in {$downloadDuration}s", [
                        'bytes' => $written,
                        'path' => $absoluteLocalPath,
                        'url' => $songUrl
                    ]);
                    
                    // Store JUST the filename in the database
                    $localPath = $originalFilename;
                } else {
                    \Log::error('saveSong: file_get_contents failed for song URL', ['song_url' => $songUrl]);
                    $localPath = $songUrl; // Fallback to original if download fails
                }
            } catch (\Exception $e) {
                \Log::error('saveSong: song download exception', ['error' => $e->getMessage(), 'song_url' => $songUrl]);
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
            'cover_image' => $coverImageFilename,
        ]);

        $saveSongTotalDuration = round(microtime(true) - $saveSongStartTime, 3);
        \Log::info("saveSong: COMPLETION - Total time: {$saveSongTotalDuration}s", [
            'song_id' => $song->id,
            'final_format' => pathinfo($localPath, PATHINFO_EXTENSION) ?: 'unknown'
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
                'song_url' => $this->buildSongUrl($song->song_url, $user->api_user_id),
                'cover_image' => $this->buildCoverImageUrl($song->cover_image, $user->api_user_id),
                'created_at' => $song->created_at,
                'updated_at' => $song->updated_at,
            ],
        ]);
    }

    public function getUser(Request $request)
    {
        // Accept: user_id  OR  user_name  OR  username  OR  email
        $request->validate([
            'user_id' => 'required_without_all:user_name,username,email',
        ], [
            'user_id.required_without_all' => 'Provide at least one of: user_id, user_name, username, or email.',
        ]);

        $query = AppUser::with('songs');

        if ($request->filled('user_id')) {
            $query->where('api_user_id', $request->user_id);
        } elseif ($request->filled('user_name') || $request->filled('username')) {
            $userName = $request->user_name ?? $request->username;
            $query->where('username', $userName);
        } elseif ($request->filled('email')) {
            $query->where('email_address', $request->email);
        }

        $user = $query->first();

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
            'song_url' => $this->buildSongUrl($s->song_url, $user->api_user_id),
            'cover_image' => $this->buildCoverImageUrl($s->cover_image, $user->api_user_id),
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
                'profile_name' => $user->profile_name,
                'user_profile' => $this->buildProfileUrl($user->user_profile, $user->api_user_id),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'songs' => $songs,
            ],
        ]);
    }

    public function deleteUser(Request $request)
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

        \DB::transaction(function () use ($user) {
            $userDir = public_path('upload/' . $user->api_user_id);
            $profileDir = public_path('upload/profiles/' . $user->api_user_id);

            foreach ($user->songs as $song) {
                $songFile = $song->song_url;

                if ($songFile) {
                    if (str_starts_with($songFile, 'http://') || str_starts_with($songFile, 'https://')) {
                        $songFile = basename(parse_url($songFile, PHP_URL_PATH) ?? '');
                    }

                    $songFile = urldecode($songFile);
                    $songFile = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $songFile);

                    if ($songFile) {
                        $songPath = $userDir . '/' . $songFile;
                        if (file_exists($songPath) && is_file($songPath)) {
                            @unlink($songPath);
                        }
                    }
                }

                if ($song->cover_image) {
                    $coverFile = $song->cover_image;
                    $coverFile = urldecode($coverFile);
                    $coverFile = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $coverFile);
                    if ($coverFile) {
                        $coverPath = $userDir . '/' . $coverFile;
                        if (file_exists($coverPath) && is_file($coverPath)) {
                            @unlink($coverPath);
                        }
                    }
                }

                $song->delete();
            }

            if ($user->user_profile && !preg_match('/^\d+\.webp$/', $user->user_profile)) {
                $profilePath = $profileDir . '/' . $user->user_profile;
                if (file_exists($profilePath) && is_file($profilePath)) {
                    @unlink($profilePath);
                }
            }

            if (is_dir($userDir)) {
                $remaining = array_diff(scandir($userDir), ['.', '..']);
                if (empty($remaining)) {
                    @rmdir($userDir);
                }
            }

            if (is_dir($profileDir)) {
                $remaining = array_diff(scandir($profileDir), ['.', '..']);
                if (empty($remaining)) {
                    @rmdir($profileDir);
                }
            }

            $user->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'User and all associated songs deleted successfully',
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
                'cover_image' => $this->buildCoverImageUrl($song->cover_image, $song->appUser?->api_user_id),
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
                    'lyrics' => $song->lyrics,
                    'song_url' => $songUrl,
                    'cover_image' => $this->buildCoverImageUrl($song->cover_image, $song->appUser?->api_user_id),
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
    // Edit user profile — identify by user_id, update any fields passed
    // POST fields: user_id (required), username, profile_name,
    //              email_address, user_profile (base64)
    // ---------------------------------------------------------------
    public function editProfile(Request $request)
    {
        $request->validate([
            'user_id' => 'required', // Identifying by user_id
        ]);

        $user = AppUser::where('api_user_id', $request->user_id)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found with this user_id.',
            ], 404);
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        if ($request->has('profile_name')) {
            $user->profile_name = $this->normalizeProfileName($request->profile_name);
        }

        // Accept both 'email' and 'email_address' field names
        if ($request->filled('email')) {
            $user->email_address = $request->email;
        } elseif ($request->filled('email_address')) {
            $user->email_address = $request->email_address;
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
                'id'           => $user->id,
                'api_user_id'  => $user->api_user_id,
                'username'     => $user->username,
                'profile_name' => $user->profile_name,
                'email'        => $user->email_address,
                'user_profile' => $this->buildProfileUrl($user->user_profile, $user->api_user_id),
                'created_at'   => $user->created_at,
                'updated_at'   => $user->updated_at,
            ],
        ]);
    }

    public function getSongCovers()
    {
        $voices = SongCover::all()->map(function ($cover) {
            return [
                'voice_id' => $cover->voice_id,
                'voice_name' => $cover->voice_name,
                'tts_only' => (bool)$cover->tts_only,
                'image' => asset($cover->image),
            ];
        });

        return response()->json([
            'success' => true,
            'voices' => $voices,
        ]);
    }
}
