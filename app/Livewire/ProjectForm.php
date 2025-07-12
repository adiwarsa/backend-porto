<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Project;

class ProjectForm extends Component
{
    use WithFileUploads;

    public $projectId;
    public $title;
    public $type;
    public $images = [];
    public $imagePreviews = [];
    public $existingImages = [];
    public $author = 'Adi Warsa';
    public $date;
    public $description;
    public $technologies;
    public $features;
    public $status;
    public $liveUrl;
    public $githubUrl;
    public $mode = 'create';

    protected function rules()
    {
        $rules = [
            'title' => 'required|string',
            'type' => 'required|string',
            'author' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'technologies' => 'nullable|string',
            'features' => 'nullable|string',
            'status' => 'required|string',
            'liveUrl' => 'nullable|url',
            'githubUrl' => 'nullable|url',
        ];

        // Add validation rules for each image
        if ($this->images) {
            foreach ($this->images as $index => $image) {
                $rules["images.{$index}"] = 'nullable|image|max:2048';
            }
        }

        return $rules;
    }

    protected $listeners = ['editProject' => 'loadProject'];

    public function mount($projectId = null)
    {
        if ($projectId) {
            $this->loadProject($projectId);
            $this->mode = 'edit';
        } else {
            $this->mode = 'create';
            // Initialize with one empty image input for new projects
            $this->images = [null];
        }
    }

    public function updatedImages()
    {
        $this->imagePreviews = [];
        if ($this->images) {
            foreach ($this->images as $index => $image) {
                if ($image) {
                    $this->imagePreviews[$index] = $image->temporaryUrl();
                }
            }
        }
    }

    public function addImage()
    {
        $this->images[] = null;
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
        if (isset($this->imagePreviews[$index])) {
            unset($this->imagePreviews[$index]);
            $this->imagePreviews = array_values($this->imagePreviews);
        }
    }

    public function removeExistingImage($index)
    {
        if (isset($this->existingImages[$index])) {
            unset($this->existingImages[$index]);
            $this->existingImages = array_values($this->existingImages);
        }
    }

    public function save()
    {
        $this->validate();
        
        $imagePaths = [];
        
        // Add existing images that weren't removed
        if ($this->existingImages) {
            $imagePaths = array_merge($imagePaths, $this->existingImages);
        }
        
        // Add new uploaded images
        if ($this->images) {
            foreach ($this->images as $image) {
                if ($image) {
                    $imagePaths[] = $image->store('projects', 'public');
                }
            }
        }
        
        $data = [
            'title' => $this->title,
            'type' => $this->type,
            'images' => $imagePaths,
            'author' => $this->author,
            'date' => $this->date,
            'description' => $this->description,
            'technologies' => array_map('trim', explode(',', $this->technologies)),
            'features' => array_map('trim', preg_split('/\r\n|\r|\n/', $this->features)),
            'status' => $this->status,
            'liveUrl' => $this->liveUrl,
            'githubUrl' => $this->githubUrl,
        ];
        
        if ($this->mode === 'edit' && $this->projectId) {
            $project = Project::find($this->projectId);
            if ($project) {
                $project->update($data);
            }
            session()->flash('success', 'Project updated successfully!');
        } else {
            Project::create($data);
            session()->flash('success', 'Project created successfully!');
        }
        $this->resetForm();
        $this->dispatch('refreshProjectTable');
    }

    public function loadProject($id)
    {
        $project = Project::find($id);
        if ($project) {
            $this->projectId = $project->id;
            $this->title = $project->title;
            $this->type = $project->type;
            $this->author = $project->author;
            $this->date = $project->date;
            $this->description = $project->description;
            $this->technologies = is_array($project->technologies) ? implode(', ', $project->technologies) : $project->technologies;
            $this->features = is_array($project->features) ? implode("\n", $project->features) : $project->features;
            $this->status = $project->status;
            $this->liveUrl = $project->liveUrl;
            $this->githubUrl = $project->githubUrl;
            $this->existingImages = $project->images ?? [];
            $this->mode = 'edit';
        }
    }

    public function resetForm()
    {
        $this->reset(['projectId','title','type','images','imagePreviews','existingImages','author','date','description','technologies','features','status','liveUrl','githubUrl']);
        $this->author = 'Adi Warsa';
        $this->mode = 'create';
    }

    public function render()
    {
        return view('livewire.project-form');
    }
}
