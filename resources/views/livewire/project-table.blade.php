<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Projects</h5>
        <div class="d-flex align-items-center gap-2">
            <input wire:model.debounce.500ms="search" type="text" class="form-control w-auto" placeholder="Search by title or author">
            <a href="{{ route('project.create') }}" class="btn btn-primary ms-2">Create Project</a>
        </div>
    </div>
    <div class="card-body p-0">
        @if (session()->has('success'))
            <div class="alert alert-success m-3">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td>{{ $project->title }}</td>
                            <td>{{ $project->type }}</td>
                            <td>{{ $project->author }}</td>
                            <td>{{ $project->status }}</td>
                            <td>{{ $project->date }}</td>
                            <td>
                                <button class="btn btn-sm btn-info me-1" wire:click="show({{ $project->id }})">View</button>
                                <button class="btn btn-sm btn-primary me-1" wire:click="edit({{ $project->id }})">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="if(!confirm('Are you sure you want to delete this project?')){event.stopImmediatePropagation();return false;}" wire:click="delete({{ $project->id }})">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No projects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $projects->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>

    @if($showModal && $modalProject)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Project Details</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-4 text-center">
                            @if($modalProject->image)
                                <img src="{{ asset('storage/'.$modalProject->image) }}" class="img-fluid rounded mb-2" style="max-height:200px;">
                            @else
                                <div class="text-muted">No image</div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h5>{{ $modalProject->title }}</h5>
                            <p><strong>Type:</strong> {{ $modalProject->type }}</p>
                            <p><strong>Author:</strong> {{ $modalProject->author }}</p>
                            <p><strong>Status:</strong> {{ $modalProject->status }}</p>
                            <p><strong>Date:</strong> {{ $modalProject->date }}</p>
                            <p><strong>Description:</strong> {{ $modalProject->description }}</p>
                            <p><strong>Technologies:</strong> {{ is_array($modalProject->technologies) ? implode(', ', $modalProject->technologies) : $modalProject->technologies }}</p>
                            <p><strong>Features:</strong><br>
                                @if(is_array($modalProject->features))
                                    <ul class="mb-0">
                                        @foreach($modalProject->features as $feature)
                                            <li>{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $modalProject->features }}
                                @endif
                            </p>
                            <p><strong>Live URL:</strong> <a href="{{ $modalProject->liveUrl }}" target="_blank">{{ $modalProject->liveUrl }}</a></p>
                            <p><strong>GitHub URL:</strong> <a href="{{ $modalProject->githubUrl }}" target="_blank">{{ $modalProject->githubUrl }}</a></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
