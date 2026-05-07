<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SongCover;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SongCoverController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = SongCover::query();

        if ($search) {
            $query->where('voice_name', 'LIKE', "%{$search}%")
                  ->orWhere('voice_id', 'LIKE', "%{$search}%");
        }

        $covers = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('admin.song_covers._table', compact('covers'));
        }
        return view('admin.song_covers.index', compact('covers', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'voice_id' => 'required|unique:song_covers,voice_id',
            'voice_name' => 'required',
            'image' => 'required|image|mimes:webp|max:2048',
        ], [
            'image.mimes' => 'Only WEBP images are allowed.',
        ]);

        $data = $request->only(['voice_id', 'voice_name', 'tts_only']);
        $data['tts_only'] = $request->has('tts_only');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = 'uploads/song_covers';
            
            if (!File::exists(public_path($path))) {
                File::makeDirectory(public_path($path), 0777, true, true);
            }
            
            $image->move(public_path($path), $filename);
            $data['image'] = $path . '/' . $filename;
        }

        SongCover::create($data);

        return redirect()->back()->with('success', 'Voice added successfully.');
    }

    public function update(Request $request, $id)
    {
        $cover = SongCover::findOrFail($id);

        $request->validate([
            'voice_id' => 'required|unique:song_covers,voice_id,' . $id,
            'voice_name' => 'required',
            'image' => 'nullable|image|mimes:webp|max:2048',
        ], [
            'image.mimes' => 'Only WEBP images are allowed.',
        ]);

        $data = $request->only(['voice_id', 'voice_name', 'tts_only']);
        $data['tts_only'] = $request->has('tts_only');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($cover->image && File::exists(public_path($cover->image))) {
                File::delete(public_path($cover->image));
            }

            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = 'uploads/song_covers';
            
            if (!File::exists(public_path($path))) {
                File::makeDirectory(public_path($path), 0777, true, true);
            }
            
            $image->move(public_path($path), $filename);
            $data['image'] = $path . '/' . $filename;
        }

        $cover->update($data);

        return redirect()->back()->with('success', 'Voice updated successfully.');
    }

    public function destroy($id)
    {
        $cover = SongCover::findOrFail($id);
        
        if ($cover->image && File::exists(public_path($cover->image))) {
            File::delete(public_path($cover->image));
        }
        
        $cover->delete();

        return redirect()->back()->with('success', 'Voice deleted successfully.');
    }
}
