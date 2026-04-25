<?php

namespace App\Http\Controllers\Invitations;

use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class IndexInvitationController extends Controller
{
    public function __invoke(): Response
    {
        $invitations = Auth::user()
            ->receivedInvitations()
            ->with(['team', 'invitedBy'])
            ->where('status', InvitationStatus::Pending)
            ->latest()
            ->get()
            ->map(fn ($invitation) => [
                'id' => $invitation->id,
                'team' => [
                    'id' => $invitation->team->id,
                    'name' => $invitation->team->name,
                ],
                'invited_by' => [
                    'name' => $invitation->invitedBy->name,
                ],
                'created_at' => $invitation->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Invitations', [
            'invitations' => $invitations,
        ]);
    }
}
