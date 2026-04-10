@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Đơn hàng của tôi</span>
            </div>
            <h1>Lịch sử mua hàng</h1>
        </div>
    </div>

    <div class="container">
        <div class="profile-grid">
            {{-- Sidebar --}}
            <div class="profile-sidebar">
                <div class="profile-avatar">{{ strtoupper(mb_substr($user->ho_ten, 0, 1)) }}</div>
                <h4 style="margin-bottom: var(--space-1);">{{ $user->ho_ten }}</h4>
                <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin-bottom: var(--space-6);">{{ $user->email }}</p>

                <nav class="profile-nav">
                    <a href="{{ route('profile') }}" id="nav-profile-info">
                        <span class="material-icons" style="font-size:20px">person</span>
                        Thông tin cá nhân
                    </a>
                    <a href="{{ route('my-orders') }}" class="active" id="nav-my-orders">
                        <span class="material-icons" style="font-size:20px">receipt_long</span>
                        Đơn hàng của tôi
                    </a>
                    <a href="{{ url('/wishlist') }}" id="nav-my-wishlist">
                        <span class="material-icons" style="font-size:20px">favorite_border</span>
                        Sách yêu thích
                    </a>
                    <a href="{{ route('profile') }}?action=change-password" id="nav-change-pw">
                        <span class="material-icons" style="font-size:20px">lock</span>
                        Đổi mật khẩu
                    </a>
                    <a href="#" id="nav-logout" style="color:var(--color-danger)">
                        <span class="material-icons" style="font-size:20px">logout</span>
                        Đăng xuất
                    </a>
                </nav>
            </div>

            {{-- Content --}}
            <div class="profile-content">
                <h3 style="margin-bottom: var(--space-6);">Danh sách đơn hàng</h3>

                @if($orders->isEmpty())
                    <div style="text-align: center; padding: var(--space-12); background: var(--color-white); border-radius: var(--radius-xl); border: 1px dashed var(--color-border);">
                        <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted); margin-bottom: var(--space-4);">shopping_bag</span>
                        <p style="color: var(--color-text-muted);">Bạn chưa có đơn hàng nào.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm" style="margin-top: var(--space-4);">Mua sắm ngay</a>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                        @foreach($orders as $order)
                            <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light); transition: transform 0.2s, box-shadow 0.2s;" class="order-card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-4); flex-wrap: wrap; gap: var(--space-2);">
                                    <div>
                                        <div style="font-weight: var(--font-bold); font-size: var(--font-size-lg); color: var(--color-text);">
                                            Đơn hàng #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                        </div>
                                        <div style="font-size: var(--font-size-xs); color: var(--color-text-muted); margin-top: 2px;">
                                            Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    <span class="badge {{ $order->trang_thai_color }}" style="padding: 6px 12px; border-radius: 99px; font-weight: 500;">
                                        {{ $order->trang_thai_label }}
                                    </span>
                                </div>

                                <div style="display: flex; gap: var(--space-4); margin-bottom: var(--space-4); overflow-x: auto; padding-bottom: 8px;">
                                    @foreach($order->chiTiets->take(4) as $ct)
                                        <div style="width: 60px; height: 80px; flex-shrink: 0; background: var(--color-bg-alt); border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--color-border-light);">
                                            @if($ct->sach && $ct->sach->file_anh_bia)
                                                <img src="{{ asset('uploads/books/' . $ct->sach->file_anh_bia) }}" style="width:100%; height:100%; object-fit:cover;">
                                            @elseif($ct->sach && $ct->sach->link_anh_bia)
                                                <img src="{{ $ct->sach->link_anh_bia }}" style="width:100%; height:100%; object-fit:cover;">
                                            @else
                                                <div style="height:100%; display:flex; align-items:center; justify-content:center; color: var(--color-text-muted);">
                                                    <span class="material-icons" style="font-size: 20px;">book</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($order->chiTiets->count() > 4)
                                        <div style="width: 60px; height: 80px; flex-shrink: 0; background: #f3f4f6; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 13px; color: #4b5563; font-weight: 600;">
                                            +{{ $order->chiTiets->count() - 4 }}
                                        </div>
                                    @endif
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-border-light); padding-top: var(--space-4);">
                                    <div>
                                        <span style="font-size: var(--font-size-sm); color: var(--color-text-muted);">Tổng cộng:</span>
                                        <span style="font-weight: var(--font-bold); color: var(--color-primary-dark); font-size: var(--font-size-lg); margin-left: 4px;">
                                            {{ number_format($order->thanh_toan, 0, ',', '.') }}đ
                                        </span>
                                    </div>
                                    <a href="{{ route('order.tracking', $order->id) }}" class="btn btn-primary btn-sm" style="display: flex; align-items: center; gap: 4px;">
                                        <span class="material-icons" style="font-size: 16px;">local_shipping</span>
                                        Theo dõi
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top: var(--space-8);">
                        {{ $orders->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@push('scripts')
<script>
    // Logout logic
    const logoutLink = document.getElementById('nav-logout');
    if (logoutLink) {
        logoutLink.addEventListener('click', function (e) {
            e.preventDefault();
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = '{{ route("logout") }}';
            f.innerHTML = `@csrf`;
            document.body.appendChild(f);
            f.submit();
        });
    }
</script>
@endpush
