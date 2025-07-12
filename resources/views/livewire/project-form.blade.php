<div class="card shadow mb-4">
    <form wire:submit.prevent="save" enctype="multipart/form-data">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">{{ $mode === 'edit' ? 'Edit Project' : 'Create Project' }}</h5>
        </div>
        <div class="card-body row g-3">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="col-md-6">
                <label class="form-label">Title</label>
                <input wire:model.defer="title" type="text" class="form-control" placeholder="e.g. Siverlent">
                @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Type</label>
                <input wire:model.defer="type" type="text" class="form-control" placeholder="e.g. website">
                @error('type') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Images</label>
                
                <!-- Existing Images (Edit Mode) -->
                @if($mode === 'edit' && !empty($existingImages))
                    <div class="mb-3">
                        <h6 class="text-muted">Existing Images:</h6>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($existingImages as $index => $image)
                                <div class="position-relative">
                                    <img src="{{ asset('storage/'.$image) }}" class="img-thumbnail" style="max-height: 100px;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                            wire:click="removeExistingImage({{ $index }})" 
                                            style="transform: translate(50%, -50%);">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- New Image Uploads -->
                <div class="mb-3">
                    <h6 class="text-muted">Add New Images:</h6>
                    @foreach($images as $index => $image)
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-10">
                                <input wire:model="images.{{ $index }}" type="file" class="form-control" accept="image/*">
                                @error("images.{$index}") <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger" wire:click="removeImage({{ $index }})">
                                    <i class="bx bx-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                        @if(isset($imagePreviews[$index]))
                            <div class="mb-2">
                                <img src="{{ $imagePreviews[$index] }}" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        @endif
                    @endforeach
                    
                    <button type="button" class="btn btn-sm btn-success" wire:click="addImage">
                        <i class="bx bx-plus"></i> Add Another Image
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Author</label>
                <input wire:model.defer="author" type="text" class="form-control" placeholder="e.g. Adi Warsa">
                @error('author') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Date</label>
                <input wire:model.defer="date" type="date" class="form-control">
                @error('date') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <input wire:model.defer="status" type="text" class="form-control" placeholder="e.g. Active Development">
                @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea wire:model.defer="description" class="form-control" rows="3" placeholder="e.g. Lorem ipsum dolor sit amet, consectetur adipiscing elit."></textarea>
                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Technologies (comma separated)</label>
                <input wire:model.defer="technologies" type="text" class="form-control" placeholder="e.g. React, Next.js, TypeScript, Tailwind CSS, Framer Motion, AI/ML">
                @error('technologies') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Features (one per line)</label>
                <textarea wire:model.defer="features" class="form-control" rows="3" placeholder="e.g. Website for a company...\nAnother feature" id="features-textarea"></textarea>
                @error('features') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Live URL</label>
                <input wire:model.defer="liveUrl" type="text" class="form-control" placeholder="e.g. https://siverlent.id">
                @error('liveUrl') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">GitHub URL</label>
                <input wire:model.defer="githubUrl" type="text" class="form-control" placeholder="e.g. https://github.com/username/repo">
                @error('githubUrl') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">{{ $mode === 'edit' ? 'Update Project' : 'Save Project' }}</button>
            <button type="button" class="btn btn-secondary ms-2" wire:click="resetForm">Clear</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.getElementById('features-textarea');
        if (textarea) {
            textarea.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    const start = this.selectionStart;
                    const end = this.selectionEnd;
                    this.value = this.value.substring(0, start) + "\n" + this.value.substring(end);
                    this.selectionStart = this.selectionEnd = start + 1;
                    this.dispatchEvent(new Event('input'));
                }
            });
        }
    });
</script>
@endpush

