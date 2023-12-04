<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function createTask(Request $request){
        
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'title' => 'nullable|string',
            'description'=>'nullable|string',
        ]);

        $task = Tasks::create($request->all());
        return response()->json($task, 201);

    }

    public function getAllTasks(){
        $tasks = Tasks::all();
        return response()->json($tasks);
    }

    public function getTask($id){
        $task = Tasks::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        return response()->json($task);
    }

    public function updateTask(Request $request, $id){
        $task = Tasks::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->update($request->all());
        return response()->json($task);
    }

    public function deleteTask($id){
        $task = Tasks::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
