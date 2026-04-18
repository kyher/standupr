<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'teams' => TeamResource::collection($request->user()->teams),
        ]);
    }
}
