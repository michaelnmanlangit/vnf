<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffTaskController extends Controller
{
    /**
     * Display staff's assigned tasks
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get tasks assigned to current user
        $query = TaskAssignment::where('user_id', $user->id)
            ->with(['assignedBy', 'user']);
        
        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by task type
        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
        }
        
        // Order by priority and due date
        $tasks = $query->orderByRaw("
            CASE priority
                WHEN 'high' THEN 1
                WHEN 'medium' THEN 2
                WHEN 'low' THEN 3
            END
        ")
        ->orderBy('due_date', 'asc')
        ->paginate(15);
        
        // Calculate statistics
        $stats = [
            'total' => TaskAssignment::where('user_id', $user->id)->count(),
            'pending' => TaskAssignment::where('user_id', $user->id)->where('status', 'pending')->count(),
            'in_progress' => TaskAssignment::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'completed_today' => TaskAssignment::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'overdue' => TaskAssignment::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->where('due_date', '<', now())
                ->count(),
        ];
        
        return view('staff.tasks.index', compact('tasks', 'stats'));
    }
    
    /**
     * Show task details
     */
    public function show(TaskAssignment $task)
    {
        // Verify task belongs to authenticated user
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this task.');
        }
        
        return view('staff.tasks.show', compact('task'));
    }
    
    /**
     * Update task status (staff can only update status)
     */
    public function updateStatus(Request $request, TaskAssignment $task)
    {
        // Verify task belongs to authenticated user
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this task.');
        }
        
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(TaskAssignment::STATUSES)),
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Update status
        if ($validated['status'] === 'completed') {
            $task->markCompleted();
        } else {
            $task->status = $validated['status'];
            $task->save();
        }
        
        // Add staff notes if provided
        if ($request->filled('notes')) {
            $task->notes = ($task->notes ? $task->notes . "\n\n" : '') . 
                           "[" . now()->format('Y-m-d H:i') . "] " . 
                           Auth::user()->name . ": " . $validated['notes'];
            $task->save();
        }
        
        return redirect()->back()->with('success', 'Task status updated successfully!');
    }
    
    /**
     * Start working on a task (mark as in-progress)
     */
    public function start(TaskAssignment $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($task->status === 'pending') {
            $task->status = 'in_progress';
            $task->save();
            
            return redirect()->back()->with('success', 'Task started! Good luck!');
        }
        
        return redirect()->back()->with('error', 'Task cannot be started.');
    }
    
    /**
     * Mark task as completed
     */
    public function complete(TaskAssignment $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }
        
        $task->markCompleted();
        
        return redirect()->back()->with('success', 'Task completed! Well done!');
    }
}
