<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use Livewire\Attributes\On;

class ProjectTable extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $modalProject = null;
    public $deleteId = null;
    public $showDeleteModal = false;

    #[On('refreshProjectTable')]
    public function refreshTable()
    {
        // This will trigger a re-render
    }

    public function edit($id)
    {
        return redirect()->route('project.edit', $id);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('openDeleteModal');
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
        $this->dispatch('closeDeleteModal');
    }

    public function delete($id)
    {
        $project = Project::find($id);
        if ($project) {
            $project->delete();
            session()->flash('success', 'Project deleted successfully!');
        }
        $this->refreshTable();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function show($id)
    {
        $this->modalProject = Project::find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalProject = null;
    }

    public function render()
    {
        $projects = Project::where('title', 'like', '%'.$this->search.'%')
            ->orWhere('author', 'like', '%'.$this->search.'%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('livewire.project-table', compact('projects'));
    }
}
