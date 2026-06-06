<?php

namespace Modules\IAM\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserProjectsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([]);
        }
        $projects = $user->assignedProjects();

        return response()->json($projects);
    }
}
