@extends('layouts.warehouse')

@section('title', 'Warehouse Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #52b788;
    }

    .stat-card h3 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: #1b4332;
    }

    .stat-card p {
        color: #666;
        font-size: 0.9rem;
    }

    .tasks-section {
        margin-top: 2rem;
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .tasks-section h2 {
        margin-bottom: 1rem;
        color: #1b4332;
    }

    .task-list {
        list-style: none;
    }

    .task-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        border-left: 4px solid #52b788;
    }

    .task-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .temperature-section {
        margin-top: 2rem;
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .temperature-section h2 {
        margin-bottom: 1rem;
        color: #1b4332;
    }

    .temperature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .temp-card {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        border: 2px solid #e0e0e0;
    }

    .temp-card.normal {
        border-color: #52b788;
    }

    .temp-card.warning {
        border-color: #ffc107;
        background: #fff3cd;
    }

    .temp-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #1b4332;
    }

    .temp-label {
        font-size: 0.85rem;
        color: #666;
        margin-top: 0.5rem;
    }

    .activity-time {
        font-size: 0.85rem;
        color: #999;
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('content')
@endsection
