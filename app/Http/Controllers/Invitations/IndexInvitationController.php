<?php

namespace App\Http\Controllers\Invitations;

use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReceivedInvitationResource;
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
            ->get();

        return Inertia::render('Invitations', [
            'invitations' => ReceivedInvitationResource::collection($invitations),
        ]);
    }
}
