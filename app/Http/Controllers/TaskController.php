<?php

namespace App\Http\Controllers;

use App\Exports\TasksExport;
use App\Imports\TasksImport;
use App\Models\Tasks;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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
            // $task = Tasks::create([
            //     'title' => $request->title,
            //     'description'=> $request->description,
            //     'completed'=> $request->completed,
            // ]);
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
                $tasks = Tasks::orderBy($request->sortBy, $request->desc == 'true' ? 'desc' : 'asc')->paginate($request->perPage ? $request->perPage : 10);
                return response()->json(['success' => true, 'data' => $tasks]);
            } else {
                $tasks = Tasks::where('user_id', Auth::user()->id)->orderBy($request->sortBy, $request->desc == 'true' ? 'desc' : 'asc')->paginate($request->perPage ? $request->perPage : 10);
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

    public function downloadTasks()
    {
        try {
            return Excel::download(new TasksExport(Auth::id(), $this->isAdmin), 'tasks.xlsx');
        } catch (\Exception $e) {
            Log::error('Error downloading tasks: ' . $e->getMessage());
            return response()->json(['status' => false, 'error' => 'Failed to download tasks.'], 500);
        }
    }

    public function importTasks(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx',
        ]);
        
        try {
            $file =  $request->file('file')->store('temp');
            Excel::import(new TasksImport, $file);
            return response()->json(['status' => true, 'message' => 'Tasks imported successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to import tasks.'], 500);
        }
    }

}
