<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Comment;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $forums = Forum::with(['user', 'comments.user'])->latest()->get();
        return view('forum', compact('forums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Silakan login terlebih dahulu.']);
        }

        $gambarPath = $request->hasFile('gambar') 
            ? $request->file('gambar')->store('photos', 'public') 
            : null;

        Forum::create([
            'user_id' => $user->id,
            'konten' => $request->konten,
            'gambar' => $gambarPath,
        ]);

        return redirect()->back()->with('message', 'Forum berhasil diposting.');
    }
    public function reply(Request $request, $forumId)
    {
        $request->validate([
            'komentar' => 'required|string',
        ]);

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Silakan login terlebih dahulu.']);
        }

        Comment::create([
            'forum_id' => $forumId,
            'user_id' => $user->id,
            'komentar' => $request->komentar,
        ]);

        return redirect()->back()->with('message', 'Komentar berhasil ditambahkan.');
    }

    public function deletePost($id)
    {
        $forum = Forum::findOrFail($id);
        $user = auth()->user();

        // User bisa hapus post mereka sendiri, atau admin bisa hapus semua
        if ($forum->user_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $forum->delete();

        return redirect()->route('forum')->with('success', 'Postingan berhasil dihapus.');
    }

    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $user = auth()->user();

        // User bisa hapus comment mereka sendiri, atau admin bisa hapus semua
        if ($comment->user_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}
