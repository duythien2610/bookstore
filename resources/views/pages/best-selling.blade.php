@extends('layouts.app')

@section('title', 'Sách bán chạy nhất')
@section('meta_description', 'Top sách bán chạy nhất tại Modtra Books – xếp hạng theo số lượt mua thực tế từ đơn hàng thành công.')

@section('content')
<div class="container" style="padding-top: var(--space-8); padding-bottom: var(--space-12);">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: var(--space-6); border-bottom: 2px solid var(--color-primary-light); padding-bottom: var(--space-4);">
        <div>
            <h1 style="display:flex; align-items:center; gap:var(--space-3); margin:0;">
                <span style="font-size:32px;">🔥</span> Sách Bán Chạy Nhất
            </h1>
            <p style="color: var(--color-text-muted); margin-top: var(--space-2); margin-bottom:0;">
                Xếp hạng dựa trên số lượt mua thực tế từ các đơn hàng đã hoàn thành.
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

    @if(empty($rankingByCategory))
        <div style="text-align:center; padding:var(--space-24); background:var(--color-white); border-radius:var(--radius-2xl);">
            <span class="material-icons" style="font-size:80px; color:var(--color-text-muted); margin-bottom:var(--space-4);">trending_down</span>
            <h3>Chưa có dữ liệu bán chạy</h3>
            <p>Hệ thống cần thêm đơn hàng hoàn thành để xếp hạng sách bán chạy.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top:var(--space-4);">Xem tất cả sách</a>
        </div>
    @else

    {{-- Category Tabs --}}
    <div class="bs-tabs-wrap" id="bs-tabs">
        @foreach($rankingByCategory as $i => $cat)
        <button class="bs-tab {{ $i === 0 ? 'active' : '' }}"
                data-cat="{{ $cat['id'] }}"
                onclick="switchCategory({{ $cat['id'] }}, this)">
            {{ $cat['name'] }}
        </button>
        @endforeach
    </div>

    {{-- Ranking Panels — one per category --}}
    @foreach($rankingByCategory as $i => $cat)
    @php
        $books = $cat['books'];
    @endphp
    <div class="bs-panel" id="panel-{{ $cat['id'] }}" style="{{ $i !== 0 ? 'display:none;' : '' }}">
        <div class="bs-layout">

            {{-- LEFT: Ranking list --}}
            <div class="bs-left">
                @foreach($books as $rank => $sach)
                @php
                    $imgUrl = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/'.$sach->file_anh_bia) : 'https://placehold.co/80x110?text=No+Img');
                    $rankNum = $rank + 1;
                    $rankColor = $rankNum === 1 ? '#f59e0b' : ($rankNum === 2 ? '#94a3b8' : ($rankNum === 3 ? '#cd7c3a' : 'var(--color-text-muted)'));
                    $isHidden = $rankNum > 5;
                @endphp
                <div class="bs-row {{ $isHidden ? 'bs-extra' : '' }}"
                     data-sach-id="{{ $sach->id }}"
                     data-cat="{{ $cat['id'] }}"
                     onclick="selectBook(this, {{ $cat['id'] }})"
                     style="{{ $rankNum === 1 ? 'background:var(--color-bg-alt); border-radius:12px;' : '' }} {{ $isHidden ? 'display:none;' : '' }}">
                    <div class="bs-rank" style="color:{{ $rankColor }};">
                        <span style="font-size:20px; font-weight:900;">{{ str_pad($rankNum, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="material-icons" style="font-size:16px; color:{{ $rankNum <= 3 ? '#f59e0b' : '#10b981' }};">
                            {{ $rankNum <= 3 ? 'trending_up' : 'arrow_upward' }}
                        </span>
                    </div>
                    <div class="bs-thumb">
                        <img src="{{ $imgUrl }}" alt="{{ $sach->tieu_de }}">
                    </div>
                    <div class="bs-info">
                        <div class="bs-title">{{ Str::limit($sach->tieu_de, 55) }}</div>
                        <div class="bs-author">{{ $sach->tacGia->ten_tac_gia ?? 'Chưa cập nhật' }}</div>
                        <div class="bs-score">
                            <span class="material-icons" style="font-size:13px; vertical-align:middle;">local_fire_department</span>
                            {{ number_format($sach->tong_ban ?? 0) }} đã bán
                        </div>
                    </div>
                </div>
                @endforeach

                @if($books->count() > 5)
                <button class="bs-more-btn" onclick="showMoreBooks({{ $cat['id'] }}, this)">
                    <span class="material-icons" style="font-size:16px;">expand_more</span>
                    Xem thêm
                </button>
                @endif
            </div>

            {{-- RIGHT: Book detail --}}
            <div class="bs-right" id="detail-{{ $cat['id'] }}">
                @php $first = $books->first(); @endphp
                @if($first)
                @php
                    $img1 = $first->link_anh_bia ?: ($first->file_anh_bia ? asset('uploads/books/'.$first->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                    $giaBan1 = (float)$first->gia_ban;
                    $giaSauGiam1 = null;
                    if ($activeCoupon) {
                        $giaSauGiam1 = $activeCoupon->loai === 'percent'
                            ? $giaBan1 * (1 - $activeCoupon->gia_tri / 100)
                            : max(0, $giaBan1 - $activeCoupon->gia_tri);
                        $giaSauGiam1 = round($giaSauGiam1);
                    }
                    $pct1 = ($giaSauGiam1 && $giaSauGiam1 < $giaBan1)
                        ? round((1 - $giaSauGiam1 / $giaBan1) * 100)
                        : ($first->gia_goc > $giaBan1 ? round(($first->gia_goc - $giaBan1) / $first->gia_goc * 100) : 0);
                @endphp
                <div class="bs-detail-inner">
                    <div class="bs-cover-wrap">
                        <img src="{{ $img1 }}" alt="{{ $first->tieu_de }}" class="bs-cover-img">
                        @if($pct1 > 0)
                        <span class="bs-discount-badge">-{{ $pct1 }}%</span>
                        @endif
                    </div>
                    <div class="bs-detail-body">
                        <a href="{{ route('products.show', $first->id) }}" class="bs-detail-title">{{ $first->tieu_de }}</a>
                        <div class="bs-detail-meta">Tác giả: <strong>{{ $first->tacGia->ten_tac_gia ?? 'Chưa cập nhật' }}</strong></div>
                        @if($first->nhaXuatBan)
                        <div class="bs-detail-meta">Nhà xuất bản: <strong>{{ $first->nhaXuatBan->ten_nxb }}</strong></div>
                        @endif
                        <div class="bs-detail-price">
                            @if($giaSauGiam1 && $giaSauGiam1 < $giaBan1)
                                <span class="bs-price-main">{{ number_format($giaSauGiam1, 0, ',', '.') }} đ</span>
                                <span class="bs-price-old">{{ number_format($giaBan1, 0, ',', '.') }} đ</span>
                                <span class="bs-discount-tag">-{{ $pct1 }}%</span>
                            @else
                                <span class="bs-price-main">{{ number_format($giaBan1, 0, ',', '.') }} đ</span>
                                @if($first->gia_goc > $giaBan1)
                                <span class="bs-price-old">{{ number_format($first->gia_goc, 0, ',', '.') }} đ</span>
                                @endif
                            @endif
                        </div>
                        @if($first->mo_ta)
                        <div class="bs-desc-title">{{ Str::upper(Str::limit(strip_tags($first->mo_ta), 60)) }}</div>
                        <div class="bs-desc-text">{{ Str::limit(strip_tags($first->mo_ta), 200) }}</div>
                        @endif
                        @if($first->tacGia && $first->tacGia->mo_ta)
                        <div class="bs-author-section">
                            <div class="bs-author-title">VỀ TÁC GIẢ: {{ $first->tacGia->ten_tac_gia }}</div>
                            <div class="bs-author-bio">{{ Str::limit($first->tacGia->mo_ta, 150) }}</div>
                        </div>
                        @endif
                        <div style="display:flex; gap:10px; margin-top:20px; flex-wrap:wrap;">
                            <a href="{{ route('products.show', $first->id) }}" class="btn btn-primary" style="flex:1; min-width:120px; text-align:center;">Xem chi tiết</a>
                            <form action="{{ route('cart.add') }}" method="POST" style="flex:1; min-width:120px;">
                                @csrf
                                <input type="hidden" name="sach_id" value="{{ $first->id }}">
                                <input type="hidden" name="so_luong" value="1">
                                <button type="submit" class="btn btn-outline" style="width:100%;">
                                    <span class="material-icons" style="font-size:16px;">shopping_cart</span> Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        </div>{{-- .bs-layout --}}

        {{-- Hidden book data for JS detail switching (JSON) --}}
        <script id="books-data-{{ $cat['id'] }}" type="application/json">
        [
        @foreach($books as $s)
        @php
            $sImg   = $s->link_anh_bia ?: ($s->file_anh_bia ? asset('uploads/books/'.$s->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
            $sGia   = (float)$s->gia_ban;
            $sGiam  = null;
            if ($activeCoupon) {
                $sGiam = $activeCoupon->loai === 'percent'
                    ? $sGia * (1 - $activeCoupon->gia_tri / 100)
                    : max(0, $sGia - $activeCoupon->gia_tri);
                $sGiam = round($sGiam);
            }
            $sPct = ($sGiam && $sGiam < $sGia)
                ? round((1 - $sGiam / $sGia) * 100)
                : ($s->gia_goc > $sGia ? round(($s->gia_goc - $sGia) / $s->gia_goc * 100) : 0);
        @endphp
        {
            "id": {{ $s->id }},
            "tieu_de": {{ json_encode($s->tieu_de) }},
            "tac_gia": {{ json_encode($s->tacGia->ten_tac_gia ?? 'Chưa cập nhật') }},
            "nxb": {{ json_encode($s->nhaXuatBan->ten_nxb ?? '') }},
            "img": {{ json_encode($sImg) }},
            "gia_ban": {{ $sGia }},
            "gia_sau_giam": {{ $sGiam ?? 'null' }},
            "gia_goc": {{ (float)($s->gia_goc ?? 0) }},
            "pct_off": {{ $sPct }},
            "mo_ta": {{ json_encode(Str::limit(strip_tags($s->mo_ta ?? ''), 200)) }},
            "tac_gia_bio": {{ json_encode(Str::limit($s->tacGia->mo_ta ?? '', 150)) }},
            "url": {{ json_encode(route('products.show', $s->id)) }},
            "cart_url": {{ json_encode(route('cart.add')) }},
            "tong_ban": {{ $s->tong_ban ?? 0 }}
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
        ]
        </script>

    </div>{{-- .bs-panel --}}
    @endforeach

    @endif
</div>

<style>
/* ── Tabs ───────────────────────────────────────────────────── */
.bs-tabs-wrap {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 0;
}
.bs-tab {
    padding: 10px 18px;
    border: none;
    background: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-text-secondary, #718096);
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: color .2s, border-color .2s;
    border-radius: 6px 6px 0 0;
}
.bs-tab:hover { color: var(--color-primary, #e53e3e); }
.bs-tab.active { color: var(--color-primary, #e53e3e); border-bottom-color: var(--color-primary, #e53e3e); background: #fff5f5; }

/* ── Two-column layout ──────────────────────────────────────── */
.bs-layout {
    display: grid;
    grid-template-columns: 420px 1fr;
    gap: 28px;
    align-items: start;
}
@media (max-width: 900px) { .bs-layout { grid-template-columns: 1fr; } }

/* ── Left ranking list ──────────────────────────────────────── */
.bs-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 14px;
    cursor: pointer;
    border-radius: 10px;
    transition: background .18s, transform .15s;
    border: 1.5px solid transparent;
}
.bs-row:hover { background: var(--color-bg-alt, #f7f7f7); transform: translateX(3px); }
.bs-row.active-row { background: #fff5f5; border-color: var(--color-primary, #e53e3e); }
.bs-rank { display: flex; flex-direction: column; align-items: center; width: 36px; flex-shrink: 0; }
.bs-thumb { width: 56px; height: 78px; border-radius: 6px; overflow: hidden; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,.12); }
.bs-thumb img { width: 100%; height: 100%; object-fit: cover; }
.bs-info { flex: 1; min-width: 0; }
.bs-title { font-size: 14px; font-weight: 700; color: #1a202c; line-height: 1.35; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.bs-author { font-size: 12px; color: #718096; margin-top: 2px; }
.bs-score { font-size: 12px; color: var(--color-primary, #e53e3e); font-weight: 600; margin-top: 3px; display:flex; align-items:center; gap:2px; }

.bs-more-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    margin-top: 14px;
    padding: 11px;
    border: 1.5px solid var(--color-primary, #e53e3e);
    border-radius: 10px;
    background: none;
    color: var(--color-primary, #e53e3e);
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: background .18s, color .18s;
}
.bs-more-btn:hover { background: var(--color-primary, #e53e3e); color: white; }

/* ── Right detail panel ─────────────────────────────────────── */
.bs-right {
    position: sticky;
    top: 90px;
    background: var(--color-white, #fff);
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.07);
    transition: opacity .25s;
}
.bs-detail-inner { display: flex; gap: 0; }
.bs-cover-wrap { position: relative; flex-shrink: 0; width: 200px; min-height: 280px; overflow: hidden; }
.bs-cover-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.bs-discount-badge { position: absolute; top: 12px; left: 12px; background: var(--color-primary, #e53e3e); color: white; font-size: 12px; font-weight: 800; padding: 3px 8px; border-radius: 20px; }
.bs-detail-body { flex: 1; padding: 20px 22px; overflow: hidden; }
.bs-detail-title { font-size: 18px; font-weight: 800; color: #1a202c; line-height: 1.4; text-decoration: none; display: block; margin-bottom: 8px; }
.bs-detail-title:hover { color: var(--color-primary, #e53e3e); }
.bs-detail-meta { font-size: 13px; color: #718096; margin-bottom: 4px; }
.bs-detail-price { display: flex; align-items: baseline; gap: 10px; margin: 14px 0; flex-wrap: wrap; }
.bs-price-main { font-size: 24px; font-weight: 800; color: var(--color-primary, #e53e3e); }
.bs-price-old { font-size: 14px; color: #a0aec0; text-decoration: line-through; }
.bs-discount-tag { font-size: 12px; background: #fef3c7; color: #d97706; font-weight: 700; padding: 2px 8px; border-radius: 20px; }
.bs-desc-title { font-size: 12px; font-weight: 800; color: #4a5568; letter-spacing: .5px; margin-bottom: 6px; margin-top: 8px; }
.bs-desc-text { font-size: 13px; color: #718096; line-height: 1.6; }
.bs-author-section { margin-top: 14px; padding-top: 14px; border-top: 1px solid #f0f0f0; }
.bs-author-title { font-size: 12px; font-weight: 800; color: #4a5568; letter-spacing: .5px; margin-bottom: 6px; }
.bs-author-bio { font-size: 13px; color: #718096; line-height: 1.6; }

@media (max-width: 768px) {
    .bs-detail-inner { flex-direction: column; }
    .bs-cover-wrap { width: 100%; height: 220px; }
    .bs-layout { grid-template-columns: 1fr; }
    .bs-right { position: static; }
}
</style>

<script>
const BS_CSRF = '{{ csrf_token() }}';

// ── Switch category tab ─────────────────────────────────────────────────────
function switchCategory(catId, btn) {
    document.querySelectorAll('.bs-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.bs-panel').forEach(p => p.style.display = 'none');
    document.getElementById('panel-' + catId).style.display = '';
}

// ── Click a ranking row → update right panel ────────────────────────────────
function selectBook(row, catId) {
    // Highlight row
    document.querySelectorAll(`.bs-row[data-cat="${catId}"]`).forEach(r => r.classList.remove('active-row'));
    row.classList.add('active-row');

    const sachId = parseInt(row.dataset.sachId);
    const dataEl = document.getElementById('books-data-' + catId);
    if (!dataEl) return;
    const books  = JSON.parse(dataEl.textContent);
    const book   = books.find(b => b.id === sachId);
    if (!book) return;

    renderDetail(catId, book);
}

function renderDetail(catId, b) {
    const panel = document.getElementById('detail-' + catId);
    if (!panel) return;

    const priceHtml = (b.gia_sau_giam && b.gia_sau_giam < b.gia_ban)
        ? `<span class="bs-price-main">${fmt(b.gia_sau_giam)} đ</span>
           <span class="bs-price-old">${fmt(b.gia_ban)} đ</span>
           <span class="bs-discount-tag">-${b.pct_off}%</span>`
        : `<span class="bs-price-main">${fmt(b.gia_ban)} đ</span>
           ${b.gia_goc > b.gia_ban ? `<span class="bs-price-old">${fmt(b.gia_goc)} đ</span>` : ''}`;

    const discBadge = b.pct_off > 0 ? `<span class="bs-discount-badge">-${b.pct_off}%</span>` : '';
    const nxbHtml   = b.nxb ? `<div class="bs-detail-meta">Nhà xuất bản: <strong>${b.nxb}</strong></div>` : '';
    const descHtml  = b.mo_ta ? `<div class="bs-desc-title">${b.mo_ta.toUpperCase().substring(0,60)}</div><div class="bs-desc-text">${b.mo_ta}</div>` : '';
    const bioHtml   = b.tac_gia_bio ? `<div class="bs-author-section"><div class="bs-author-title">VỀ TÁC GIẢ: ${b.tac_gia}</div><div class="bs-author-bio">${b.tac_gia_bio}</div></div>` : '';

    panel.style.opacity = '0';
    setTimeout(() => {
        panel.innerHTML = `
        <div class="bs-detail-inner">
            <div class="bs-cover-wrap">
                <img src="${b.img}" alt="${esc(b.tieu_de)}" class="bs-cover-img">
                ${discBadge}
            </div>
            <div class="bs-detail-body">
                <a href="${b.url}" class="bs-detail-title">${esc(b.tieu_de)}</a>
                <div class="bs-detail-meta">Tác giả: <strong>${esc(b.tac_gia)}</strong></div>
                ${nxbHtml}
                <div class="bs-detail-price">${priceHtml}</div>
                ${descHtml}
                ${bioHtml}
                <div style="display:flex; gap:10px; margin-top:20px; flex-wrap:wrap;">
                    <a href="${b.url}" class="btn btn-primary" style="flex:1; min-width:120px; text-align:center;">Xem chi tiết</a>
                    <form action="${b.cart_url}" method="POST" style="flex:1; min-width:120px;">
                        <input type="hidden" name="_token" value="${BS_CSRF}">
                        <input type="hidden" name="sach_id" value="${b.id}">
                        <input type="hidden" name="so_luong" value="1">
                        <button type="submit" class="btn btn-outline" style="width:100%;">
                            <span class="material-icons" style="font-size:16px;">shopping_cart</span> Thêm vào giỏ
                        </button>
                    </form>
                </div>
            </div>
        </div>`;
        panel.style.opacity = '1';
    }, 150);
}

// ── Show more books ──────────────────────────────────────────────────────────
function showMoreBooks(catId, btn) {
    const extras = document.querySelectorAll(`.bs-extra[data-cat="${catId}"]`);
    extras.forEach(el => el.style.display = 'flex');
    btn.style.display = 'none';
}

// ── Helpers ──────────────────────────────────────────────────────────────────
function fmt(n) { return new Intl.NumberFormat('vi-VN').format(n); }
function esc(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

// Auto-highlight first row of each panel on load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.bs-panel').forEach(panel => {
        const firstRow = panel.querySelector('.bs-row:not(.bs-extra)');
        if (firstRow) firstRow.classList.add('active-row');
    });
});
</script>
@endsection
