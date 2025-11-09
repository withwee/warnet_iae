@extends('layouts.user-layout')

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
            {{-- Tombol hapus post untuk admin atau pemilik post --}}
            @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->id === $forum->user_id))
                <form action="{{ auth()->user()->role === 'admin' ? route('admin.forum.deletePost', $forum->id) : route('forum.deletePost', $forum->id) }}" method="POST" onsubmit="return confirm('Yakin hapus post ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 ml-2">
                        <iconify-icon icon="mdi:delete"></iconify-icon>
                    </button>
                </form>
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
            <div class="flex gap-3 mb-2 items-center">
                <img src="{{ $comment->user->photo ? asset('storage/' . $comment->user->photo) . '?v=' . $comment->user->updated_at->timestamp : asset('images/Foto Profil.jpg') }}" 
                     alt="Profile Photo" 
                     class="w-8 h-8 rounded-full border border-gray-200 object-cover">
                <div>
                    <div class="bg-gray-100 p-2 rounded-lg">
                        <p class="font-semibold text-sm">{{ $comment->user->name }}</p>
                        <p>{{ $comment->komentar }}</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $comment->created_at->format('d M Y H:i') }}</p>
                </div>
                {{-- Tombol hapus komentar untuk admin atau pemilik komentar --}}
                @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->id === $comment->user_id))
                    <form action="{{ auth()->user()->role === 'admin' ? route('admin.forum.deleteComment', $comment->id) : route('forum.deleteComment', $comment->id) }}" method="POST" onsubmit="return confirm('Yakin hapus komentar ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 ml-2">
                            <iconify-icon icon="mdi:delete"></iconify-icon>
                        </button>
                    </form>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</section>
@endsection