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
        'images',
        'author',
        'date',
        'description',
        'technologies',
        'features',
        'status',
        'liveUrl',
        'githubUrl',
    ];

    protected $casts = [
        'images' => 'array',
        'technologies' => 'array',
        'features' => 'array',
    ];
} 