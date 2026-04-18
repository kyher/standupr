<?php

namespace App\Http\Controllers\Teams;

use App\Actions\Teams\StoreTeam;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\StoreTeamRequest;
use Illuminate\Http\RedirectResponse;

class StoreTeamController extends Controller
{
    public function __invoke(StoreTeamRequest $request, StoreTeam $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated('name'));

        return back();
    }
}
