@extends('layouts.app')

@section('title', 'Hồ sơ của tôi')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Hồ sơ</span>
            </div>
            <h1>Tài khoản của tôi</h1>
        </div>
    </div>

    <div class="container">
        <div class="profile-grid" id="profile-content">
            {{-- Profile Sidebar --}}
            <div class="profile-sidebar">
                <div class="profile-avatar">N</div>
                <h4 style="margin-bottom: var(--space-1);">Nguyễn Văn A</h4>
                <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin-bottom: var(--space-6);">member@email.com</p>

                <nav class="profile-nav">
                    <a href="#" class="active" id="nav-profile-info">
                        <span class="material-icons" style="font-size: 20px;">person</span>
                        Thông tin cá nhân
                    </a>
                    <a href="{{ url('/order-tracking') }}" id="nav-my-orders">
                        <span class="material-icons" style="font-size: 20px;">receipt_long</span>
                        Đơn hàng của tôi
                    </a>
                    <a href="{{ url('/wishlist') }}" id="nav-my-wishlist">
                        <span class="material-icons" style="font-size: 20px;">favorite_border</span>
                        Sách yêu thích
                    </a>
                    <a href="#" id="nav-addresses">
                        <span class="material-icons" style="font-size: 20px;">location_on</span>
                        Sổ địa chỉ
                    </a>
                    <a href="#" id="nav-change-pw">
                        <span class="material-icons" style="font-size: 20px;">lock</span>
                        Đổi mật khẩu
                    </a>
                    <a href="#" id="nav-logout" style="color: var(--color-danger);">
                        <span class="material-icons" style="font-size: 20px;">logout</span>
                        Đăng xuất
                    </a>
                </nav>
            </div>

            {{-- Profile Content --}}
            <div class="profile-content" id="profile-form-section">
                <h3 style="margin-bottom: var(--space-6);">Thông tin cá nhân</h3>

                <form method="POST" action="{{ url('/profile') }}" id="profile-form">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4);">
                        <div class="form-group">
                            <label for="profile-name" class="form-label">Họ và tên</label>
                            <input type="text" id="profile-name" name="name" class="form-control" value="Nguyễn Văn A">
                        </div>
                        <div class="form-group">
                            <label for="profile-email" class="form-label">Email</label>
                            <input type="email" id="profile-email" name="email" class="form-control" value="member@email.com" disabled>
                            <div class="form-text">Email không thể thay đổi</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4);">
                        <div class="form-group">
                            <label for="profile-phone" class="form-label">Số điện thoại</label>
                            <input type="tel" id="profile-phone" name="phone" class="form-control" value="0912 345 678">
                        </div>
                        <div class="form-group">
                            <label for="profile-dob" class="form-label">Ngày sinh</label>
                            <input type="date" id="profile-dob" name="dob" class="form-control" value="1990-01-01">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profile-gender" class="form-label">Giới tính</label>
                        <div style="display: flex; gap: var(--space-6);">
                            <div class="form-check">
                                <input type="radio" id="gender-male" name="gender" value="male" checked>
                                <label for="gender-male">Nam</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="gender-female" name="gender" value="female">
                                <label for="gender-female">Nữ</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="gender-other" name="gender" value="other">
                                <label for="gender-other">Khác</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profile-address" class="form-label">Địa chỉ mặc định</label>
                        <input type="text" id="profile-address" name="address" class="form-control" value="123 Đường ABC, Quận 1, TP.HCM">
                    </div>

                    <div style="display: flex; gap: var(--space-4); justify-content: flex-end;">
                        <button type="button" class="btn btn-ghost">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-profile">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
