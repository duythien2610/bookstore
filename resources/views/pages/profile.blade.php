@extends('layouts.app')

@section('title', 'Hồ sơ của tôi')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Hồ sơ</span>
            </div>
            <h1>Tài khoản của tôi</h1>
        </div>
    </div>

    <div class="container">
        <div class="profile-grid" id="profile-content">

            {{-- ─── Sidebar ─── --}}
            <div class="profile-sidebar">
                <div class="profile-avatar">{{ strtoupper(mb_substr($user->ho_ten, 0, 1)) }}</div>
                <h4 style="margin-bottom: var(--space-1);">{{ $user->ho_ten }}</h4>
                <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin-bottom: var(--space-6);">{{ $user->email }}</p>

                <nav class="profile-nav">
                    <a href="#" class="active" id="nav-profile-info">
                        <span class="material-icons" style="font-size:20px">person</span>
                        Thông tin cá nhân
                    </a>
                    <a href="{{ route('my-orders') }}" id="nav-my-orders">
                        <span class="material-icons" style="font-size:20px">receipt_long</span>
                        Đơn hàng của tôi
                    </a>
                    <a href="{{ url('/wishlist') }}" id="nav-my-wishlist">
                        <span class="material-icons" style="font-size:20px">favorite_border</span>
                        Sách yêu thích
                    </a>
                    <a href="#" id="nav-change-pw">
                        <span class="material-icons" style="font-size:20px">lock</span>
                        Đổi mật khẩu
                    </a>
                    <a href="#" id="nav-logout" style="color:var(--color-danger)">
                        <span class="material-icons" style="font-size:20px">logout</span>
                        Đăng xuất
                    </a>
                </nav>
            </div>

            {{-- ─── Content ─── --}}
            <div class="profile-content" id="profile-form-section">
                <h3 style="margin-bottom:var(--space-6)">Thông tin cá nhân</h3>

                @if(session('success'))
                    <div style="margin-bottom:var(--space-4);padding:var(--space-3) var(--space-4);background:#d1fae5;color:#065f46;border-radius:var(--radius-md)">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="margin-bottom:var(--space-4);padding:var(--space-3) var(--space-4);background:#fee2e2;color:#991b1b;border-radius:var(--radius-md)">
                        <ul style="margin:0;padding-left:1.2rem">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" id="profile-form">
                    @csrf
                    @method('PUT')

                    {{-- Họ tên & Email --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                        <div class="form-group">
                            <label for="profile-name" class="form-label">Họ và tên <span style="color:var(--color-danger)">*</span></label>
                            <input type="text" id="profile-name" name="ho_ten" class="form-control"
                                   value="{{ old('ho_ten', $user->ho_ten) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            <div class="form-text">Email không thể thay đổi</div>
                        </div>
                    </div>

                    {{-- SĐT & Ngày sinh --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                        <div class="form-group">
                            <label for="profile-phone" class="form-label">Số điện thoại</label>
                            <input type="tel" id="profile-phone" name="so_dien_thoai" class="form-control"
                                   value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}">
                        </div>
                        <div class="form-group">
                            <label for="profile-dob" class="form-label">Ngày sinh</label>
                            <input type="date" id="profile-dob" name="ngay_sinh" class="form-control"
                                   value="{{ old('ngay_sinh', $user->ngay_sinh ? $user->ngay_sinh->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    {{-- Giới tính --}}
                    <div class="form-group">
                        <label class="form-label">Giới tính</label>
                        <div style="display:flex;gap:var(--space-6)">
                            <div class="form-check">
                                <input type="radio" id="gender-male" name="gioi_tinh" value="male"
                                       {{ old('gioi_tinh', $user->gioi_tinh) === 'male' ? 'checked' : '' }}>
                                <label for="gender-male">Nam</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="gender-female" name="gioi_tinh" value="female"
                                       {{ old('gioi_tinh', $user->gioi_tinh) === 'female' ? 'checked' : '' }}>
                                <label for="gender-female">Nữ</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="gender-other" name="gioi_tinh" value="other"
                                       {{ old('gioi_tinh', $user->gioi_tinh) === 'other' ? 'checked' : '' }}>
                                <label for="gender-other">Khác</label>
                            </div>
                        </div>
                    </div>

                    {{-- ĐỊA CHỈ --}}
                    <div class="form-group">
                        <label class="form-label">Địa chỉ mặc định</label>

                        {{-- Hidden: chuỗi ghép cuối cùng --}}
                        <input type="hidden" name="dia_chi" id="dia_chi_hidden">

                        {{-- Hàng 1: Tỉnh & Huyện --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3);margin-bottom:var(--space-3)">
                            <div>
                                <label for="select-tinh" class="form-label" style="font-size:var(--font-size-sm)">Tỉnh / Thành phố</label>
                                <select id="select-tinh" name="tinh" class="form-control">
                                    <option value="">-- Chọn Tỉnh/Thành --</option>
                                </select>
                            </div>
                            <div>
                                <label for="select-huyen" class="form-label" style="font-size:var(--font-size-sm)">Quận / Huyện</label>
                                <select id="select-huyen" name="huyen" class="form-control" disabled>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                            </div>
                        </div>

                        {{-- Hàng 2: Xã & Số nhà --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3);margin-bottom:var(--space-3)">
                            <div>
                                <label for="select-xa" class="form-label" style="font-size:var(--font-size-sm)">Phường / Xã</label>
                                <select id="select-xa" name="xa" class="form-control" disabled>
                                    <option value="">-- Chọn Phường/Xã --</option>
                                </select>
                            </div>
                            <div>
                                <label for="ten_duong" class="form-label" style="font-size:var(--font-size-sm)">Số nhà, Tên đường</label>
                                <input type="text" id="ten_duong" name="ten_duong" class="form-control"
                                       placeholder="Ví dụ: 123 Lê Lợi">
                            </div>
                        </div>

                        {{-- Preview địa chỉ đầy đủ --}}
                        <div id="address-preview"
                             style="display:none;margin-top:var(--space-2);padding:var(--space-2) var(--space-3);background:#f0f9ff;border:1px solid #bae6fd;border-radius:var(--radius-md);font-size:var(--font-size-sm);color:#0369a1">
                            <span class="material-icons" style="font-size:14px;vertical-align:middle">location_on</span>
                            <span id="address-preview-text"></span>
                        </div>

                        {{-- Địa chỉ hiện tại (nếu có) --}}
                        @if($user->dia_chi)
                            <div style="margin-top:var(--space-2);font-size:var(--font-size-sm);color:var(--color-text-muted)">
                                Địa chỉ hiện tại: <em>{{ implode(', ', array_filter(explode('|', $user->dia_chi))) }}</em>
                            </div>
                        @endif
                    </div>

                    <div style="display:flex;gap:var(--space-4);justify-content:flex-end">
                        <a href="{{ route('profile') }}" class="btn btn-ghost">Hủy</a>
                        <button type="submit" class="btn btn-primary" id="btn-save-profile">Lưu thay đổi</button>
                    </div>
                </form>
            </div>

            {{-- ─── Content: Đổi mật khẩu (Mặc định ẩn) ─── --}}
            <div class="profile-content" id="password-form-section" style="display: none;">
                <h3 style="margin-bottom:var(--space-6)">Đổi mật khẩu</h3>

                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group" style="margin-bottom:var(--space-4)">
                        <label class="form-label" for="old_password">Mật khẩu hiện tại <span style="color:var(--color-danger)">*</span></label>
                        <input type="password" name="old_password" id="old_password" class="form-control" required>
                    </div>

                    <div class="form-group" style="margin-bottom:var(--space-4)">
                        <label class="form-label" for="password">Mật khẩu mới <span style="color:var(--color-danger)">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="form-group" style="margin-bottom:var(--space-6)">
                        <label class="form-label" for="password_confirmation">Xác nhận mật khẩu mới <span style="color:var(--color-danger)">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>

                    <div style="display:flex;gap:var(--space-4);justify-content:flex-end">
                        <button type="button" class="btn btn-ghost" onclick="toggleSection('profile')">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Hàm chuyển đổi giữa các mục hồ sơ
    function toggleSection(section) {
        const profileSec = document.getElementById('profile-form-section');
        const passwordSec = document.getElementById('password-form-section');
        const navProfile = document.getElementById('nav-profile-info');
        const navPass    = document.getElementById('nav-change-pw');
        const sidebarLinks = document.querySelectorAll('.profile-nav a');

        sidebarLinks.forEach(link => link.classList.remove('active'));

        if (section === 'password') {
            profileSec.style.display = 'none';
            passwordSec.style.display = 'block';
            navPass.classList.add('active');
        } else {
            profileSec.style.display = 'block';
            passwordSec.style.display = 'none';
            navProfile.classList.add('active');
        }
    }

    // Gắn sự kiện cho các link sidebar (ngăn reload trang khi chuyển mục nội bộ)
    document.getElementById('nav-change-pw').addEventListener('click', function(e) {
        if (document.getElementById('password-form-section')) {
            e.preventDefault();
            toggleSection('password');
        }
    });

    document.getElementById('nav-profile-info').addEventListener('click', function(e) {
        if (document.getElementById('profile-form-section')) {
            e.preventDefault();
            toggleSection('profile');
        }
    });

    // Tự động chuyển tab nếu có query param ?action=change-password
    window.addEventListener('load', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'change-password') {
            toggleSection('password');
        }
    });

(function () {
    // ── Dữ liệu lưu sẵn ──────────────────────────────────────────────────
    const savedAddress = @json($user->dia_chi ?? '');

    // Parse địa chỉ — lưu theo format "tenDuong|xa|huyen|tinh" (pipe separator)
    function parseAddress(str) {
        if (!str) return { tinh:'', huyen:'', xa:'', tenDuong:'' };
        // Format mới: pipe separator (4 parts always)
        if (str.includes('|')) {
            const p = str.split('|');
            return { tenDuong: p[0]||'', xa: p[1]||'', huyen: p[2]||'', tinh: p[3]||'' };
        }
        // Legacy fallback: dấu phẩy (dữ liệu cũ)
        const parts = str.split(',').map(s => s.trim());
        if (parts.length === 4) return { tenDuong: parts[0], xa: parts[1], huyen: parts[2], tinh: parts[3] };
        if (parts.length === 3) return { tenDuong: '', xa: parts[0], huyen: parts[1], tinh: parts[2] };
        if (parts.length === 2) return { tenDuong: '', xa: '', huyen: parts[0], tinh: parts[1] };
        return { tenDuong: '', xa: '', huyen: '', tinh: str };
    }

    const parsed = parseAddress(savedAddress);

    // Pre-fill số nhà
    document.getElementById('ten_duong').value = parsed.tenDuong;

    // ── DOM refs ──────────────────────────────────────────────────────────
    const selTinh  = document.getElementById('select-tinh');
    const selHuyen = document.getElementById('select-huyen');
    const selXa    = document.getElementById('select-xa');

    // ── 1. Tải Tỉnh ───────────────────────────────────────────────────────
    fetch('https://provinces.open-api.vn/api/?depth=1')
        .then(r => r.json())
        .then(data => {
            data.forEach(p => {
                const opt = new Option(p.name, p.name);
                opt.dataset.code = p.code;
                if (p.name === parsed.tinh) opt.selected = true;
                selTinh.add(opt);
            });
            // Nếu đã có tỉnh saved → tải huyện
            if (parsed.tinh) {
                const sel = [...selTinh.options].find(o => o.value === parsed.tinh);
                if (sel) loadHuyen(sel.dataset.code, parsed.huyen, parsed.xa);
            }
        })
        .catch(() => { selTinh.innerHTML = '<option value="">Không tải được dữ liệu</option>'; });

    // ── 2. Tải Huyện theo Tỉnh ───────────────────────────────────────────
    function loadHuyen(tinhCode, preHuyen, preXa) {
        selHuyen.disabled = true;
        selHuyen.innerHTML = '<option value="">Đang tải...</option>';
        selXa.innerHTML    = '<option value="">-- Chọn Phường/Xã --</option>';
        selXa.disabled     = true;

        fetch(`https://provinces.open-api.vn/api/p/${tinhCode}?depth=2`)
            .then(r => r.json())
            .then(data => {
                selHuyen.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                (data.districts || []).forEach(d => {
                    const opt = new Option(d.name, d.name);
                    opt.dataset.code = d.code;
                    if (d.name === preHuyen) opt.selected = true;
                    selHuyen.add(opt);
                });
                selHuyen.disabled = false;

                // Nếu có huyện saved → tải xã
                if (preHuyen) {
                    const sel = [...selHuyen.options].find(o => o.value === preHuyen);
                    if (sel) loadXa(sel.dataset.code, preXa);
                }
                updatePreview();
            })
            .catch(() => { selHuyen.innerHTML = '<option value="">Lỗi tải dữ liệu</option>'; selHuyen.disabled = false; });
    }

    // ── 3. Tải Xã theo Huyện ────────────────────────────────────────────
    function loadXa(huyenCode, preXa) {
        selXa.disabled = true;
        selXa.innerHTML = '<option value="">Đang tải...</option>';

        fetch(`https://provinces.open-api.vn/api/d/${huyenCode}?depth=2`)
            .then(r => r.json())
            .then(data => {
                selXa.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                (data.wards || []).forEach(w => {
                    const opt = new Option(w.name, w.name);
                    if (w.name === preXa) opt.selected = true;
                    selXa.add(opt);
                });
                selXa.disabled = false;
                updatePreview();
            })
            .catch(() => { selXa.innerHTML = '<option value="">Lỗi tải dữ liệu</option>'; selXa.disabled = false; });
    }

    // ── Sự kiện đổi dropdown ────────────────────────────────────────────
    selTinh.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt.dataset.code) {
            loadHuyen(opt.dataset.code, '', '');
        } else {
            selHuyen.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            selHuyen.disabled  = true;
            selXa.innerHTML    = '<option value="">-- Chọn Phường/Xã --</option>';
            selXa.disabled     = true;
        }
        updatePreview();
    });

    selHuyen.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt.dataset.code) {
            loadXa(opt.dataset.code, '');
        } else {
            selXa.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            selXa.disabled  = true;
        }
        updatePreview();
    });

    selXa.addEventListener('change', updatePreview);
    document.getElementById('ten_duong').addEventListener('input', updatePreview);

    // ── Ghép & Preview ───────────────────────────────────────────────────
    function updatePreview() {
        const tinh     = selTinh.value;
        const huyen    = selHuyen.value;
        const xa       = selXa.value;
        const tenDuong = document.getElementById('ten_duong').value.trim();

        // Lưu DB: format cố định "tenDuong|xa|huyen|tinh" (pipe, luôn đủ 4 phần)
        document.getElementById('dia_chi_hidden').value = `${tenDuong}|${xa}|${huyen}|${tinh}`;

        // Hiển thị preview: dạng đọc được (bỏ phần rỗng)
        const display = [tenDuong, xa, huyen, tinh].filter(Boolean).join(', ');
        const preview = document.getElementById('address-preview');
        if (display) {
            document.getElementById('address-preview-text').textContent = display;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }

    // Gọi updatePreview trước khi submit
    document.getElementById('profile-form').addEventListener('submit', updatePreview);

    // ── Logout ──────────────────────────────────────────────────────────
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
})();
</script>
@endpush
