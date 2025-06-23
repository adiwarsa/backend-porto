<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Skill;
use Livewire\Attributes\On;

class SkillForm extends Component
{
    public $skillId;
    public $name;
    public $color;
    public $mode = 'create';

    protected $rules = [
        'name' => 'required|string',
        'color' => 'required|string',
    ];

    public function mount($skillId = null)
    {
        if ($skillId) {
            $this->loadSkill($skillId);
            $this->mode = 'edit';
        } else {
            $this->mode = 'create';
        }
    }

    #[On('editSkill')]
    public function editSkill($id)
    {
        $this->loadSkill($id);
        $this->mode = 'edit';
    }

    public function save()
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'color' => $this->color,
        ];
        if ($this->mode === 'edit' && $this->skillId) {
            $skill = Skill::find($this->skillId);
            if ($skill) {
                $skill->update($data);
            }
            session()->flash('success', 'Skill updated successfully!');
        } else {
            Skill::create($data);
            session()->flash('success', 'Skill created successfully!');
        }
        $this->resetForm();
        $this->dispatch('refreshSkillTable');
    }

    public function loadSkill($id)
    {
        $skill = Skill::find($id);
        if ($skill) {
            $this->skillId = $skill->id;
            $this->name = $skill->name;
            $this->color = $skill->color;
            $this->mode = 'edit';
        }
    }

    public function resetForm()
    {
        $this->reset(['skillId','name','color']);
        $this->mode = 'create';
    }

    public function render()
    {
        return view('livewire.skill-form');
    }
}
