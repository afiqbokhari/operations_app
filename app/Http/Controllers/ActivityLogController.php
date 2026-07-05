<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->user_id, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->action, function ($query, $action) {
                $query->where('action', $action);
            })
            ->when($request->entity_type, function ($query, $type) {
                $query->where('entity_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('logs.index', compact('logs'));
    }
}
