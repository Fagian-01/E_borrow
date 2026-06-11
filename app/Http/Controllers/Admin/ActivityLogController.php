<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = ActivityLog::with('user:id,name,email,role')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('description', 'like', "%{$s}%")
                        ->orWhere('action', 'like', "%{$s}%")
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$s}%"));
                });
            })
            ->when($request->action, fn ($q, $a) => $q->where('action', $a))
            ->latest()
            ->paginate(20)
            ->through(fn (ActivityLog $log) => [
                'id' => $log->id,
                'action' => $log->action,
                'description' => $log->description,
                'user' => $log->user ? [
                    'name' => $log->user->name,
                    'role' => $log->user->role,
                ] : null,
                'model_type' => $log->model_type ? class_basename($log->model_type) : null,
                'model_id' => $log->model_id,
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at->format('d M Y H:i'),
            ])
            ->withQueryString();

        // Get unique action types for filter
        $actionTypes = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return Inertia::render('Admin/ActivityLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only('search', 'action'),
            'actionTypes' => $actionTypes,
        ]);
    }
}
