@extends('layouts.app')

@section('title', 'Forum')

@section('content')
<section class="space-y-4">
    <!-- Form untuk membuat postingan baru -->
    <div class="bg-white p-4 rounded-xl shadow">
        <form action="{{ route('forum.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ (isset($user) && $user->photo) ? asset('storage/' . $user->photo) . '?v=' . time() : asset('images/Foto Profil.jpg') }}" 
                     alt="Profile Photo" 
                     class="w-10 h-10 rounded-full border-2 border-gray-200 object-cover">
                
                <div class="flex-1 relative">
                    <textarea 
                           name="konten" 
                           placeholder="Ketik forum yang mau dibuat disini..." 
                           rows="2"
                           class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
            </div>
            
            <!-- Preview Gambar -->
            <div id="imagePreview" class="hidden mt-3">
                <img id="previewImg" src="" alt="Preview" class="max-h-40 rounded-lg">
                <button type="button" onclick="removeImage()" class="text-red-500 text-sm mt-2">Hapus Gambar</button>
            </div>
            
            <div class="flex items-center justify-between mt-3">
                <label for="gambarInput" class="flex items-center gap-2 cursor-pointer text-gray-600 hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm">Tambah Gambar</span>
                    <input type="file" id="gambarInput" name="gambar" accept="image/*" class="hidden" onchange="previewImage(this)">
                </label>
                
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-full flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span>Posting</span>
                </button>
            </div>
            
            <script>
            function previewImage(input) {
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            
            function removeImage() {
                document.getElementById('gambarInput').value = '';
                document.getElementById('imagePreview').classList.add('hidden');
            }
            </script>
        </form>
    </div>

    <!-- Daftar postingan forum -->
    @foreach($forums as $forum)
    <div class="bg-white p-4 rounded-xl shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3 ">
                <img src="{{ $forum->user->photo ? asset('storage/' . $forum->user->photo) . '?v=' . $forum->user->updated_at->timestamp : asset('images/Foto Profil.jpg') }}" 
                     alt="Profile Photo" 
                     class="w-10 h-10 rounded-full border-2 border-gray-200 object-cover">
                <div>
                    <h1 class="font-bold">{{ $forum->user->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $forum->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            {{-- Menu dropdown untuk admin atau pemilik post --}}
            @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->id === $forum->user_id))
                <div class="relative">
                    <button onclick="toggleDropdown('dropdown-post-{{ $forum->id }}')" class="text-gray-500 hover:text-gray-700 p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                        </svg>
                    </button>
                    <div id="dropdown-post-{{ $forum->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                        <form id="deletePostForm-{{ $forum->id }}" action="{{ auth()->user()->role === 'admin' ? route('admin.forum.deletePost', $forum->id) : route('forum.deletePost', $forum->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="showDeleteModal(document.getElementById('deletePostForm-{{ $forum->id }}'), 'Apakah Anda yakin ingin menghapus postingan ini?')" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg flex items-center gap-2">
                                <iconify-icon icon="mdi:delete"></iconify-icon>
                                <span>Hapus Postingan</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        
        <p class="mb-3">{{ $forum->konten }}</p>
        
        @if($forum->gambar)
        <img src="{{ asset('storage/' . $forum->gambar) }}" alt="Forum Image" class="mb-3 rounded-lg w-full h-80 object-contain">
        @endif
        
        <div class="border-t border-gray-200 pt-3">
            <!-- Form komentar -->
            <form action="{{ route('forum.reply', $forum->id) }}" method="POST" class="flex gap-2 mb-3">
                @csrf
                <input type="text" 
                       name="komentar" 
                       placeholder="Tulis komentar..." 
                       class="flex-1 rounded-full px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-full transition-colors">
                    Balas
                </button>
            </form>
            
            <!-- Daftar komentar -->
            @foreach($forum->comments as $comment)
            <div class="flex gap-3 mb-2 items-start">
                <img src="{{ $comment->user->photo ? asset('storage/' . $comment->user->photo) . '?v=' . $comment->user->updated_at->timestamp : asset('images/Foto Profil.jpg') }}" 
                     alt="Profile Photo" 
                     class="w-8 h-8 rounded-full border border-gray-200 object-cover">
                <div class="flex-1">
                    <div class="bg-gray-100 p-2 rounded-lg">
                        <p class="font-semibold text-sm">{{ $comment->user->name }}</p>
                        <p>{{ $comment->komentar }}</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $comment->created_at->format('d M Y H:i') }}</p>
                </div>
                {{-- Menu dropdown untuk admin atau pemilik komentar --}}
                @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->id === $comment->user_id))
                    <div class="relative">
                        <button onclick="toggleDropdown('dropdown-comment-{{ $comment->id }}')" class="text-gray-500 hover:text-gray-700 p-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                        <div id="dropdown-comment-{{ $comment->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                            <form id="deleteCommentForm-{{ $comment->id }}" action="{{ auth()->user()->role === 'admin' ? route('admin.forum.deleteComment', $comment->id) : route('forum.deleteComment', $comment->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="showDeleteModal(document.getElementById('deleteCommentForm-{{ $comment->id }}'), 'Apakah Anda yakin ingin menghapus komentar ini?')" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg flex items-center gap-2">
                                    <iconify-icon icon="mdi:delete"></iconify-icon>
                                    <span>Hapus Komentar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</section>

<!-- Modal Konfirmasi Delete -->
<div id="deleteModal" class="hidden fixed inset-0 bg-white bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl px-8 py-6 mx-4 transform transition-all duration-300 scale-95 opacity-0" id="deleteModalCard">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-3 px-4">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-600 mb-6 px-6" id="deleteMessage">Apakah Anda yakin ingin menghapus ini?</p>
            <div class="flex gap-3 justify-center pt-2">
                <button onclick="closeDeleteModal()" class="px-10 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl transition-all duration-200 text-base">
                    Batal
                </button>
                <button onclick="confirmDelete()" class="px-10 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg text-base">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteFormToSubmit = null;

function toggleDropdown(dropdownId) {
    // Tutup semua dropdown lainnya
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== dropdownId) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Toggle dropdown yang diklik
    const dropdown = document.getElementById(dropdownId);
    dropdown.classList.toggle('hidden');
}

// Tutup dropdown ketika klik di luar
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

function showDeleteModal(form, message) {
    deleteFormToSubmit = form;
    document.getElementById('deleteMessage').textContent = message;
    const modal = document.getElementById('deleteModal');
    const card = document.getElementById('deleteModalCard');
    
    modal.classList.remove('hidden');
    
    // Trigger animation
    setTimeout(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const card = document.getElementById('deleteModalCard');
    
    // Animate out
    card.classList.remove('scale-100', 'opacity-100');
    card.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
    
    deleteFormToSubmit = null;
}

function confirmDelete() {
    if (deleteFormToSubmit) {
        deleteFormToSubmit.submit();
    }
    closeDeleteModal();
}

// Tutup modal ketika klik di luar
document.getElementById('deleteModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeDeleteModal();
    }
});
</script>
@endsection