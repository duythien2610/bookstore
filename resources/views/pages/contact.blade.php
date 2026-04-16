@extends('layouts.app')

@section('title', 'Liên hệ')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Liên hệ</span>
            </div>
            <h1>Liên hệ với chúng tôi</h1>
        </div>
    </div>

    <div class="container">
        {{-- Contact Info Cards --}}
        <div class="contact-info-cards" id="contact-info">
            <div class="contact-info-card">
                <span class="material-icons">location_on</span>
                <h4>Địa chỉ</h4>
                <p>123 Đường Sách, Q.1, TP.HCM</p>
            </div>
            <div class="contact-info-card">
                <span class="material-icons">phone</span>
                <h4>Điện thoại</h4>
                <p>1900 xxxx</p>
            </div>
            <div class="contact-info-card">
                <span class="material-icons">email</span>
                <h4>Email</h4>
                <p>support@modtrabooks.vn</p>
            </div>
            <div class="contact-info-card">
                <span class="material-icons">schedule</span>
                <h4>Giờ làm việc</h4>
                <p>T2-T7: 8:00 — 21:00</p>
            </div>
        </div>

        {{-- Contact Form + Map --}}
        <div class="contact-grid" id="contact-form-section">
            <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-8); border: 1px solid var(--color-border-light);">
                <h3 style="margin-bottom: var(--space-6);">Gửi tin nhắn cho chúng tôi</h3>

                @if(session('success'))
                <div style="display:flex; align-items:center; gap:var(--space-3); background:linear-gradient(135deg,#d1fae5,#a7f3d0); border:1px solid #34d399; border-radius:var(--radius-lg); padding:var(--space-4) var(--space-5); margin-bottom:var(--space-6);">
                    <span class="material-icons" style="color:#065f46; font-size:22px;">check_circle</span>
                    <span style="color:#065f46; font-weight:500;">{{ session('success') }}</span>
                </div>
                @endif

                <form method="POST" action="{{ url('/contact') }}" id="contact-form">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4);">
                        <div class="form-group">
                            <label for="contact-name" class="form-label">Họ và tên</label>
                            <input type="text" id="contact-name" name="name" class="form-control" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-email" class="form-label">Email</label>
                            <input type="email" id="contact-email" name="email" class="form-control" placeholder="example@email.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact-subject" class="form-label">Chủ đề</label>
                        <select id="contact-subject" name="subject" class="form-control" required>
                            <option value="">Chọn chủ đề</option>
                            <option>Hỏi về sản phẩm</option>
                            <option>Hỗ trợ đơn hàng</option>
                            <option>Đổi/Trả hàng</option>
                            <option>Góp ý/Phản hồi</option>
                            <option>Hợp tác</option>
                            <option>Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contact-message" class="form-label">Nội dung</label>
                        <textarea id="contact-message" name="message" class="form-control" rows="5" placeholder="Nhập nội dung tin nhắn..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" id="btn-send-contact">
                        <span class="material-icons">send</span>
                        Gửi tin nhắn
                    </button>
                </form>
            </div>

            {{-- Map Placeholder --}}
            <div style="background: var(--color-bg-alt); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; min-height: 400px;">
                <div style="text-align: center; color: var(--color-text-muted);">
                    <span class="material-icons" style="font-size: 64px;">map</span>
                    <p style="margin-top: var(--space-2);">Bản đồ Google Maps</p>
                </div>
            </div>
        </div>
    </div>
@endsection
