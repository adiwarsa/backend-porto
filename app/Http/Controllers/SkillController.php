<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $items = Skill::paginate(10);
        if (request()->wantsJson()) {
            return response()->json(Skill::all());
        }
        return view('pages.skill.index', compact('items'));
    }
} 