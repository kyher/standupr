<?php

namespace App\Http\Controllers\Teams;

use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\StandupResource;
use App\Http\Resources\TeamInvitationResource;
use App\Http\Resources\TeamResource;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ShowTeamController extends Controller
{
    public function __invoke(Team $team): Response
    {
        Gate::authorize('view', $team);

        $team = Auth::user()->teams()->where('teams.id', $team->id)->first();

        $todayStandup = $team->standups()->whereDate('date', today())->first();

        $previousStandups = $team->standups()
            ->whereDate('date', '<', today())
            ->orderByDesc('date')
            ->get();

        $roleNames = Role::pluck('name', 'id');
        $members = $team->users()->get()->map(fn ($member) => [
            'id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'role' => $roleNames[$member->pivot->role_id] ?? null,
        ]);

        $pendingInvitations = null;
        if ($team->pivot->role->name === 'admin') {
            $pendingInvitations = TeamInvitationResource::collection(
                $team->invitations()
                    ->with(['user', 'invitedBy'])
                    ->where('status', InvitationStatus::Pending)
                    ->latest()
                    ->get()
            );
        }

        return Inertia::render('teams/Show', [
            'team' => TeamResource::make($team),
            'today_standup' => $todayStandup ? StandupResource::make($todayStandup) : null,
            'previous_standups' => StandupResource::collection($previousStandups),
            'members' => $members,
            'pending_invitations' => $pendingInvitations,
        ]);
    }
}
