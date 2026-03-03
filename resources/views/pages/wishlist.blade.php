@extends('layouts.app')

@section('title', 'Danh sách yêu thích')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Danh sách yêu thích</span>
            </div>
            <h1>Sách yêu thích của bạn</h1>
        </div>
    </div>

    <div class="container" id="wishlist-content">
        <div class="book-grid book-grid-4">
            @for ($i = 1; $i <= 8; $i++)
            <div class="card" id="wishlist-item-{{ $i }}">
                <div style="position: relative;">
                    <div class="card-img" style="display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                    </div>
                    <button style="position: absolute; top: var(--space-3); right: var(--space-3); background: var(--color-white); border: none; cursor: pointer; width: 36px; height: 36px; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-md);" title="Xóa khỏi yêu thích">
                        <span class="material-icons" style="color: var(--color-danger); font-size: 20px;">favorite</span>
                    </button>
                </div>
                <div class="card-body">
                    <div class="stars" style="margin-bottom: var(--space-2);">
                        <span class="material-icons" style="font-size: 14px;">star</span>
                        <span class="material-icons" style="font-size: 14px;">star</span>
                        <span class="material-icons" style="font-size: 14px;">star</span>
                        <span class="material-icons" style="font-size: 14px;">star</span>
                        <span class="material-icons empty" style="font-size: 14px;">star</span>
                    </div>
                    <div class="card-title">Sách yêu thích {{ $i }}</div>
                    <div class="card-subtitle">Tác giả {{ $i }}</div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-3);">
                        <div class="card-price">{{ number_format(rand(89, 299) * 1000, 0, ',', '.') }}đ</div>
                        <button class="btn btn-primary btn-sm" id="btn-add-cart-{{ $i }}">
                            <span class="material-icons" style="font-size: 16px;">add_shopping_cart</span>
                        </button>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
@endsection
