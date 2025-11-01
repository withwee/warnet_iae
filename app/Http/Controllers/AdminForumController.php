<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Comment;
use Illuminate\Http\Request;

class AdminForumController extends Controller
{
    public function index()
    {
        $forums = Forum::with(['user', 'comments.user'])->latest()->get();
        return view('admin.forumAdmin', compact('forums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

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
        $forum->comments()->delete();
        $forum->delete();

        return redirect()->back()->with('message', 'Postingan berhasil dihapus.');
    }

    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->back()->with('message', 'Komentar berhasil dihapus.');
    }
}
