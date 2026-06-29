@extends('layouts.app')

@section('title', 'Prompt History')
@section('page-title', 'Prompt History')

@push('styles')
<style>
    .history-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.2rem 1.5rem;
        margin-bottom: 1rem;
    }

    .response-preview {
        max-height: 100px;
        overflow: hidden;
        position: relative;
        white-space: pre-wrap;
        font-size: 0.88rem;
        color: #555;
    }

    .response-preview.expanded {
        max-height: none;
    }

    .toggle-link {
        font-size: 0.8rem;
        cursor: pointer;
        color: #6f6cff;
    }

    .filter-pills .nav-link {
        border-radius: 20px;
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        color: #555;
        margin-right: 0.5rem;
    }

    .filter-pills .nav-link.active {
        background: #6f6cff;
        color: #fff;
    }
</style>
@endpush

@section('content')

    <!-- Filter Pills -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <ul class="nav filter-pills mb-0">
            <li class="nav-item">
                <a class="nav-link {{ !$type ? 'active' : '' }} bg-white shadow-sm" href="{{ route('history.index') }}">
                    All
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $type == 'chat' ? 'active' : '' }} bg-white shadow-sm" href="{{ route('history.index', ['type' => 'chat']) }}">
                    <i class="bi bi-chat-dots-fill me-1"></i> Chat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $type == 'blog' ? 'active' : '' }} bg-white shadow-sm" href="{{ route('history.index', ['type' => 'blog']) }}">
                    <i class="bi bi-file-earmark-text-fill me-1"></i> Blog
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $type == 'product' ? 'active' : '' }} bg-white shadow-sm" href="{{ route('history.index', ['type' => 'product']) }}">
                    <i class="bi bi-bag-check-fill me-1"></i> Product
                </a>
            </li>
        </ul>
    </div>

    <!-- History List -->
    @forelse($histories as $item)
        <div class="history-card shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge mb-2
                        @if($item->type == 'chat') bg-success
                        @elseif($item->type == 'blog') bg-warning text-dark
                        @else bg-info @endif">
                        {{ ucfirst($item->type) }}
                    </span>
                    <h6 class="fw-bold mb-0">{{ Str::limit($item->prompt, 80) }}</h6>
                    <span class="text-muted small">{{ $item->created_at->format('d M Y, h:i A') }}</span>
                </div>

                <form action="{{ route('history.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this entry?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash3"></i>
                    </button>
                </form>
            </div>

            <hr class="my-2">

            <div class="response-preview" id="response-{{ $item->id }}">{{ $item->response }}</div>

            @if(strlen($item->response) > 200)
                <span class="toggle-link" onclick="toggleResponse({{ $item->id }})" id="toggle-link-{{ $item->id }}">
                    Show more <i class="bi bi-chevron-down"></i>
                </span>
            @endif
        </div>
    @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mt-2">No history found{{ $type ? ' for "'.ucfirst($type).'"' : '' }}.</p>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="mt-3">
        {{ $histories->links() }}
    </div>

@endsection

@push('scripts')
<script>
    function toggleResponse(id) {
        const el = document.getElementById('response-' + id);
        const link = document.getElementById('toggle-link-' + id);

        el.classList.toggle('expanded');

        if (el.classList.contains('expanded')) {
            link.innerHTML = 'Show less <i class="bi bi-chevron-up"></i>';
        } else {
            link.innerHTML = 'Show more <i class="bi bi-chevron-down"></i>';
        }
    }
</script>
@endpush
