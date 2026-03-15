<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('thu_tu')->orderByDesc('created_at')->get();
        return view('admin.banners', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de'      => 'nullable|string|max:200',
            'mo_ta'        => 'nullable|string|max:500',
            'lien_ket'     => 'nullable|url|max:500',
            'vi_tri'       => 'required|in:hero,sidebar',
            'thu_tu'       => 'nullable|integer|min:0',
            'anh_file'     => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:3072',
            'link_anh'     => 'nullable|url|max:500',
        ], [
            'anh_file.image' => 'File phải là ảnh.',
            'anh_file.max'   => 'Ảnh không quá 3MB.',
            'link_anh.url'   => 'Link ảnh phải là URL hợp lệ.',
        ]);

        $data = $request->only(['tieu_de', 'mo_ta', 'lien_ket', 'vi_tri', 'link_anh']);
        $data['thu_tu']    = (int)($request->thu_tu ?? 0);
        $data['trang_thai'] = true;

        if ($request->hasFile('anh_file')) {
            $file = $request->file('anh_file');
            $name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $name);
            $data['duong_dan_anh'] = $name;
        }

        Banner::create($data);
        return back()->with('success', 'Đã thêm banner mới!');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'tieu_de'  => 'nullable|string|max:200',
            'mo_ta'    => 'nullable|string|max:500',
            'lien_ket' => 'nullable|url|max:500',
            'vi_tri'   => 'required|in:hero,sidebar',
            'thu_tu'   => 'nullable|integer|min:0',
            'anh_file' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:3072',
            'link_anh' => 'nullable|url|max:500',
        ]);

        $data = $request->only(['tieu_de', 'mo_ta', 'lien_ket', 'vi_tri', 'link_anh']);
        $data['thu_tu']    = (int)($request->thu_tu ?? 0);
        $data['trang_thai'] = $request->boolean('trang_thai', true);

        if ($request->hasFile('anh_file')) {
            // Xóa ảnh cũ
            if ($banner->duong_dan_anh && file_exists(public_path('uploads/banners/' . $banner->duong_dan_anh))) {
                unlink(public_path('uploads/banners/' . $banner->duong_dan_anh));
            }
            $file = $request->file('anh_file');
            $name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $name);
            $data['duong_dan_anh'] = $name;
        }

        $banner->update($data);
        return back()->with('success', 'Đã cập nhật banner!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        if ($banner->duong_dan_anh && file_exists(public_path('uploads/banners/' . $banner->duong_dan_anh))) {
            unlink(public_path('uploads/banners/' . $banner->duong_dan_anh));
        }
        $banner->delete();
        return back()->with('success', 'Đã xóa banner.');
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['trang_thai' => !$banner->trang_thai]);
        return back()->with('success', 'Đã cập nhật trạng thái banner.');
    }
}
