<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'message' => 'Projects retrieved successfully',
                'portfolio_info' => [
                    'total_projects' => $this->collection->count(),
                    'technologies_used' => $this->getUniqueTechnologies(),
                    'project_types' => $this->getUniqueTypes(),
                    'status_distribution' => $this->getStatusDistribution()
                ]
            ]
        ];
    }

    private function getUniqueTechnologies()
    {
        $technologies = [];
        foreach ($this->collection as $project) {
            if (is_array($project->technologies)) {
                $technologies = array_merge($technologies, $project->technologies);
            }
        }
        return array_unique($technologies);
    }

    private function getUniqueTypes()
    {
        return $this->collection->pluck('type')->unique()->values();
    }

    private function getStatusDistribution()
    {
        return $this->collection->groupBy('status')->map->count();
    }
}
