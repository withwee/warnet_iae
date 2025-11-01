<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

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

    $token = JWTAuth::getToken() ?? session('jwt_token');
    $user = JWTAuth::setToken($token)->authenticate();

    if (!$user) {
        return redirect()->route('login.view')->withErrors(['error' => 'Silakan login terlebih dahulu.']);
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
public function destroy($id)
{
    $forum = Forum::findOrFail($id);

    // Pastikan hanya admin yang bisa menghapus
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }

    $forum->delete();

    return redirect()->route('forum')->with('success', 'Postingan berhasil dihapus.');
}

}
