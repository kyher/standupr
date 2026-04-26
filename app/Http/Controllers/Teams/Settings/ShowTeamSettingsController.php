<?php

namespace App\Http\Controllers\Teams\Settings;

use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamInvitationResource;
use App\Http\Resources\TeamResource;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ShowTeamSettingsController extends Controller
{
    public function __invoke(Team $team): Response
    {
        Gate::authorize('update', $team);

        $team = Auth::user()->teams()->where('teams.id', $team->id)->first();

        $pendingInvitations = TeamInvitationResource::collection(
            $team->invitations()
                ->with(['user', 'invitedBy'])
                ->where('status', InvitationStatus::Pending)
                ->latest()
                ->get()
        );

        $roleNames = Role::pluck('name', 'id');
        $members = $team->users()->get()->map(fn ($member) => [
            'id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'role' => $roleNames[$member->pivot->role_id] ?? null,
        ]);

        return Inertia::render('teams/Settings', [
            'team' => TeamResource::make($team),
            'pending_invitations' => $pendingInvitations,
            'members' => $members,
        ]);
    }
}
