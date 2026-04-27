<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // === DÀNH CHO USER ===
    public function create()
    {
        $categories = [
            'Review sách',
            'Tác giả',
            'Kiến thức',
            'Sự kiện',
            'Lifestyle',
            'Khác'
        ];
        return view('pages.blog-create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|string',
            'content'  => 'required|string',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // tăng lên 5MB
        ]);

        $slug      = Str::slug($request->title) . '-' . time();
        $imagePath = null;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file      = $request->file('image');
            $uploadDir = public_path('uploads/blogs/thumbnails');

            // Tạo thư mục nếu chưa tồn tại
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext      = $file->getClientOriginalExtension();
            $filename = time() . '-' . \Illuminate\Support\Str::random(8) . '.' . $ext;
            $file->move($uploadDir, $filename);
            $imagePath = 'uploads/blogs/thumbnails/' . $filename;
        }

        Post::create([
            'title'    => $request->title,
            'slug'     => $slug,
            'category' => $request->category,
            'image'    => $imagePath,
            'content'  => clean($request->content),
            'status'   => 'pending',
            'user_id'  => Auth::id(),
        ]);

        return redirect()->route('blog.index')->with('success', 'Bài viết đã được gửi và đang chờ duyệt!');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Validate file là ảnh
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            $filename = time() . '-' . $file->getClientOriginalName();
            // Lưu ảnh vào thư mục public/uploads/blogs
            $path = $file->move(public_path('uploads/blogs'), $filename);

            // Trả về URL để TinyMCE hiển thị
            return response()->json(['location' => asset('uploads/blogs/' . $filename)]);
        }

        return response()->json(['error' => 'Upload failed'], 400);
    }

    // === DÀNH CHO ADMIN ===
    public function adminIndex()
    {
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.blogs', compact('posts'));
    }

    public function approve(Post $post)
    {
        $post->update(['status' => 'published']);
        return back()->with('success', 'Đã duyệt bài viết!');
    }

    public function reject(Post $post)
    {
        $post->update(['status' => 'rejected']);
        return back()->with('success', 'Đã từ chối bài viết!');
    }

    public function destroy(Post $post)
    {
        // Xoá ảnh thumbnail nếu tồn tại (chỉ xoá file trong thư mục uploads/blogs để an toàn)
        if ($post->image && str_starts_with($post->image, 'uploads/blogs/')) {
            $imagePath = public_path($post->image);
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        $post->delete();
        return back()->with('success', 'Đã xoá bài viết vĩnh viễn!');
    }
}
