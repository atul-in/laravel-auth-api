<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public $isAdmin;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->isAdmin = Auth::user()->hasRole('admin');
            return $next($request);
        });
    }

    public function createTask(Request $request)
    {

        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'completed' => 'boolean'
        ]);
        if (!$this->isAdmin) {
            $task = Tasks::create($request->all());
            Log::debug($request->all());
            return response()->json($task, 201);
        } else {
            return response()->json(['status' => false, 'message' => 'You are not authorized to create task'], 403);
        }
    }

    public function getAllTasks(Request $request)
    {
        Log::debug("Going to load tasks");
        // $isAdmin = Auth::user()->hasRole('admin');
        // Log::debug($this->isAdmin);
        try {
            if ($this->isAdmin) {
                $tasks = Tasks::orderBy($request->sortBy, $request->desc ? 'desc' : 'asc')->paginate($request->perPage ? $request->perPage : 10);
                return response()->json(['success' => true, 'data' => $tasks]);
            } else {
                $tasks = Tasks::where('user_id', Auth::user()->id)->orderBy($request->sortBy, $request->desc ? 'desc' : 'asc')->paginate($request->perPage ? $request->perPage : 10);
                return response()->json(['success' => true, 'data' => $tasks]);
            }
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
            return response()->json(['seccess' => false, 'error' => $error->getMessage()], 500);
        }
    }

    public function getTask($id)
    {
        try {
            $task = Tasks::find($id);
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }
            return response()->json(['success' => true, 'data' => $task]);
        } catch (\Exception $error) {
            return response()->json(['seccess' => false, 'error' => $error->getMessage()], 500);
        }

    }

    public function updateTask(Request $request, $id)
    {
        if (!$this->isAdmin) {
            try {
                $task = Tasks::find($id);
                if (!$task) {
                    return response()->json(['message' => 'Task not found'], 404);
                }
                $task->update($request->all());
                return response()->json(['success' => true, 'data' => $task]);
            } catch (\Exception $error) {
                return response()->json(['seccess' => false, 'error' => $error->getMessage()], 500);
            }
        } else {
            return response()->json(['success' => false, 'message' => "You are not authorized"], 403);
        }
    }

    public function deleteTask($id)
    {
        try {
            Tasks::destroy($id);
            return response()->json(['success' => true]);
        } catch (\Exception $error) {
            return response()->json(['seccess' => false, 'error' => $error->getMessage()], 500);
        }
    }
}
