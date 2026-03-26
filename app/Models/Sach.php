<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sach extends Model
{
    use HasFactory;
    protected $table = 'sach';
    protected $fillable = [
        'tieu_de', 'tac_gia_id', 'nha_xuat_ban_id', 'nha_cung_cap_id',
        'the_loai_id', 'hinh_thuc_bia', 'mo_ta', 'file_anh_bia',
        'link_anh_bia', 'gia_ban', 'gia_goc', 'so_luong_ton',
        'isbn', 'nam_xuat_ban', 'so_trang','loai_sach'
    ];

    protected $casts = [
        'gia_ban' => 'decimal:2',
        'gia_goc' => 'decimal:2',
    ];

    public function tacGia()
    {
        return $this->belongsTo(TacGia::class, 'tac_gia_id');
    }

    public function nhaXuatBan()
    {
        return $this->belongsTo(NhaXuatBan::class, 'nha_xuat_ban_id');
    }

    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'nha_cung_cap_id');
    }

    public function theLoai()
    {
        return $this->belongsTo(TheLoai::class, 'the_loai_id');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'sach_id');
    }

    // Tính trung bình sao tiện lợi
    public function trungBinhSao(): float
    {
        return $this->danhGias()->avg('so_sao') ?? 0;
    }

    // Kiểm tra còn hàng
    public function conHang(): bool
    {
        return $this->so_luong_ton > 0;
    }

    // =========================================================================
    //  LOCAL SCOPES
    // =========================================================================

    /**
     * Scope dùng chung cho việc lọc
     * - the_loai_id: tự động mở rộng sang các danh mục con nếu click vào danh mục cha
     * - search: tìm theo tên sách, ISBN, tác giả, nhà xuất bản, nhà cung cấp
     */
    public function scopeFilter($query, array $filters)
    {
        // 1. Tìm kiếm nâng cao: sách, ISBN, tác giả, NXB, nhà cung cấp
        $query->when($filters['search'] ?? false, function ($q, $search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('sach.tieu_de', 'like', "%{$search}%")
                      ->orWhere('sach.isbn', 'like', "%{$search}%")
                      ->orWhereHas('tacGia', fn($tg) => $tg->where('ten_tac_gia', 'like', "%{$search}%"))
                      ->orWhereHas('nhaXuatBan', fn($nxb) => $nxb->where('ten_nxb', 'like', "%{$search}%"))
                      ->orWhereHas('nhaCungCap', fn($ncc) => $ncc->where('ten_ncc', 'like', "%{$search}%"));
            });
        });

        // 2. Lọc theo thể loại — hỗ trợ mảng nhiều category (chọn nhiều)
        $query->when($filters['the_loai_id'] ?? false, function ($q, $categoryIds) {
            if (!is_array($categoryIds)) {
                $categoryIds = [$categoryIds];
            }

            // Lấy tất cả danh mục con của các danh mục được chọn
            $childIds = DB::table('the_loai')
                ->whereIn('parent_id', $categoryIds)
                ->pluck('id')
                ->toArray();

            $allIds = array_unique(array_merge($categoryIds, $childIds));
            $q->whereIn('sach.the_loai_id', $allIds);
        });

        // 3. Lọc theo loại sách (trong nước / nước ngoài)
        $query->when($filters['loai_sach'] ?? false, fn($q, $v) => $q->where('sach.loai_sach', $v));

        // 4. Khoảng giá
        $query->when($filters['gia_min'] ?? false, fn($q, $v) => $q->where('sach.gia_ban', '>=', $v));
        $query->when($filters['gia_max'] ?? false, fn($q, $v) => $q->where('sach.gia_ban', '<=', $v));

        // 5. Lọc trạng thái tồn kho (admin)
        $query->when($filters['trang_thai'] ?? false, function ($q, $trang_thai) {
            if ($trang_thai === 'con_hang') $q->where('sach.so_luong_ton', '>', 0);
            elseif ($trang_thai === 'het_hang') $q->where('sach.so_luong_ton', 0);
        });

        return $query;
    }

    /**
     * Scope hỗ trợ sắp xếp sản phẩm
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $sortType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortType($query, $sortType = 'moi_nhat')
    {
        switch ($sortType) {
            case 'gia_tang':
                return $query->orderBy('gia_ban', 'asc');
            case 'gia_giam':
                return $query->orderBy('gia_ban', 'desc');
            case 'ten_az':
                return $query->orderBy('tieu_de', 'asc');
            default: // 'moi_nhat'
                return $query->orderByDesc('created_at');
        }
    }
}
