@extends('layouts.app')

@section('title', 'Product Description Generator')
@section('page-title', 'Product Description Generator')

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
                    <h6 class="fw-bold mb-3"><i class="bi bi-bag-check-fill text-info me-2"></i>Generate Product Description</h6>

                    <form id="productForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Product Name</label>
                            <input type="text" name="product_name" id="product_name" class="form-control" placeholder="e.g. Wireless Bluetooth Earbuds" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Key Features / Details</label>
                            <textarea name="features" id="features" class="form-control" rows="4" placeholder="e.g. Noise cancellation, 24hr battery, waterproof, touch controls" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Target Audience (optional)</label>
                            <input type="text" name="audience" id="audience" class="form-control" placeholder="e.g. Gym-goers, students, professionals">
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="generateBtn">
                            <i class="bi bi-magic me-1"></i> Generate Description
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recent History -->
            @if($histories->isNotEmpty())
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 small">Recent Products</h6>
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
                <h6 class="fw-bold mb-0">Generated Description</h6>
                <button class="btn btn-sm btn-outline-secondary" id="copyBtn" style="display:none;">
                    <i class="bi bi-clipboard me-1"></i> Copy
                </button>
            </div>

            <div class="loader" id="loader">
                <div class="spinner-border" role="status"></div>
                <p class="text-muted mt-2 mb-0">Generating product description... please wait</p>
            </div>

            <div class="result-box" id="resultBox">
                <p class="text-muted text-center mt-5">Your generated product description will appear here.</p>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const productForm = document.getElementById('productForm');
    const loader = document.getElementById('loader');
    const resultBox = document.getElementById('resultBox');
    const generateBtn = document.getElementById('generateBtn');
    const copyBtn = document.getElementById('copyBtn');

    productForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const product_name = document.getElementById('product_name').value.trim();
        const features = document.getElementById('features').value.trim();
        const audience = document.getElementById('audience').value.trim();

        if (!product_name || !features) return;

        loader.style.display = 'block';
        resultBox.style.display = 'none';
        copyBtn.style.display = 'none';
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Generating...';

        try {
            const response = await fetch("{{ route('product.generate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_name, features, audience }),
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
            generateBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Generate Description';
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
