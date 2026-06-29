@extends('layouts.app')

@section('title', 'Blog Generator')
@section('page-title', 'Blog Generator')

@push('styles')
<style>
    .result-box {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        min-height: 200px;
        white-space: pre-wrap;
        line-height: 1.7;
        font-size: 0.95rem;
    }

    .loader {
        display: none;
        text-align: center;
        padding: 2rem 0;
    }

    .spinner-border {
        color: #6f6cff;
    }

    .history-item {
        cursor: pointer;
        transition: background 0.2s;
    }

    .history-item:hover {
        background: #f4f4ff;
    }
</style>
@endpush

@section('content')

    <div class="row g-4">
        <!-- Form -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-text-fill text-warning me-2"></i>Generate Blog Post</h6>

                    <form id="blogForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Topic</label>
                            <input type="text" name="topic" id="topic" class="form-control" placeholder="e.g. Benefits of remote work" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tone</label>
                            <select name="tone" id="tone" class="form-select" required>
                                <option value="Professional">Professional</option>
                                <option value="Casual">Casual</option>
                                <option value="Friendly">Friendly</option>
                                <option value="Persuasive">Persuasive</option>
                                <option value="Humorous">Humorous</option>
                                <option value="Informative">Informative</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Approx. Word Count</label>
                            <input type="number" name="word_count" id="word_count" class="form-control" value="500" min="100" max="2000" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="generateBtn">
                            <i class="bi bi-magic me-1"></i> Generate Blog Post
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recent History -->
            @if($histories->isNotEmpty())
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 small">Recent Blogs</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($histories as $item)
                        <div class="list-group-item history-item" onclick="loadHistory({{ $item->id }})">
                            <div class="small fw-semibold">{{ Str::limit($item->prompt, 40) }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->diffForHumans() }}</div>
                        </div>
                        <div id="history-data-{{ $item->id }}" class="d-none">{{ $item->response }}</div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Result -->
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0">Generated Content</h6>
                <button class="btn btn-sm btn-outline-secondary" id="copyBtn" style="display:none;">
                    <i class="bi bi-clipboard me-1"></i> Copy
                </button>
            </div>

            <div class="loader" id="loader">
                <div class="spinner-border" role="status"></div>
                <p class="text-muted mt-2 mb-0">Generating your blog post... please wait</p>
            </div>

            <div class="result-box" id="resultBox">
                <p class="text-muted text-center mt-5">Your generated blog post will appear here.</p>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const blogForm = document.getElementById('blogForm');
    const loader = document.getElementById('loader');
    const resultBox = document.getElementById('resultBox');
    const generateBtn = document.getElementById('generateBtn');
    const copyBtn = document.getElementById('copyBtn');

    blogForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const topic = document.getElementById('topic').value.trim();
        const tone = document.getElementById('tone').value;
        const wordCount = document.getElementById('word_count').value;

        if (!topic) return;

        loader.style.display = 'block';
        resultBox.style.display = 'none';
        copyBtn.style.display = 'none';
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Generating...';

        try {
            const response = await fetch("{{ route('blog.generate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ topic, tone, word_count: wordCount }),
            });

            const data = await response.json();

            if (data.success) {
                resultBox.textContent = data.result;
                copyBtn.style.display = 'inline-block';
            } else {
                resultBox.textContent = 'Error: ' + (data.message || 'Something went wrong.');
            }
        } catch (error) {
            resultBox.textContent = 'Network error. Please try again.';
        } finally {
            loader.style.display = 'none';
            resultBox.style.display = 'block';
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Generate Blog Post';
        }
    });

    copyBtn.addEventListener('click', function () {
        navigator.clipboard.writeText(resultBox.textContent);
        copyBtn.innerHTML = '<i class="bi bi-check2 me-1"></i> Copied!';
        setTimeout(() => {
            copyBtn.innerHTML = '<i class="bi bi-clipboard me-1"></i> Copy';
        }, 2000);
    });

    function loadHistory(id) {
        const data = document.getElementById('history-data-' + id).textContent;
        resultBox.textContent = data;
        resultBox.style.display = 'block';
        loader.style.display = 'none';
        copyBtn.style.display = 'inline-block';
    }
</script>
@endpush
