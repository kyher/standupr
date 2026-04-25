<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceivedInvitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'team' => [
                'id' => $this->team->id,
                'name' => $this->team->name,
            ],
            'invited_by' => [
                'name' => $this->invitedBy->name,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
