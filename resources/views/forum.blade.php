<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
</head>
<body>
@extends('layouts.app')

@section('content')
<section class="space-y-4 p-4">
    <!-- Form untuk membuat postingan baru -->
    <div class="bg-white p-4 rounded-xl shadow">
        <form action="{{ route('forum.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/profile.png') }}" 
                     alt="Profile Photo" 
                     class="w-10 h-10 rounded-full border-2 border-gray-200 object-cover">
                
                <div class="flex-1 relative">
                    <input type="text" 
                           name="konten" 
                           placeholder="Ketik forum yang mau dibuat disini..." 
                           class="w-full rounded-full px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" class="text-blue-500 hover:text-blue-700">
                    <iconify-icon icon="mdi:send" class="text-2xl"></iconify-icon>
                </button>
            </div>
            
            <div class="flex flex-col items-center justify-center gap-2">
                <label for="gambar" class="flex flex-col items-center justify-center gap-2 cursor-pointer bg-blue-500 p-3 rounded-lg text-white">
                    <div class="flex items-center gap-3">
                        <iconify-icon icon="mdi:image-plus"></iconify-icon>
                            <span>Tambah Gambar</span>
                    </div>
                    <input type="file" id="gambar" name="gambar" class="hidden" accept="image/*" onchange="previewImage(this)">
                </label>
                <div id="image-preview" class="hidden">
        <img id="preview" class="h-16 w-16 object-cover rounded">
    </div>
            </div>
        </form>
    </div>

    <!-- Daftar postingan forum -->
    @foreach($forums as $forum)
    <div class="bg-white p-4 rounded-xl shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3 ">
                <img src="{{ $forum->user->photo ? asset('storage/' . $forum->user->photo) : asset('images/profile.png') }}" 
                     alt="Profile Photo" 
                     class="w-10 h-10 rounded-full border-2 border-gray-200 object-cover">
                <div>
                    <h1 class="font-bold">{{ $forum->user->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $forum->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            {{-- Tombol hapus post untuk admin --}}
            @if(auth()->user() && auth()->user()->role === 'admin')
                <form action="{{ route('admin.forum.deletePost', $forum->id) }}" method="POST" onsubmit="return confirm('Yakin hapus post ini?')">
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
            <form action="{{ route('comment.store', $forum->id) }}" method="POST" class="flex gap-2 mb-3">
                @csrf
                <input type="text" 
                       name="komentar" 
                       placeholder="Tulis komentar..." 
                       class="flex-1 rounded-full px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="text-blue-500 hover:text-blue-700">
                    <iconify-icon icon="mdi:send" class="text-2xl"></iconify-icon>
                </button>
            </form>
            
            <!-- Daftar komentar -->
            @foreach($forum->comments as $comment)
            <div class="flex gap-3 mb-2 items-center">
                <img src="{{ $comment->user->photo ? asset('storage/' . $comment->user->photo) : asset('images/profile.png') }}" 
                     alt="Profile Photo" 
                     class="w-8 h-8 rounded-full border border-gray-200 object-cover">
                <div>
                    <div class="bg-gray-100 p-2 rounded-lg">
                        <p class="font-semibold text-sm">{{ $comment->user->name }}</p>
                        <p>{{ $comment->komentar }}</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $comment->created_at->format('d M Y H:i') }}</p>
                </div>
                {{-- Tombol hapus komentar untuk admin --}}
                @if(auth()->user() && auth()->user()->role === 'admin')
                    <form action="{{ route('admin.forum.deleteComment', $comment->id) }}" method="POST" onsubmit="return confirm('Yakin hapus komentar ini?')">
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
<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>