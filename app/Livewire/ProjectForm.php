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
    public $image;
    public $imagePreview;
    public $author = 'Adi Warsa';
    public $date;
    public $gradient;
    public $description;
    public $technologies;
    public $features;
    public $status;
    public $liveUrl;
    public $githubUrl;
    public $mode = 'create';

    protected $rules = [
        'title' => 'required|string',
        'type' => 'required|string',
        'image' => 'nullable|image|max:2048',
        'author' => 'required|string',
        'date' => 'required|date',
        'gradient' => 'nullable|string',
        'description' => 'nullable|string',
        'technologies' => 'nullable|string',
        'features' => 'nullable|string',
        'status' => 'required|string',
        'liveUrl' => 'nullable|url',
        'githubUrl' => 'nullable|url',
    ];

    protected $listeners = ['editProject' => 'loadProject'];

    public function mount($projectId = null)
    {
        if ($projectId) {
            $this->loadProject($projectId);
            $this->mode = 'edit';
        } else {
            $this->mode = 'create';
        }
    }

    public function updatedImage()
    {
        if ($this->image) {
            $this->imagePreview = $this->image->temporaryUrl();
        }
    }

    public function save()
    {
        $this->validate();
        $imagePath = $this->image ? $this->image->store('projects', 'public') : null;
        $data = [
            'title' => $this->title,
            'type' => $this->type,
            'image' => $imagePath,
            'author' => $this->author,
            'date' => $this->date,
            'gradient' => $this->gradient,
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
                if (!$imagePath) unset($data['image']);
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
            $this->gradient = $project->gradient;
            $this->description = $project->description;
            $this->technologies = is_array($project->technologies) ? implode(', ', $project->technologies) : $project->technologies;
            $this->features = is_array($project->features) ? implode("\n", $project->features) : $project->features;
            $this->status = $project->status;
            $this->liveUrl = $project->liveUrl;
            $this->githubUrl = $project->githubUrl;
            $this->mode = 'edit';
        }
    }

    public function resetForm()
    {
        $this->reset(['projectId','title','type','image','imagePreview','author','date','gradient','description','technologies','features','status','liveUrl','githubUrl']);
        $this->author = 'Adi Warsa';
        $this->mode = 'create';
    }

    public function render()
    {
        return view('livewire.project-form');
    }
}
