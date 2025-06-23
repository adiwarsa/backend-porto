<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Skills</h5>
        <input wire:model.debounce.500ms="search" type="text" class="form-control w-auto" placeholder="Search by name">
    </div>
    <div class="card-body p-0">
        @if (session()->has('success'))
            <div class="alert alert-success m-3">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Color</th>
                        <th>Preview</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skills as $skill)
                        <tr>
                            <td>{{ $skill->name }}</td>
                            <td><code>{{ $skill->color }}</code></td>
                            <td><span class="badge {{ $skill->color }}">{{ $skill->name }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary me-1" wire:click="edit({{ $skill->id }})">Edit</button>
                                <button class="btn btn-sm btn-danger" wire:click="delete({{ $skill->id }})" onclick="return confirm('Are you sure you want to delete this skill?')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No skills found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $skills->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>


