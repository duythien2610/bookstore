@extends('layouts.app')

@section('title', 'Sách bán chạy nhất')
@section('meta_description', 'Top sách bán chạy nhất tại Modtra Books – xếp hạng theo số lượt mua thực tế từ đơn hàng thành công.')

@section('content')
<div class="container" style="padding-top: var(--space-8); padding-bottom: var(--space-12);">

    {{-- Header --}}
    <div class="section-header" style="margin-bottom: var(--space-6); border-bottom: 2px solid var(--color-primary-light); padding-bottom: var(--space-4);">
        <div>
            <h1 style="display:flex; align-items:center; gap:var(--space-3);">
                <span style="font-size:32px;">🔥</span> Sách Bán Chạy Nhất
            </h1>
            <p style="color: var(--color-text-muted); margin-top: var(--space-2);">
                Xếp hạng dựa trên số lượt mua thực tế từ các đơn hàng đã giao thành công.
            </p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline">
            <span class="material-icons" style="font-size:18px;">grid_view</span> Xem tất cả sách
        </a>
    </div>

    {{-- Coupon Banner --}}
    @if($activeCoupon)
    <div style="display:flex; align-items:center; gap:var(--space-3); background:linear-gradient(135deg,#fff3cd,#ffe69c); border:1px solid #ffc107; border-radius:var(--radius-lg); padding:var(--space-3) var(--space-5); margin-bottom:var(--space-6);">
        <span class="material-icons" style="color:#856404; font-size:22px;">local_offer</span>
        <div>
            <strong style="color:#856404;">Khuyến mãi đang diễn ra!</strong>
            <span style="color:#856404; margin-left:8px;">Dùng mã <strong>{{ $activeCoupon->ma_code }}</strong> để giảm {{ $activeCoupon->loai === 'percent' ? $activeCoupon->gia_tri . '%' : number_format($activeCoupon->gia_tri, 0, ',', '.') . 'đ' }} cho đơn hàng!</span>
        </div>
    </div>
    @endif

    {{-- Book Grid --}}
    @if($sachs->isEmpty())
        <div style="text-align: center; padding: var(--space-24); background: var(--color-white); border-radius: var(--radius-2xl);">
            <span class="material-icons" style="font-size: 80px; color: var(--color-text-muted); margin-bottom: var(--space-4);">trending_down</span>
            <h3>Chưa có dữ liệu bán chạy</h3>
            <p>Hệ thống cần thêm đơn hàng hoàn thành để xếp hạng sách bán chạy.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top: var(--space-4);">Xem tất cả sách</a>
        </div>
    @else
        <div class="book-grid book-grid-5">
            @foreach($sachs as $index => $sach)
            @php
                $giaBan = (float)$sach->gia_ban;
                $giaSauGiam = null;
                if ($activeCoupon) {
                    $giaSauGiam = $activeCoupon->loai === 'percent'
                        ? $giaBan * (1 - $activeCoupon->gia_tri / 100)
                        : max(0, $giaBan - $activeCoupon->gia_tri);
                    $giaSauGiam = round($giaSauGiam);
                }
                $pctOff = ($giaSauGiam && $giaSauGiam < $giaBan)
                    ? round((1 - $giaSauGiam/$giaBan)*100)
                    : ($sach->gia_goc > $giaBan ? round(($sach->gia_goc - $giaBan)/$sach->gia_goc*100) : 0);
                $imageUrl = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/' . $sach->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                $rank = $index + 1;
                $rankColor = $rank === 1 ? '#f59e0b' : ($rank === 2 ? '#94a3b8' : ($rank === 3 ? '#cd7c3a' : 'var(--color-primary)'));
            @endphp
            <div class="card" style="position: relative;">
                {{-- Rank badge --}}
                <div style="position: absolute; top: -10px; left: -10px; width: 40px; height: 40px; background: {{ $rankColor }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; z-index: 10; box-shadow: var(--shadow-md);">
                    {{ $rank }}
                </div>

                <div style="position: relative;">
                    <a href="{{ route('products.show', $sach->id) }}" style="display:block;">
                        <img src="{{ $imageUrl }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 280px; object-fit: cover; border-radius: var(--radius-md) var(--radius-md) 0 0; display:block;">
                    </a>

                    {{-- Discount badge --}}
                    @if($pctOff > 0)
                    <span class="badge badge-danger" style="position:absolute; top:var(--space-3); right:var(--space-3); z-index:5;">-{{ $pctOff }}%</span>
                    @endif



                    {{-- Add to cart --}}
                    <form action="{{ route('cart.add') }}" method="POST" style="position:absolute; bottom:var(--space-3); right:var(--space-3); z-index:5;">
                        @csrf
                        <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                        <input type="hidden" name="so_luong" value="1">
                        <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 50%; width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-lg);" title="Thêm vào giỏ">
                            <span class="material-icons">shopping_cart</span>
                        </button>
                    </form>
                </div>

                <div class="card-body" style="padding: var(--space-4);">
                    <div class="stars" style="color: #ffc107; font-size: 14px; margin-bottom: 5px;">
                        @php $avgRating = $sach->trungBinhSao(); @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="material-icons" style="font-size: 16px;">{{ $i <= $avgRating ? 'star' : ($i - $avgRating < 1 ? 'star_half' : 'star_outline') }}</span>
                        @endfor
                    </div>

                    <a href="{{ route('products.show', $sach->id) }}" style="text-decoration: none; color: inherit;">
                        <h3 class="card-title" style="font-size: 16px; min-height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $sach->tieu_de }}</h3>
                    </a>

                    <div class="card-subtitle" style="margin-bottom: var(--space-3);">{{ $sach->tacGia->ten_tac_gia ?? 'Đang cập nhật' }}</div>

                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap:wrap; gap:4px;">
                        <div>
                            @if($giaSauGiam && $giaSauGiam < $giaBan)
                                <span class="card-price" style="font-size: 16px; font-weight: 700; color: var(--color-danger);">{{ number_format($giaSauGiam, 0, ',', '.') }}đ</span>
                                <span style="font-size:12px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                            @else
                                <span class="card-price" style="font-size: 16px; font-weight: 700;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                @if($sach->gia_goc > $giaBan)
                                    <span style="font-size:12px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($sach->gia_goc, 0, ',', '.') }}đ</span>
                                @endif
                            @endif
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 500;">
                            <span style="color: var(--color-success);">{{ $sach->tong_ban ?? 0 }}</span> đã bán
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .book-grid-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: var(--space-6);
    }
    @media (max-width: 1200px) { .book-grid-5 { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 992px)  { .book-grid-5 { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px)  { .book-grid-5 { grid-template-columns: repeat(2, 1fr); } }
</style>
@endsection
