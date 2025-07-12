<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'images' => $this->images ? array_map(function ($image) {
                return asset('storage/' . $image);
            }, $this->images) : [],
            'author' => $this->author,
            'date' => $this->date,
            'description' => $this->description,
            'technologies' => $this->technologies ?? [],
            'features' => $this->features ?? [],
            'status' => $this->status,
            'liveUrl' => $this->liveUrl,
            'githubUrl' => $this->githubUrl,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
