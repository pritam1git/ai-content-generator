@extends('layouts.app')

@section('title', 'AI Chat Assistant')
@section('page-title', 'AI Chat Assistant')

@push('styles')
<style>
    .chat-box {
        height: 65vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 12px;
        padding: 1.2rem;
    }

    .msg-row {
        display: flex;
        margin-bottom: 1rem;
    }

    .msg-row.user {
        justify-content: flex-end;
    }

    .msg-bubble {
        max-width: 70%;
        padding: 0.7rem 1rem;
        border-radius: 14px;
        font-size: 0.92rem;
        line-height: 1.5;
        white-space: pre-wrap;
    }

    .msg-row.user .msg-bubble {
        background: #6f6cff;
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .msg-row.ai .msg-bubble {
        background: #f1f1f7;
        color: #222;
        border-bottom-left-radius: 4px;
    }

    .typing-indicator span {
        display: inline-block;
        width: 6px;
        height: 6px;
        margin-right: 3px;
        background: #999;
        border-radius: 50%;
        animation: blink 1.4s infinite both;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes blink {
        0%, 80%, 100% { opacity: 0.2; }
        40% { opacity: 1; }
    }
</style>
@endpush

@section('content')

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="chat-box" id="chatBox">
                @forelse($histories as $item)
                    <div class="msg-row user">
                        <div class="msg-bubble">{{ $item->prompt }}</div>
                    </div>
                    <div class="msg-row ai">
                        <div class="msg-bubble">{{ $item->response }}</div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-dots fs-1"></i>
                        <p class="mt-2">Start a conversation with your AI assistant!</p>
                    </div>
                @endforelse
            </div>

            <form id="chatForm" class="d-flex gap-2 mt-3">
                @csrf
                <input
                    type="text"
                    id="messageInput"
                    class="form-control"
                    placeholder="Type your message..."
                    autocomplete="off"
                    required
                >
                <button type="submit" class="btn btn-primary px-4" id="sendBtn">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const chatForm = document.getElementById('chatForm');
    const chatBox = document.getElementById('chatBox');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function appendMessage(role, text) {
        const row = document.createElement('div');
        row.className = `msg-row ${role}`;
        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble';
        bubble.textContent = text;
        row.appendChild(bubble);
        chatBox.appendChild(row);
        scrollToBottom();
        return row;
    }

    function showTyping() {
        const row = document.createElement('div');
        row.className = 'msg-row ai';
        row.id = 'typingRow';
        row.innerHTML = `<div class="msg-bubble typing-indicator"><span></span><span></span><span></span></div>`;
        chatBox.appendChild(row);
        scrollToBottom();
    }

    function removeTyping() {
        const el = document.getElementById('typingRow');
        if (el) el.remove();
    }

    chatForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        appendMessage('user', message);
        messageInput.value = '';
        sendBtn.disabled = true;
        showTyping();

        try {
            const response = await fetch("{{ route('chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: message }),
            });

            const data = await response.json();
            removeTyping();

            if (data.success) {
                appendMessage('ai', data.reply);
            } else {
                appendMessage('ai', 'Error: ' + (data.message || 'Something went wrong.'));
            }
        } catch (error) {
            removeTyping();
            appendMessage('ai', 'Network error. Please try again.');
        } finally {
            sendBtn.disabled = false;
            messageInput.focus();
        }
    });

    scrollToBottom();
</script>
@endpush
