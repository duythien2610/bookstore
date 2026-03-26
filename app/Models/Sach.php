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

    public function chiTiets()
    {
        return $this->hasMany(DonHangChiTiet::class, 'sach_id');
    }

    // Lấy top sách bán chạy (đơn giản hóa logic)
    public function scopeMostSold($query, $limit = 8)
    {
        return $query->withSum(['chiTiets as tong_ban' => function($q) {
            $q->whereHas('donHang', function($dq) {
                $dq->where('trang_thai', '!=', 'da_huy');
            });
        }], 'so_luong')
        ->orderByDesc('tong_ban')
        ->take($limit);
    }

    // Kiểm tra còn hàng
    public function conHang(): bool
    {
        return $this->so_luong_ton > 0;
    }

    // Lấy giá sau khi tự động áp dụng mã giảm của thể loại
    public function tinhGiaSauKhuyenMai()
    {
        $gia = $this->gia_ban;
        
        $theLoaiIds = [$this->the_loai_id];
        if ($this->theLoai && $this->theLoai->parent_id) {
            $theLoaiIds[] = $this->theLoai->parent_id;
        }

        // Tìm mã giảm giá auto-apply (nếu có the_loai_id thì coi như là auto apply cho thể loại đó)
        $voucher = \App\Models\MaGiamGia::whereIn('the_loai_id', $theLoaiIds)
            ->where('trang_thai', 1)
            ->where(function($q) {
                $q->whereNull('ngay_het_han')->orWhere('ngay_het_han', '>=', date('Y-m-d'));
            })
            ->where(function($q) {
                $q->whereNull('so_luong')->orWhereRaw('da_dung < so_luong');
            })
            ->orderByDesc('gia_tri') // lấy cái giảm nhiều nhất nếu có nhiều mã
            ->first();

        if ($voucher) {
            if ($voucher->loai === 'percent') {
                 $gia -= $gia * ($voucher->gia_tri / 100);
            } else {
                 $gia -= $voucher->gia_tri;
            }
            if ($gia < 0) $gia = 0;
        }
        
        return $gia;
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
                      ->orWhereHas('tacGia', function($tg) use ($search) { $tg->where('ten_tac_gia', 'like', "%{$search}%"); })
                      ->orWhereHas('nhaXuatBan', function($nxb) use ($search) { $nxb->where('ten_nxb', 'like', "%{$search}%"); })
                      ->orWhereHas('nhaCungCap', function($ncc) use ($search) { $ncc->where('ten_ncc', 'like', "%{$search}%"); });
            });
        });

        // 2. Lọc theo thể loại — tự động mở rộng sang con nếu click vào cha
        $query->when($filters['the_loai_id'] ?? false, function ($q, $categoryId) {
            // Kiểm tra xem đây có phải là danh mục cha không (có children)
            $childIds = DB::table('the_loai')
                ->where('parent_id', $categoryId)
                ->pluck('id')
                ->toArray();

            if (!empty($childIds)) {
                // Là danh mục cha: lọc cả cha lẫn con
                $allIds = array_merge([(int)$categoryId], $childIds);
                $q->whereIn('sach.the_loai_id', $allIds);
            } else {
                // Là danh mục lá: lọc chính xác
                $q->where('sach.the_loai_id', $categoryId);
            }
        });

        // 3. Lọc theo loại sách (trong nước / nước ngoài)
        $query->when($filters['loai_sach'] ?? false, function($q, $v) { $q->where('sach.loai_sach', $v); });

        // 4. Khoảng giá
        $query->when($filters['gia_min'] ?? false, function($q, $v) { $q->where('sach.gia_ban', '>=', $v); });
        $query->when($filters['gia_max'] ?? false, function($q, $v) { $q->where('sach.gia_ban', '<=', $v); });

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
