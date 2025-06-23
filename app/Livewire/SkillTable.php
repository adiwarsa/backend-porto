<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Skill;
use Livewire\Attributes\On;

class SkillTable extends Component
{
    use WithPagination;

    public $search = '';
    public $deleteId = null;
    public $showDeleteModal = false;

    #[On('refreshSkillTable')]
    public function refreshTable() {}

    public function edit($id)
    {
        $this->dispatch('editSkill', id: $id);
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
        $skill = Skill::find($id);
        if ($skill) {
            $skill->delete();
            session()->flash('success', 'Skill deleted successfully!');
        }
        $this->refreshTable();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $skills = Skill::where('name', 'like', '%'.$this->search.'%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('livewire.skill-table', compact('skills'));
    }
}
