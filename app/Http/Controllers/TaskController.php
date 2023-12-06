<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function createTask(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'completed' => 'boolean'
        ]);
        $task = Tasks::create($request->all());
        Log::debug($request->all());
        return response()->json($task, 201);
    }

    public function getAllTasks(Request $request)
    {
        Log::debug("Going to load tasks");
        $data = Auth::user()->role_id;
        try {
            if ($data == 1) {
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
