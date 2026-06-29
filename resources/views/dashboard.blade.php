@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <!-- Welcome -->
    <div class="mb-4">
        <h4 class="fw-bold">Welcome back, {{ Auth::user()->name }} 👋</h4>
        <p class="text-muted">Here's a quick overview of your AI content activity.</p>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-stars fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small">Total Prompts</h6>
                        <h4 class="fw-bold mb-0">{{ $totalPrompts }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-chat-dots-fill fs-4 text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small">Chat Conversations</h6>
                        <h4 class="fw-bold mb-0">{{ $chatCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="bi bi-file-earmark-text-fill fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small">Blogs Generated</h6>
                        <h4 class="fw-bold mb-0">{{ $blogCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="bi bi-bag-check-fill fs-4 text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small">Product Descriptions</h6>
                        <h4 class="fw-bold mb-0">{{ $productCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h6 class="fw-bold text-muted">Quick Actions</h6>
        </div>
        <div class="col-md-4">
            <a href="{{ route('chat.index') }}" class="card text-decoration-none shadow-sm border-0 h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-chat-dots-fill fs-1 text-primary mb-2"></i>
                    <h6 class="fw-bold mb-0">Start AI Chat</h6>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('blog.index') }}" class="card text-decoration-none shadow-sm border-0 h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-file-earmark-text-fill fs-1 text-warning mb-2"></i>
                    <h6 class="fw-bold mb-0">Generate Blog Post</h6>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('product.index') }}" class="card text-decoration-none shadow-sm border-0 h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-bag-check-fill fs-1 text-info mb-2"></i>
                    <h6 class="fw-bold mb-0">Generate Product Description</h6>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent History -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Recent Activity</h6>
            <a href="{{ route('history.index') }}" class="small text-decoration-none">View All</a>
        </div>
        <div class="card-body p-0">
            @if($recentHistory->isEmpty())
                <p class="text-muted text-center py-4 mb-0">No activity yet. Start generating content!</p>
            @else
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Prompt</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentHistory as $item)
                            <tr>
                                <td>
                                    <span class="badge
                                        @if($item->type == 'chat') bg-success
                                        @elseif($item->type == 'blog') bg-warning text-dark
                                        @else bg-info @endif">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($item->prompt, 50) }}</td>
                                <td class="text-muted small">{{ $item->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

@endsection
