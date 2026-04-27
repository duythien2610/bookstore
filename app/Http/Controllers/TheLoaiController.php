<?php

namespace App\Http\Controllers;

use App\Models\TheLoai;
use Illuminate\Http\Request;

class TheLoaiController extends Controller
{
    /**
     * Hiển thị danh sách thể loại dạng cây.
     *
     * Supports a `search` query parameter that filters both the parent
     * categories and their children by name. When filtering, a parent
     * group is kept whenever itself OR any of its children match.
     *
     * When called via AJAX we return a JSON payload containing the
     * pre-rendered groups partial so the js-admin-search helper in
     * layouts/admin.blade.php can swap the table body seamlessly.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $query = TheLoai::with(['children.sachs', 'sachs'])
                    ->whereNull('parent_id')
                    ->orderBy('ten_the_loai');

        if ($search !== '') {
            // Keep parents that match themselves OR whose children match.
            $query->where(function ($q) use ($search) {
                $q->where('ten_the_loai', 'like', "%{$search}%")
                  ->orWhereHas('children', function ($c) use ($search) {
                      $c->where('ten_the_loai', 'like', "%{$search}%");
                  });
            });
        }

        $theLoaiChas = $query->get();

        // When searching, also narrow down the visible children in each
        // parent so the UI doesn't show unrelated sub-categories.
        if ($search !== '') {
            $needle = mb_strtolower($search);
            $theLoaiChas = $theLoaiChas->map(function ($parent) use ($needle) {
                $parentMatches = mb_stripos($parent->ten_the_loai, $needle) !== false;
                if (!$parentMatches) {
                    $parent->setRelation('children', $parent->children->filter(
                        fn ($c) => mb_stripos($c->ten_the_loai, $needle) !== false
                    )->values());
                }
                return $parent;
            });
        }

        $tongCon = TheLoai::whereNotNull('parent_id')->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count'   => $theLoaiChas->count(),
                'html'    => view('admin._partials.the_loai_rows', compact('theLoaiChas'))->render(),
            ]);
        }

        return view('admin.categories', compact('theLoaiChas', 'tongCon'));
    }

    /**
     * Hiển thị form thêm thể loại.
     */
    public function create()
    {
        // Lấy danh mục cha để hiển thị trong select
        $theLoaiChas = TheLoai::whereNull('parent_id')
                        ->orderBy('ten_the_loai')
                        ->get();

        return view('admin.add-the-loai', compact('theLoaiChas'));
    }

    /**
     * Lưu thể loại mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_the_loai' => 'required|string|max:150',
            'parent_id'    => 'nullable|exists:the_loai,id',
        ], [
            'ten_the_loai.required' => 'Tên thể loại là bắt buộc.',
            'ten_the_loai.max'      => 'Tên thể loại không quá 150 ký tự.',
            'parent_id.exists'      => 'Danh mục cha không hợp lệ.',
        ]);

        TheLoai::create($validated);

        return redirect()
            ->route('admin.the-loai.create')
            ->with('success', 'Thêm thể loại "' . $validated['ten_the_loai'] . '" thành công!');
    }
}
