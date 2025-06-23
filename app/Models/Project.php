<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'image',
        'author',
        'date',
        'gradient',
        'description',
        'technologies',
        'features',
        'status',
        'liveUrl',
        'githubUrl',
    ];

    protected $casts = [
        'technologies' => 'array',
        'features' => 'array',
    ];
} 