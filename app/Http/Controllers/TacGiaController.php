<?php

namespace App\Http\Controllers;

use App\Models\TacGia;
use Illuminate\Http\Request;

class TacGiaController extends Controller
{
    /**
     * Hiển thị danh sách đối tác (tác giả, NXB, NCC).
     *
     * For full page render we load all three collections so the tabs can
     * swap instantly without another round-trip. For AJAX search we only
     * query the currently active `tab` and return a JSON payload with a
     * rendered partial for that tab.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $tab    = $request->input('tab', 'tac-gia');
        $tab    = in_array($tab, ['tac-gia', 'nha-xuat-ban', 'nha-cung-cap'], true) ? $tab : 'tac-gia';

        // AJAX path: only hydrate the requested tab.
        if ($request->ajax() || $request->wantsJson()) {
            $rows = $this->queryPartner($tab, $search);

            return response()->json([
                'success' => true,
                'tab'     => $tab,
                'count'   => $rows->count(),
                'html'    => view('admin._partials.partners_rows', [
                    'tab'  => $tab,
                    'rows' => $rows,
                ])->render(),
            ]);
        }

        // Full page render — load all three (no filtering on first paint,
        // search is applied client-side via AJAX after the page loads).
        $tacGias     = TacGia::with('sachs')->orderBy('ten_tac_gia')->get();
        $nhaXuatBans = \App\Models\NhaXuatBan::with('sachs')->orderBy('ten_nxb')->get();
        $nhaCungCaps = \App\Models\NhaCungCap::with('sachs')->orderBy('ten_ncc')->get();

        return view('admin.partners', compact('tacGias', 'nhaXuatBans', 'nhaCungCaps'));
    }

    /**
     * Filter one of the three partner collections by name.
     */
    private function queryPartner(string $tab, string $search)
    {
        switch ($tab) {
            case 'nha-xuat-ban':
                $q = \App\Models\NhaXuatBan::with('sachs')->orderBy('ten_nxb');
                if ($search !== '') $q->where('ten_nxb', 'like', "%{$search}%");
                return $q->get();

            case 'nha-cung-cap':
                $q = \App\Models\NhaCungCap::with('sachs')->orderBy('ten_ncc');
                if ($search !== '') $q->where('ten_ncc', 'like', "%{$search}%");
                return $q->get();

            case 'tac-gia':
            default:
                $q = TacGia::with('sachs')->orderBy('ten_tac_gia');
                if ($search !== '') $q->where('ten_tac_gia', 'like', "%{$search}%");
                return $q->get();
        }
    }

    /**
     * Hiển thị form thêm tác giả.
     */
    public function create()
    {
        return view('admin.add-tac-gia');
    }

    /**
     * Lưu tác giả mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_tac_gia' => 'required|string|max:150|unique:tac_gia,ten_tac_gia',
        ], [
            'ten_tac_gia.required' => 'Tên tác giả là bắt buộc.',
            'ten_tac_gia.max'      => 'Tên tác giả không quá 150 ký tự.',
            'ten_tac_gia.unique'   => 'Tác giả này đã tồn tại.',
        ]);

        TacGia::create($validated);

        return redirect()
            ->route('admin.tac-gia.create')
            ->with('success', 'Thêm tác giả "' . $validated['ten_tac_gia'] . '" thành công!');
    }
}
