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

<div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div class="text-muted small">
        Showing {{ $covers->firstItem() ?? 0 }} to {{ $covers->lastItem() ?? 0 }} of
        {{ $covers->total() }} entries
    </div>
    <div class="pagination-container">
        {{ $covers->links() }}
    </div>
</div>
