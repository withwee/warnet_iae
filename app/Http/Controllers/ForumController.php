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

        $forum = Forum::create([
            'user_id' => $user->id,
            'konten' => $request->konten,
            'gambar' => $gambarPath,
        ]);

        // Send notification to all users (except the post creator) when new post is created
        $allUsers = \App\Models\User::where('id', '!=', $user->id)->get();
        foreach ($allUsers as $recipient) {
            \App\Models\Notification::create([
                'user_id' => $recipient->id,
                'type' => 'forum',
                'message' => $user->name . ' membuat postingan baru di forum',
            ]);

            // Keep only 5 latest notifications
            $notifToDelete = \App\Models\Notification::where('user_id', $recipient->id)
                ->orderBy('created_at', 'desc')
                ->skip(5)
                ->take(PHP_INT_MAX)
                ->get();

            foreach ($notifToDelete as $notif) {
                $notif->delete();
            }
        }

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

        $comment = Comment::create([
            'forum_id' => $forumId,
            'user_id' => $user->id,
            'komentar' => $request->komentar,
        ]);

        // Send notification to post owner when someone replies
        $forum = Forum::find($forumId);
        if ($forum && $forum->user_id !== $user->id) {
            \App\Models\Notification::create([
                'user_id' => $forum->user_id,
                'type' => 'komentar',
                'message' => $user->name . ' mengomentari postingan Anda: "' . substr($request->komentar, 0, 50) . (strlen($request->komentar) > 50 ? '..."' : '"'),
            ]);

            // Keep only 5 latest notifications
            $notifToDelete = \App\Models\Notification::where('user_id', $forum->user_id)
                ->orderBy('created_at', 'desc')
                ->skip(5)
                ->take(PHP_INT_MAX)
                ->get();

            foreach ($notifToDelete as $notif) {
                $notif->delete();
            }
        }

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

    // API Methods
    public function indexApi()
    {
        $forums = Forum::with(['user', 'comments.user'])->latest()->get();
        return response()->json($forums);
    }

    public function storeApi(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        $gambarPath = $request->hasFile('gambar') 
            ? $request->file('gambar')->store('photos', 'public') 
            : null;

        $forum = Forum::create([
            'user_id' => $user->id,
            'konten' => $request->konten,
            'gambar' => $gambarPath,
        ]);

        return response()->json($forum->load(['user', 'comments.user']), 201);
    }

    public function replyApi(Request $request, $forumId)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'komentar' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        $comment = Comment::create([
            'forum_id' => $forumId,
            'user_id' => $user->id,
            'komentar' => $request->komentar,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    public function deletePostApi($id)
    {
        $forum = Forum::findOrFail($id);
        $user = auth()->user();

        if ($forum->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $forum->delete();

        return response()->json(null, 204);
    }

    public function deleteCommentApi($id)
    {
        $comment = Comment::findOrFail($id);
        $user = auth()->user();

        if ($comment->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(null, 204);
    }
}
