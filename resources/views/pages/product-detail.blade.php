@extends('layouts.app')

@section('title', $sach->tieu_de)
@section('meta_description', Str::limit(strip_tags($sach->mo_ta ?? ''), 160))

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <a href="{{ route('products.index') }}">Tất cả sách</a>
                <span class="separator">›</span>
                <span>{{ Str::limit($sach->tieu_de, 40) }}</span>
            </div>
        </div>
    </div>

    <div class="container">
        {{-- Product Detail --}}
        <div style="display: grid; grid-template-columns: 380px 1fr; gap: var(--space-12); margin-bottom: var(--space-16);" id="product-detail">
            {{-- Image --}}
            <div>
                <div style="background: var(--color-bg-alt); border-radius: var(--radius-xl); overflow: hidden; aspect-ratio: 3/4; display: flex; align-items: center; justify-content: center;">
                    @if ($sach->file_anh_bia)
                        <img src="{{ asset('uploads/books/' . $sach->file_anh_bia) }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @elseif ($sach->link_anh_bia)
                        <img src="{{ $sach->link_anh_bia }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.parentElement.innerHTML='<span class=\'material-icons\' style=\'font-size:120px;color:var(--color-text-muted)\'>book</span>'">
                    @else
                        <span class="material-icons" style="font-size: 120px; color: var(--color-text-muted);">book</span>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div>
                @if($sach->theLoai)
                <span class="badge badge-primary" style="margin-bottom: var(--space-3);">{{ $sach->theLoai->ten_the_loai }}</span>
                @endif
                <h1 style="font-size: var(--font-size-3xl); margin-bottom: var(--space-2);" id="product-title">{{ $sach->tieu_de }}</h1>
                <p style="font-size: var(--font-size-base); color: var(--color-text-secondary); margin-bottom: var(--space-4);">
                    bởi <strong>{{ $sach->tacGia->ten_tac_gia ?? 'Không rõ' }}</strong>
                    @if($sach->nhaXuatBan)
                        &nbsp;·&nbsp; {{ $sach->nhaXuatBan->ten_nxb }}
                    @endif
                </p>

                @if($danhGias->count() > 0)
                <div class="stars" style="margin-bottom: var(--space-4);">
                    @for ($s = 1; $s <= 5; $s++)
                        <span class="material-icons" style="color: {{ $s <= round($diemTrungBinh) ? '#f59e0b' : '#d1d5db' }};">star</span>
                    @endfor
                    <span style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin-left: var(--space-2);">
                        {{ number_format($diemTrungBinh, 1) }} ({{ $danhGias->count() }} đánh giá)
                    </span>
                </div>
                @endif

                <div style="display: flex; align-items: baseline; gap: var(--space-3); margin-bottom: var(--space-6);">
                    @php 
                        $giaKhuyenMai = $sach->tinhGiaSauKhuyenMai(); 
                    @endphp
                    @if ($giaKhuyenMai < $sach->gia_ban)
                        <span style="font-size: var(--font-size-3xl); font-weight: var(--font-bold); color: var(--color-primary-dark);">
                            {{ number_format($giaKhuyenMai, 0, ',', '.') }}đ
                        </span>
                        <span style="font-size: var(--font-size-lg); color: var(--color-text-muted); text-decoration: line-through;">
                            {{ number_format($sach->gia_ban, 0, ',', '.') }}đ
                        </span>
                        <span class="badge badge-danger">-{{ round(($sach->gia_ban - $giaKhuyenMai) / $sach->gia_ban * 100) }}%</span>
                    @else
                        <span style="font-size: var(--font-size-3xl); font-weight: var(--font-bold); color: var(--color-primary-dark);">
                            {{ number_format($sach->gia_ban, 0, ',', '.') }}đ
                        </span>
                        @if ($sach->gia_goc > 0 && $sach->gia_goc > $sach->gia_ban)
                        <span style="font-size: var(--font-size-lg); color: var(--color-text-muted); text-decoration: line-through;">
                            {{ number_format($sach->gia_goc, 0, ',', '.') }}đ
                        </span>
                        <span class="badge badge-danger">-{{ round(($sach->gia_goc - $sach->gia_ban) / $sach->gia_goc * 100) }}%</span>
                        @endif
                    @endif
                </div>

                @if($sach->mo_ta)
                <p style="color: var(--color-text-secondary); line-height: 1.7; margin-bottom: var(--space-6);">
                    {{ Str::limit(strip_tags($sach->mo_ta), 300) }}
                </p>
                @endif

                @if($sach->so_luong_ton > 0)
                {{-- Thêm vào giỏ --}}
                @auth
                    @if(Auth::user()->email_verified_at)
                    <form method="POST" action="{{ route('cart.add') }}" class="ajax-cart-form">
                        @csrf
                        <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                        <div style="display: flex; gap: var(--space-4); align-items: center; margin-bottom: var(--space-6);">
                            <div class="quantity-control">
                                <button type="button" id="btn-qty-minus">−</button>
                                <input type="number" name="so_luong" id="qty-value" value="1" min="1" max="{{ $sach->so_luong_ton }}"
                                    style="width: 50px; text-align: center; border: none; font-size: var(--font-size-base); background: transparent;">
                                <button type="button" id="btn-qty-plus">+</button>
                            </div>
                            <button class="btn btn-primary btn-lg" id="btn-add-to-cart" style="flex: 1;">
                                <span class="material-icons">shopping_cart</span>
                                Thêm vào giỏ hàng
                            </button>
                            @php
                                $inWishlist = array_key_exists((string)$sach->id, session('wishlist', []));
                            @endphp
                             <button type="button" class="btn btn-outline btn-lg" id="btn-add-wishlist" data-sach-id="{{ $sach->id }}" title="Yêu thích" style="--wishlist-color: {{ $inWishlist ? 'var(--color-danger)' : 'inherit' }};">
                                <span class="material-icons" id="wishlist-icon" style="color: var(--wishlist-color);">{{ $inWishlist ? 'favorite' : 'favorite_border' }}</span>
                            </button>
                        </div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-muted);">
                            <span class="material-icons" style="font-size: 14px; vertical-align: middle; color: #4caf50;">check_circle</span>
                            Còn {{ $sach->so_luong_ton }} cuốn trong kho
                        </div>
                    </form>
                    @else
                        <a href="{{ route('verification.notice') }}" class="btn btn-primary btn-lg" style="width: 100%;">Xác thực email để mua hàng</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg" style="width: 100%;">Đăng nhập để mua hàng</a>
                @endauth
                @else
                <div class="badge badge-danger" style="padding: var(--space-3); font-size: var(--font-size-base);">Hết hàng</div>
                @endif

                {{-- Product Meta --}}
                <div style="background: var(--color-bg); border-radius: var(--radius-xl); padding: var(--space-6); margin-top: var(--space-6);">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); font-size: var(--font-size-sm);">
                        @if($sach->isbn)
                        <div><span style="color: var(--color-text-muted);">ISBN:</span> {{ $sach->isbn }}</div>
                        @endif
                        @if($sach->nhaXuatBan)
                        <div><span style="color: var(--color-text-muted);">Nhà xuất bản:</span> {{ $sach->nhaXuatBan->ten_nxb }}</div>
                        @endif
                        @if($sach->so_trang)
                        <div><span style="color: var(--color-text-muted);">Số trang:</span> {{ $sach->so_trang }}</div>
                        @endif
                        @if($sach->nam_xuat_ban)
                        <div><span style="color: var(--color-text-muted);">Năm XB:</span> {{ $sach->nam_xuat_ban }}</div>
                        @endif
                        @if($sach->hinh_thuc_bia)
                        <div><span style="color: var(--color-text-muted);">Bìa:</span> {{ $sach->hinh_thuc_bia }}</div>
                        @endif
                        <div><span style="color: var(--color-text-muted);">Loại:</span> {{ $sach->loai_sach === 'nuoc_ngoai' ? 'Nước ngoài' : 'Trong nước' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mô tả đầy đủ --}}
        @if($sach->mo_ta)
        <section style="margin-bottom: var(--space-16);">
            <h2 style="margin-bottom: var(--space-6);">Mô tả sách</h2>
            <div style="line-height: 1.8; color: var(--color-text-secondary);">
                {!! $sach->mo_ta !!}
            </div>
        </section>
        @endif

        {{-- ================================================================ --}}
        {{-- REVIEWS SECTION                                                   --}}
        {{-- ================================================================ --}}
        <section style="margin-bottom: var(--space-16);" id="reviews">
            <h2 style="margin-bottom: var(--space-6);">Đánh giá từ độc giả</h2>

            @if(session('success'))
                <div style="margin-bottom:var(--space-4);padding:var(--space-3) var(--space-4);background:#d1fae5;color:#065f46;border-radius:var(--radius-md);display:flex;align-items:center;gap:var(--space-2);">
                    <span class="material-icons" style="font-size:18px;">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom:var(--space-4);padding:var(--space-3) var(--space-4);background:#fee2e2;color:#991b1b;border-radius:var(--radius-md);display:flex;align-items:center;gap:var(--space-2);">
                    <span class="material-icons" style="font-size:18px;">error</span>
                    {{ session('error') }}
                </div>
            @endif

            <div class="review-layout">
                {{-- ── Left: Tổng quan điểm ── --}}
                <div class="review-summary-box">
                    <div class="review-score">{{ number_format($diemTrungBinh, 1) }}</div>
                    <div class="review-stars-big">
                        @for($s = 1; $s <= 5; $s++)
                            <span class="material-icons" style="color: {{ $s <= round($diemTrungBinh) ? '#f59e0b' : '#d1d5db' }};">star</span>
                        @endfor
                    </div>
                    <div style="font-size:var(--font-size-sm);color:var(--color-text-muted);margin-top:var(--space-1);">
                        {{ $danhGias->count() }} đánh giá
                    </div>

                    <div class="star-bars" style="margin-top:var(--space-4);width:100%;">
                        @php $tongDG = max(1, $danhGias->count()); @endphp
                        @for($s = 5; $s >= 1; $s--)
                        @php $cnt = $phanPhoiSao[$s] ?? 0; $pct = round($cnt / $tongDG * 100); @endphp
                        <div style="display:flex;align-items:center;gap:var(--space-2);margin-bottom:var(--space-1);">
                            <span style="font-size:var(--font-size-xs);width:8px;text-align:right;color:var(--color-text-muted);">{{ $s }}</span>
                            <span class="material-icons" style="font-size:12px;color:#f59e0b;">star</span>
                            <div style="flex:1;height:6px;background:var(--color-border);border-radius:999px;overflow:hidden; --bar-width: {{ $pct }}%;">
                                <div style="height:100%;width: var(--bar-width); background:#f59e0b; border-radius:999px; transition:width 0.6s ease;"></div>
                            </div>
                            <span style="font-size:var(--font-size-xs);color:var(--color-text-muted);width:28px;text-align:right;">{{ $cnt }}</span>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- ── Right: Danh sách + Form ── --}}
                <div style="flex:1;min-width:0;">

                    {{-- Form gửi đánh giá --}}
                    @auth
                        @if(Auth::user()->email_verified_at)
                            @if($daGuiDanhGia)
                                <div style="background:var(--color-bg-alt);border-radius:var(--radius-xl);padding:var(--space-4) var(--space-6);margin-bottom:var(--space-6);font-size:var(--font-size-sm);color:var(--color-text-muted);display:flex;align-items:center;gap:var(--space-2);">
                                    <span class="material-icons" style="font-size:18px;color:var(--color-primary-dark);">verified</span>
                                    Bạn đã gửi đánh giá cho cuốn sách này rồi.
                                </div>
                            @elseif($daMua)
                                <div class="review-form-card" id="review-form-card">
                                    <div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-4);">
                                        <div class="author-avatar" style="width:40px;height:40px;line-height:40px;font-size:var(--font-size-base);">{{ strtoupper(mb_substr(Auth::user()->ho_ten, 0, 1)) }}</div>
                                        <div>
                                            <div style="font-weight:var(--font-semibold);font-size:var(--font-size-sm);">{{ Auth::user()->ho_ten }}</div>
                                            <div style="font-size:var(--font-size-xs);color:var(--color-text-muted);">Đánh giá của bạn</div>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('danh-gia.store') }}" id="review-form">
                                        @csrf
                                        <input type="hidden" name="sach_id" value="{{ $sach->id }}">

                                        {{-- Star picker --}}
                                        <div style="margin-bottom:var(--space-4);">
                                            <div style="font-size:var(--font-size-sm);font-weight:var(--font-medium);margin-bottom:var(--space-2);">Chấm điểm <span style="color:var(--color-danger)">*</span></div>
                                            <div class="star-picker" id="star-picker">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <span class="star-pick material-icons" data-val="{{ $s }}" title="{{ $s }} sao">star_border</span>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="so_sao" id="so_sao_input" value="0">
                                            @error('so_sao')<div style="color:var(--color-danger);font-size:var(--font-size-xs);margin-top:4px;">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label" style="font-size:var(--font-size-sm);">Tiêu đề đánh giá</label>
                                            <input type="text" name="tieu_de" class="form-control" placeholder="Tóm tắt ngắn gọn..." maxlength="200" value="{{ old('tieu_de') }}">
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label" style="font-size:var(--font-size-sm);">Nội dung đánh giá</label>
                                            <textarea name="binh_luan" class="form-control" rows="4" placeholder="Chia sẻ cảm nhận của bạn về cuốn sách..." maxlength="2000">{{ old('binh_luan') }}</textarea>
                                        </div>

                                        <div style="display:flex;justify-content:flex-end;">
                                            <button type="submit" class="btn btn-primary" id="btn-submit-review">
                                                <span class="material-icons" style="font-size:18px;">send</span>
                                                Gửi đánh giá
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div style="background:var(--color-bg-alt);border-radius:var(--radius-xl);padding:var(--space-4) var(--space-6);margin-bottom:var(--space-6);font-size:var(--font-size-sm);color:var(--color-text-muted);display:flex;align-items:center;gap:var(--space-2);">
                                    <span class="material-icons" style="font-size:18px;">shopping_bag</span>
                                    Bạn cần mua và nhận sách này mới có thể đánh giá.
                                </div>
                            @endif
                        @else
                            <div style="background:var(--color-bg-alt);border-radius:var(--radius-xl);padding:var(--space-4) var(--space-6);margin-bottom:var(--space-6);font-size:var(--font-size-sm);color:var(--color-text-muted);">
                                <a href="{{ route('verification.notice') }}" style="color:var(--color-primary-dark);">Xác thực email</a> để có thể đánh giá sách.
                            </div>
                        @endif
                    @else
                        <div style="background:var(--color-bg-alt);border-radius:var(--radius-xl);padding:var(--space-4) var(--space-6);margin-bottom:var(--space-6);font-size:var(--font-size-sm);color:var(--color-text-muted);display:flex;align-items:center;gap:var(--space-2);">
                            <span class="material-icons" style="font-size:18px;">login</span>
                            <a href="{{ route('login') }}" style="color:var(--color-primary-dark);">Đăng nhập</a> để viết đánh giá.
                        </div>
                    @endauth

                    {{-- Danh sách đánh giá --}}
                    @if($danhGias->isEmpty())
                        <div style="text-align:center;padding:var(--space-12) 0;color:var(--color-text-muted);">
                            <span class="material-icons" style="font-size:64px;display:block;margin-bottom:var(--space-4);">rate_review</span>
                            Chưa có đánh giá nào. Hãy là người đầu tiên!
                        </div>
                    @else
                        <div style="display:flex;flex-direction:column;gap:var(--space-4);">
                        @foreach($danhGias as $dg)
                            <div class="review-card" id="review-{{ $dg->id }}">
                                <div class="review-card-header">
                                    <div class="author-info">
                                        <div class="author-avatar" style="width:40px;height:40px;line-height:40px;font-size:var(--font-size-sm);">
                                            {{ strtoupper(mb_substr($dg->user->ho_ten ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="author-name">{{ $dg->user->ho_ten ?? 'Ẩn danh' }}</div>
                                            <div class="author-role">{{ $dg->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:var(--space-3);">
                                        <div class="stars">
                                            @for($s = 1; $s <= 5; $s++)
                                                <span class="material-icons" style="font-size:16px;color:{{ $s <= $dg->so_sao ? '#f59e0b' : '#d1d5db' }};">star</span>
                                            @endfor
                                        </div>
                                        @auth
                                            @if(Auth::id() === $dg->user_id)
                                            <form method="POST" action="{{ route('danh-gia.destroy', $dg->id) }}" onsubmit="return confirm('Xóa đánh giá này?')" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--color-text-muted);display:flex;align-items:center;padding:4px;" title="Xóa đánh giá">
                                                    <span class="material-icons" style="font-size:18px;">delete_outline</span>
                                                </button>
                                            </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                @if($dg->tieu_de)
                                    <div style="font-weight:var(--font-semibold);font-size:var(--font-size-sm);margin:var(--space-2) 0 var(--space-1);">{{ $dg->tieu_de }}</div>
                                @endif
                                @if($dg->binh_luan)
                                    <p style="font-size:var(--font-size-sm);color:var(--color-text-secondary);line-height:1.7;margin:0;">{{ $dg->binh_luan }}</p>
                                @endif
                            </div>
                        @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Related Books --}}
        @if($sachLienQuan->isNotEmpty())
        <section class="section" style="padding-top: 0;" id="related-books">
            <div class="section-header">
                <h2>Sách liên quan</h2>
                <a href="{{ route('products.index') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            <div class="book-grid book-grid-4">
                @foreach ($sachLienQuan as $lr)
                <a href="{{ route('products.show', $lr->id) }}" class="card" style="text-decoration: none; color: inherit;">
                    <div class="card-img" style="display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        @if ($lr->file_anh_bia)
                            <img src="{{ asset('uploads/books/' . $lr->file_anh_bia) }}" alt="{{ $lr->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @elseif ($lr->link_anh_bia)
                            <img src="{{ $lr->link_anh_bia }}" alt="{{ $lr->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'">
                        @else
                            <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="card-title" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-clamp:2;">{{ $lr->tieu_de }}</div>
                        <div class="card-subtitle">{{ $lr->tacGia->ten_tac_gia ?? '' }}</div>
                        <div class="card-price">
                            @php $lrKhuyenMai = $lr->tinhGiaSauKhuyenMai(); @endphp
                            @if($lrKhuyenMai < $lr->gia_ban)
                                {{ number_format($lrKhuyenMai, 0, ',', '.') }}đ 
                                <span style="font-size: 12px; color: var(--color-text-muted); text-decoration: line-through; font-weight: normal; margin-left: 4px;">{{ number_format($lr->gia_ban, 0, ',', '.') }}đ</span>
                            @else
                                {{ number_format($lr->gia_ban, 0, ',', '.') }}đ
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif
    </div>

@push('scripts')
<script>
// Quantity control
const qtyInput = document.getElementById('qty-value');
const maxQty = Number('{{ $sach->so_luong_ton }}');
document.getElementById('btn-qty-minus')?.addEventListener('click', () => {
    if (qtyInput.value > 1) qtyInput.value = parseInt(qtyInput.value) - 1;
});
document.getElementById('btn-qty-plus')?.addEventListener('click', () => {
    if (parseInt(qtyInput.value) < maxQty) qtyInput.value = parseInt(qtyInput.value) + 1;
});

// Wishlist toggle
const sachId = Number('{{ $sach->id }}');
const wishlistIcon = document.getElementById('wishlist-icon');
document.getElementById('btn-add-wishlist')?.addEventListener('click', function() {
    fetch('{{ route("wishlist.toggle") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ sach_id: sachId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            wishlistIcon.textContent = data.added ? 'favorite' : 'favorite_border';
            wishlistIcon.style.color = data.added ? 'var(--color-danger)' : 'inherit';
            
            if (typeof showToast === 'function') {
                showToast(data.message, 'success');
            }
        }
    });
});

// ── Star Picker ─────────────────────────────────────────────────────────────
const starPicker = document.getElementById('star-picker');
const soSaoInput = document.getElementById('so_sao_input');
if (starPicker && soSaoInput) {
    const stars = starPicker.querySelectorAll('.star-pick');
    let selected = 0;

    function renderStars(upTo, permanent = false) {
        stars.forEach((s, i) => {
            s.textContent = i < upTo ? 'star' : 'star_border';
            s.style.color  = i < upTo ? '#f59e0b' : '#d1d5db';
        });
        if (permanent) {
            selected = upTo;
            soSaoInput.value = upTo;
        }
    }

    stars.forEach((star, index) => {
        star.addEventListener('mouseenter', () => renderStars(index + 1));
        star.addEventListener('mouseleave', () => renderStars(selected));
        star.addEventListener('click', () => renderStars(index + 1, true));
    });

    // Validate before submit
    document.getElementById('review-form')?.addEventListener('submit', function(e) {
        if (parseInt(soSaoInput.value) < 1) {
            e.preventDefault();
            starPicker.style.outline = '2px solid var(--color-danger)';
            starPicker.style.borderRadius = '4px';
            setTimeout(() => { starPicker.style.outline = ''; }, 2000);
        }
    });
}
</script>
@endpush
@endsection
