<div class="card shadow mb-4">
    <form wire:submit.prevent="save">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">{{ $mode === 'edit' ? 'Edit Skill' : 'Create Skill' }}</h5>
        </div>
        <div class="card-body row g-3">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input wire:model.defer="name" type="text" class="form-control" placeholder="e.g. JavaScript">
                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Color Classes</label>
                <input wire:model.defer="color" type="text" class="form-control" placeholder="e.g. bg-yellow-500 text-black">
                @error('color') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">{{ $mode === 'edit' ? 'Update Skill' : 'Save Skill' }}</button>
            <button type="button" class="btn btn-secondary ms-2" wire:click="resetForm">Clear</button>
        </div>
    </form>
</div>
