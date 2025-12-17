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
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'project_name' => $this->title, // Use your project column name
            'owner' => $this->user->name ?? 'N/A',
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
