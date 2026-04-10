@extends('layouts.admin')

@section('title', 'Cài đặt hệ thống')

@section('content')
    <div class="admin-topbar">
        <h1>Cài đặt hệ thống</h1>
    </div>

    @if(session('success'))
        <div style="padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3); background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
            <span class="material-icons" style="font-size: 20px;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: var(--space-6);">
        {{-- Thông tin chung --}}
        <div class="card" style="padding: var(--space-6); background: white; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light);">
            <h3 style="margin-bottom: var(--space-5); display: flex; align-items: center; gap: var(--space-2); font-size: 18px;">
                <span class="material-icons" style="color: var(--color-primary);">info</span>
                Thông tin nhà sách
            </h3>
            
            <form action="#" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: var(--space-4);">
                    <label class="form-label">Tên nhà sách</label>
                    <input type="text" class="form-control" value="Modtra Books" placeholder="Nhập tên nhà sách">
                </div>
                
                <div class="form-group" style="margin-bottom: var(--space-4);">
                    <label class="form-label">Email hỗ trợ</label>
                    <input type="email" class="form-control" value="support@modtrabooks.vn" placeholder="email@example.com">
                </div>
                
                <div class="form-group" style="margin-bottom: var(--space-4);">
                    <label class="form-label">Số điện thoại (Hotline)</label>
                    <input type="text" class="form-control" value="1900 1234" placeholder="090x xxx xxx">
                </div>
                
                <div class="form-group" style="margin-bottom: var(--space-4);">
                    <label class="form-label">Địa chỉ</label>
                    <textarea class="form-control" rows="3" placeholder="Địa chỉ trụ sở chính">123 Đường Sách, Quận 1, TP. Hồ Chí Minh</textarea>
                </div>

                <div style="margin-top: var(--space-6);">
                    <button type="button" class="btn btn-primary">Lưu cấu hình</button>
                </div>
            </form>
        </div>

        {{-- Cấu hình hiển thị --}}
        <div class="card" style="padding: var(--space-6); background: white; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light);">
            <h3 style="margin-bottom: var(--space-5); display: flex; align-items: center; gap: var(--space-2); font-size: 18px;">
                <span class="material-icons" style="color: var(--color-warning);">settings_display</span>
                Giao diện & Hệ thống
            </h3>
            
            <div class="form-group" style="margin-bottom: var(--space-4);">
                <label class="form-label">Logo website</label>
                <div style="display: flex; align-items: center; gap: var(--space-4); margin-top: var(--space-2);">
                    <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; border: 2px dashed #d1d5db;">
                        <span class="logo-icon" style="font-size: 32px;">M</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline btn-sm">Thay đổi logo</button>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 5px;">Hỗ trợ format: PNG, SVG, JPG. Tối đa 2MB.</p>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: var(--space-4);">
                <label class="form-label">Phí vận chuyển mặc định (VNĐ)</label>
                <input type="number" class="form-control" value="35000">
            </div>

            <div class="form-group" style="margin-bottom: var(--space-6);">
                <label class="form-label" style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer;">
                    <input type="checkbox" checked>
                    Cho phép đăng ký thành viên mới
                </label>
            </div>

            <div class="form-group" style="margin-bottom: var(--space-6);">
                <label class="form-label" style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer; color: var(--color-danger);">
                    <input type="checkbox">
                    Chế độ bảo trì hệ thống (Maintenance Mode)
                </label>
            </div>

            <div style="margin-top: var(--space-6);">
                <button type="button" class="btn btn-primary">Cập nhật hệ thống</button>
            </div>
        </div>
    </div>

    {{-- SEO Settings --}}
    <div class="card" style="margin-top: var(--space-6); padding: var(--space-6); background: white; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light);">
        <h3 style="margin-bottom: var(--space-5); display: flex; align-items: center; gap: var(--space-2); font-size: 18px;">
            <span class="material-icons" style="color: #2563eb;">search</span>
            Tối ưu SEO (Meta Tags)
        </h3>
        
        <div class="form-group" style="margin-bottom: var(--space-4);">
            <label class="form-label">Meta Title mặc định</label>
            <input type="text" class="form-control" value="Modtra Books - Hiệu sách trực tuyến số 1 Việt Nam">
        </div>

        <div class="form-group">
            <label class="form-label">Meta Description mặc định</label>
            <textarea class="form-control" rows="2">Chào mừng bạn đến với Modtra Books. Chúng tôi cung cấp hàng ngàn đầu sách đa dạng từ kinh tế, văn học đến kỹ năng sống.</textarea>
        </div>
    </div>
@endsection
