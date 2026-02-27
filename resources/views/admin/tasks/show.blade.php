@extends('layouts.admin')

@section('title', 'Work Details')

@section('page-title', 'Work Assignment Details')

@section('styles')
<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">
@endsection

@section('content')
<div class="billing-container">
    <!-- Header Navigation -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('admin.tasks.index') }}" class="manage-customers-btn" style="background: #6c757d;">Back to Work Assignments</a>
        
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.tasks.edit', $task) }}" class="btn-submit" style="background: #f39c12;">Edit</a>
            <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-cancel" style="background: #e74c3c; color: white;">Delete</button>
            </form>
        </div>
    </div>

    <!-- Task Details Card -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 2rem;">
        <!-- Task Title & Status -->
        <div style="margin-bottom: 2rem; border-bottom: 2px solid #e8ecef; padding-bottom: 1.5rem;">
            <h2 style="margin: 0 0 1rem 0; font-size: 1.75rem; color: #2c3e50;">{{ $task->title }}</h2>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                @if($task->status === 'completed')
                    <span class="status-badge" style="background: #27ae60; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600;">Completed</span>
                @elseif($task->status === 'in_progress')
                    <span class="status-badge" style="background: #3498db; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600;">In Progress</span>
                @else
                    <span class="status-badge" style="background: #95a5a6; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600;">Pending</span>
                @endif
                

            </div>
        </div>

        <!-- Task Information Grid -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-bottom: 2rem;">
            <div>
                <h4 style="margin: 0 0 0.5rem 0; color: #7f8c8d; font-size: 0.875rem; text-transform: uppercase; font-weight: 600;">Task Type</h4>
                <p style="margin: 0; font-size: 1rem; color: #2c3e50;">{{ \App\Models\TaskAssignment::TASK_TYPES[$task->task_type] }}</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-bottom: 2rem;">
            <div>
                <h4 style="margin: 0 0 0.5rem 0; color: #7f8c8d; font-size: 0.875rem; text-transform: uppercase; font-weight: 600;">Assigned To</h4>
                <p style="margin: 0; font-size: 1rem; color: #2c3e50;">
                    {{ $task->user->name }}<br>
                    <small style="color: #7f8c8d;">{{ ucfirst(str_replace('_', ' ', $task->user->role)) }}</small>
                    @if($employee)
                        <br><small style="color: #7f8c8d;">{{ ucfirst($employee->department) }} Department</small>
                    @endif
                </p>
            </div>
            <div>
                <h4 style="margin: 0 0 0.5rem 0; color: #7f8c8d; font-size: 0.875rem; text-transform: uppercase; font-weight: 600;">Assigned By</h4>
                <p style="margin: 0; font-size: 1rem; color: #2c3e50;">
                    {{ $task->assignedBy->name }}<br>
                    <small style="color: #7f8c8d;">{{ $task->created_at->format('M d, Y h:i A') }}</small>
                </p>
            </div>
        </div>

        @if($task->description)
            <div style="margin-bottom: 2rem; padding-top: 1.5rem; border-top: 1px solid #e8ecef;">
                <h4 style="margin: 0 0 0.75rem 0; color: #2c3e50; font-size: 1.125rem;">Description</h4>
                <p style="margin: 0; line-height: 1.6; color: #555;">{{ $task->description }}</p>
            </div>
        @endif

        @if($task->notes)
            <div style="margin-bottom: 2rem; padding-top: 1.5rem; border-top: 1px solid #e8ecef;">
                <h4 style="margin: 0 0 0.75rem 0; color: #2c3e50; font-size: 1.125rem;">Additional Notes</h4>
                <p style="margin: 0; line-height: 1.6; color: #555;">{{ $task->notes }}</p>
            </div>
        @endif

        @if($task->completed_at)
            <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 1rem; margin-top: 1.5rem;">
                <strong style="color: #155724;">Task Completed:</strong> 
                <span style="color: #155724;">{{ $task->completed_at->format('M d, Y h:i A') }}</span>
            </div>
        @endif

        <!-- Quick Status Update -->
        @if($task->status !== 'completed')
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e8ecef;">
                <h4 style="margin: 0 0 1rem 0; color: #2c3e50; font-size: 1.125rem;">Quick Status Update</h4>
                <form action="{{ route('admin.tasks.updateStatus', $task) }}" method="POST" style="display: flex; gap: 1rem; align-items: center;">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-control" style="max-width: 250px; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                        @foreach(\App\Models\TaskAssignment::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ $task->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-submit">Update Status</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
