@extends('layouts.app')

@section('title', 'Danh sách yêu thích')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Yêu thích</span>
            </div>
            <h1>Danh sách yêu thích</h1>
        </div>
    </div>

    <div class="container">
        @if ($sachs->isEmpty())
        <div style="text-align: center; padding: var(--space-20) 0;">
            <span class="material-icons" style="font-size: 80px; color: var(--color-text-muted);">favorite_border</span>
            <h2 style="margin-top: var(--space-4); color: var(--color-text-muted);">Danh sách yêu thích trống</h2>
            <p style="color: var(--color-text-muted);">Hãy yêu thích những cuốn sách bạn quan tâm!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg" style="margin-top: var(--space-6);">
                <span class="material-icons">auto_stories</span> Khám phá sách
            </a>
        </div>
        @else
        <div class="book-grid book-grid-3" id="wishlist-grid">
            @foreach ($sachs as $sach)
            <div class="card" id="wishlist-item-{{ $sach->id }}">
                <a href="{{ route('products.show', $sach->id) }}" style="text-decoration: none; color: inherit;">
                    <div class="card-img" style="display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        @if ($sach->file_anh_bia)
                            <img src="{{ asset('uploads/books/' . $sach->file_anh_bia) }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @elseif ($sach->link_anh_bia)
                            <img src="{{ $sach->link_anh_bia }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'">
                        @else
                            <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="card-title" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-clamp:2;">{{ $sach->tieu_de }}</div>
                        <div class="card-subtitle">{{ $sach->tacGia->ten_tac_gia ?? 'Không rõ' }}</div>
                        <div class="card-price">{{ number_format($sach->gia_ban, 0, ',', '.') }}đ</div>
                    </div>
                </a>
                <div style="padding: 0 var(--space-4) var(--space-4); display: flex; gap: var(--space-2);">
                    <form method="POST" action="{{ route('cart.add') }}" style="flex: 1;" class="ajax-cart-form">
                        @csrf
                        <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                        <input type="hidden" name="so_luong" value="1">
                        <button type="submit" class="btn btn-primary btn-block btn-sm">
                            <span class="material-icons" style="font-size: 16px;">add_shopping_cart</span>
                        </button>
                    </form>
                    <button onclick="removeFromWishlist('{{ $sach->id }}')" class="btn btn-outline btn-sm" title="Xóa khỏi yêu thích">
                        <span class="material-icons" style="font-size: 16px; color: var(--color-danger);">delete</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

@push('scripts')
<script>
function removeFromWishlist(sachId) {
    fetch('{{ route("wishlist.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ sach_id: sachId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && !data.added) {
            const el = document.getElementById('wishlist-item-' + sachId);
            el?.style.setProperty('opacity', '0');
            setTimeout(() => {
                el?.remove();
                const grid = document.getElementById('wishlist-grid');
                if (grid && grid.children.length === 0) location.reload();
            }, 300);
        }
    });
}
</script>
@endpush
@endsection
