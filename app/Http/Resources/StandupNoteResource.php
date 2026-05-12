<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StandupNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'has_blocker' => $this->has_blocker,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
