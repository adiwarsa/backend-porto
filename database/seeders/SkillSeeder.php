<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            [ 'name' => 'JavaScript', 'color' => 'bg-yellow-500 text-black' ],
            [ 'name' => 'HTML/CSS', 'color' => 'bg-pink-500 text-white' ],
            [ 'name' => 'React.js', 'color' => 'bg-blue-600 text-white' ],
            [ 'name' => 'Next.js', 'color' => 'bg-black text-white' ],
            [ 'name' => 'Node.js', 'color' => 'bg-green-600 text-white' ],
            [ 'name' => 'Express', 'color' => 'bg-gray-800 text-white' ],
            [ 'name' => 'Tailwind CSS', 'color' => 'bg-cyan-600 text-white' ],
            [ 'name' => 'Vue.js', 'color' => 'bg-red-600 text-white' ],
            [ 'name' => 'Laravel', 'color' => 'bg-red-600 text-white' ],
            [ 'name' => 'Livewire', 'color' => 'bg-yellow-600 text-white' ],
            [ 'name' => 'MySQL', 'color' => 'bg-red-600 text-white' ],
            [ 'name' => 'Git', 'color' => 'bg-red-600 text-white' ],
            [ 'name' => 'Figma', 'color' => 'bg-purple-600 text-white' ],
            [ 'name' => 'MongoDB', 'color' => 'bg-green-600 text-white' ],
            [ 'name' => 'PostgreSQL', 'color' => 'bg-blue-600 text-white' ],
        ];
        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
} 