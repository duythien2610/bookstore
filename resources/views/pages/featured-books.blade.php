@extends('layouts.app')

@section('title', 'Sách nổi bật')

@section('content')
<div class="container" style="padding-top: var(--space-8); padding-bottom: var(--space-12);">
    <div class="section-header" style="margin-bottom: var(--space-8); border-bottom: 2px solid var(--color-primary-light); padding-bottom: var(--space-4);">
        <h1 style="display: flex; align-items: center; gap: var(--space-4);">
            Sách nổi bật
        </h1>
        <p style="color: var(--color-text-muted);">Danh sách được cập nhật dựa trên số lượng đơn hàng thực tế trong 6 tháng qua.</p>
    </div>

    @if($sachs->isEmpty())
        <div style="text-align: center; padding: var(--space-24); background: var(--color-white); border-radius: var(--radius-2xl);">
            <span class="material-icons" style="font-size: 80px; color: var(--color-text-muted); margin-bottom: var(--space-4);">inventory_2</span>
            <h3>Chưa có dữ liệu nổi bật</h3>
            <p>Hệ thống đang tổng hợp dữ liệu, vui lòng quay lại sau.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top: var(--space-4);">Xem tất cả sách</a>
        </div>
    @else
        <div class="book-grid book-grid-5">
            @foreach($sachs as $index => $sach)
                <div class="card" style="position: relative;">
                    <div style="position: absolute; top: -10px; left: -10px; width: 40px; height: 40px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; z-index: 10; box-shadow: var(--shadow-md);">
                        {{ $index + 1 }}
                    </div>
                    
                    <div class="card-img" style="height: 280px;">
                        @php
                            $imageUrl = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/' . $sach->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                        @endphp
                        <a href="{{ route('products.show', $sach->id) }}">
                            <img src="{{ $imageUrl }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-md);">
                        </a>
                        
                        @php
                            $giaKhuyenMai = $sach->tinhGiaSauKhuyenMai();
                        @endphp
                        @if ($giaKhuyenMai < $sach->gia_ban)
                        <span class="badge badge-danger" style="position: absolute; top: var(--space-3); left: auto; right: var(--space-3); z-index: 10;">
                            -{{ round((($sach->gia_ban - $giaKhuyenMai) / $sach->gia_ban) * 100) }}%
                        </span>
                        @elseif ($sach->gia_goc > $sach->gia_ban)
                        <span class="badge badge-danger" style="position: absolute; top: var(--space-3); left: auto; right: var(--space-3); z-index: 10;">
                            -{{ round((($sach->gia_goc - $sach->gia_ban) / $sach->gia_goc) * 100) }}%
                        </span>
                        @endif
                        
                        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                            <input type="hidden" name="so_luong" value="1">
                            <button type="submit" class="btn btn-primary btn-sm" style="position: absolute; bottom: 10px; right: 10px; border-radius: 50%; width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-lg);">
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
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="card-price" style="font-size: 18px; font-weight: 700; line-height: 1;">
                                @if($giaKhuyenMai < $sach->gia_ban)
                                    {{ number_format($giaKhuyenMai, 0, ',', '.') }}đ
                                    <div style="font-size: 12px; color: var(--color-text-muted); text-decoration: line-through; font-weight: normal;">{{ number_format($sach->gia_ban, 0, ',', '.') }}đ</div>
                                @else
                                    {{ number_format($sach->gia_ban, 0, ',', '.') }}đ
                                @endif
                            </div>
                            <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 500;">
                                <span style="color: var(--color-success);">{{ $sach->tong_ban }}</span> đã bán
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
    @media (max-width: 992px) { .book-grid-5 { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .book-grid-5 { grid-template-columns: repeat(2, 1fr); } }
</style>
@endsection
