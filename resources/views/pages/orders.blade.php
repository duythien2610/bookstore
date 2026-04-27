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

                {{-- Flash messages --}}
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

                                @if($order->trang_thai === 'da_giao')
                                    {{-- Hiển thị danh sách sản phẩm với nút đánh giá --}}
                                    <div class="order-items-list">
                                        @foreach($order->chiTiets as $ct)
                                            @if($ct->sach)
                                            <div class="order-item-row">
                                                <div class="order-item-thumb">
                                                    @if($ct->sach->file_anh_bia)
                                                        <img src="{{ asset('uploads/books/' . $ct->sach->file_anh_bia) }}" alt="{{ $ct->sach->tieu_de }}">
                                                    @elseif($ct->sach->link_anh_bia)
                                                        <img src="{{ $ct->sach->link_anh_bia }}" alt="{{ $ct->sach->tieu_de }}">
                                                    @else
                                                        <span class="material-icons" style="font-size:20px;color:var(--color-text-muted);">book</span>
                                                    @endif
                                                </div>
                                                <div class="order-item-info">
                                                    <div class="order-item-title">{{ $ct->sach->tieu_de }}</div>
                                                    <div class="order-item-meta">x{{ $ct->so_luong }} · {{ number_format($ct->don_gia, 0, ',', '.') }}đ</div>
                                                </div>
                                                @if(in_array($ct->sach_id, $reviewedSachIds))
                                                    <span class="btn-reviewed">
                                                        <span class="material-icons" style="font-size:14px;">verified</span>
                                                        Đã đánh giá
                                                    </span>
                                                @else
                                                    <a href="{{ route('products.show', $ct->sach_id) }}#reviews" class="btn btn-primary btn-sm"
                                                        style="flex-shrink:0;display:inline-flex;align-items:center;gap:4px;white-space:nowrap; text-decoration: none;">
                                                        <span class="material-icons" style="font-size:15px;">rate_review</span>
                                                        Đánh giá
                                                    </a>
                                                @endif
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Hiển thị thumbnails cho đơn chưa hoàn thành --}}
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
                                @endif

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

{{-- ══════════════════════════════════════════════════════════
     REVIEW MODAL
     ══════════════════════════════════════════════════════════ --}}
@push('modals')
<div id="review-modal-overlay" class="review-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="review-modal">
        {{-- Header --}}
        <div class="review-modal-header">
            <div class="review-modal-book">
                <img id="modal-book-img" src="" alt="Book cover" onerror="this.style.display='none'">
                <div>
                    <div style="font-size:var(--font-size-xs);color:var(--color-text-muted);margin-bottom:2px;">Đánh giá sách</div>
                    <div class="review-modal-book-title" id="modal-book-title"></div>
                </div>
            </div>
            <button class="review-modal-close" onclick="closeReviewModal()" title="Đóng">
                <span class="material-icons">close</span>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('danh-gia.store') }}" enctype="multipart/form-data" id="modal-review-form" novalidate>
            @csrf
            <input type="hidden" name="sach_id" id="modal-sach-id">

            {{-- Star rating --}}
            <div style="margin-bottom:var(--space-5);">
                <div style="font-size:var(--font-size-sm);font-weight:var(--font-medium);margin-bottom:var(--space-2);">
                    Chấm điểm <span style="color:var(--color-danger)">*</span>
                </div>
                <div class="star-picker" id="modal-star-picker">
                    @for($s = 1; $s <= 5; $s++)
                        <span class="star-pick material-icons" data-val="{{ $s }}" title="{{ $s }} sao">star_border</span>
                    @endfor
                </div>
                <input type="hidden" name="so_sao" id="modal-so-sao" value="0">
                <div id="modal-star-error" style="color:var(--color-danger);font-size:var(--font-size-xs);margin-top:4px;display:none;">Vui lòng chọn số sao.</div>
            </div>

            {{-- Title --}}
            <div class="form-group">
                <label class="form-label" style="font-size:var(--font-size-sm);">Tiêu đề đánh giá</label>
                <input type="text" name="tieu_de" class="form-control" placeholder="Tóm tắt ngắn gọn..." maxlength="200">
            </div>

            {{-- Comment --}}
            <div class="form-group">
                <label class="form-label" style="font-size:var(--font-size-sm);">Nội dung đánh giá</label>
                <textarea name="binh_luan" class="form-control" rows="4" placeholder="Chia sẻ cảm nhận của bạn về cuốn sách..." maxlength="2000"></textarea>
            </div>

            {{-- Image upload --}}
            <div class="form-group">
                <label class="form-label" style="font-size:var(--font-size-sm);">
                    Ảnh đính kèm
                    <span style="color:var(--color-text-muted);font-size:var(--font-size-xs);font-weight:400;">(tối đa 3 ảnh, jpeg/png/jpg, mỗi ảnh ≤ 2MB)</span>
                </label>
                <div class="review-img-upload-area" id="modal-upload-area" onclick="document.getElementById('modal-img-input').click()">
                    <div class="review-img-upload-trigger">
                        <span class="material-icons" style="font-size:22px;">add_photo_alternate</span>
                        <span>Nhấn để chọn ảnh</span>
                    </div>
                </div>
                <input type="file" name="hinh_anh[]" id="modal-img-input" multiple accept="image/jpeg,image/png,image/jpg" style="display:none;">
                <div class="review-img-preview-grid" id="modal-img-preview"></div>
                <div id="modal-img-error" style="color:var(--color-danger);font-size:var(--font-size-xs);margin-top:4px;display:none;"></div>
            </div>

            {{-- Submit --}}
            <div style="display:flex;justify-content:flex-end;gap:var(--space-3);margin-top:var(--space-2);">
                <button type="button" class="btn btn-outline" onclick="closeReviewModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="modal-submit-btn">
                    <span class="material-icons" style="font-size:18px;">send</span>
                    Gửi đánh giá
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Lightbox --}}
<div id="lightbox" class="lightbox-overlay" style="display:none;" onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="Ảnh đánh giá" onclick="event.stopPropagation()">
    <button class="lightbox-close" onclick="closeLightbox()" title="Đóng">
        <span class="material-icons">close</span>
    </button>
</div>
@endpush

@push('scripts')
<script>
    // ── Logout ──────────────────────────────────────────────
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

    // ── Review Modal ─────────────────────────────────────────
    const reviewOverlay  = document.getElementById('review-modal-overlay');
    const modalSachId    = document.getElementById('modal-sach-id');
    const modalBookTitle = document.getElementById('modal-book-title');
    const modalBookImg   = document.getElementById('modal-book-img');
    const modalForm      = document.getElementById('modal-review-form');
    const modalStarPicker= document.getElementById('modal-star-picker');
    const modalSoSao     = document.getElementById('modal-so-sao');
    const modalImgInput  = document.getElementById('modal-img-input');
    const modalImgPreview= document.getElementById('modal-img-preview');
    const modalImgError  = document.getElementById('modal-img-error');
    const modalStarError = document.getElementById('modal-star-error');

    let modalSelectedStar = 0;
    let modalSelectedFiles = [];

    // Delegate click to all "Đánh giá" buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-open-review');
        if (!btn) return;
        const sachId = btn.dataset.sachId;
        const title  = btn.dataset.title;
        const imgSrc = btn.dataset.img;
        openReviewModal(sachId, title, imgSrc);
    });

    function openReviewModal(sachId, title, imgSrc) {
        // Populate
        modalSachId.value = sachId;
        modalBookTitle.textContent = title;
        modalBookImg.src = imgSrc || '';
        modalBookImg.style.display = imgSrc ? '' : 'none';

        // Reset form
        modalForm.reset();
        modalSachId.value = sachId;
        resetModalStars();
        modalImgPreview.innerHTML = '';
        modalSelectedFiles = [];
        modalImgError.style.display = 'none';
        modalStarError.style.display = 'none';

        // Show
        reviewOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeReviewModal() {
        reviewOverlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close on backdrop click
    reviewOverlay?.addEventListener('click', function(e) {
        if (e.target === reviewOverlay) closeReviewModal();
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && reviewOverlay.style.display !== 'none') closeReviewModal();
    });

    // ── Modal Star Picker ────────────────────────────────────
    function resetModalStars() {
        modalSelectedStar = 0;
        modalSoSao.value = 0;
        renderModalStars(0);
    }

    function renderModalStars(upTo, permanent = false) {
        const stars = modalStarPicker.querySelectorAll('.star-pick');
        stars.forEach((s, i) => {
            s.textContent = i < upTo ? 'star' : 'star_border';
            s.style.color  = i < upTo ? '#f59e0b' : '#d1d5db';
        });
        if (permanent) {
            modalSelectedStar = upTo;
            modalSoSao.value  = upTo;
        }
    }

    modalStarPicker?.querySelectorAll('.star-pick').forEach((star, index) => {
        star.addEventListener('mouseenter', () => renderModalStars(index + 1));
        star.addEventListener('mouseleave', () => renderModalStars(modalSelectedStar));
        star.addEventListener('click',      () => renderModalStars(index + 1, true));
    });

    // ── Modal Image Upload ───────────────────────────────────
    modalImgInput?.addEventListener('change', function() {
        const newFiles = Array.from(this.files);
        const combined = [...modalSelectedFiles, ...newFiles];

        if (combined.length > 3) {
            modalImgError.textContent = 'Chỉ được tải lên tối đa 3 ảnh.';
            modalImgError.style.display = 'block';
            this.value = '';
            return;
        }

        // Size check
        for (const f of newFiles) {
            if (f.size > 2 * 1024 * 1024) {
                modalImgError.textContent = `Ảnh "${f.name}" vượt quá 2MB.`;
                modalImgError.style.display = 'block';
                this.value = '';
                return;
            }
        }

        modalImgError.style.display = 'none';
        modalSelectedFiles = combined;
        syncModalFileInput();
        renderModalPreviews();
    });

    function renderModalPreviews() {
        modalImgPreview.innerHTML = '';
        modalSelectedFiles.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const item = document.createElement('div');
                item.className = 'review-img-preview-item';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="preview" style="cursor:zoom-in;" onclick="openLightbox(this.src)">
                    <button type="button" class="review-img-preview-remove" onclick="removeModalImage(${idx})" title="Xóa ảnh">✕</button>
                `;
                modalImgPreview.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }

    function removeModalImage(idx) {
        modalSelectedFiles.splice(idx, 1);
        syncModalFileInput();
        renderModalPreviews();
        if (modalSelectedFiles.length < 3) modalImgError.style.display = 'none';
    }

    function syncModalFileInput() {
        const dt = new DataTransfer();
        modalSelectedFiles.forEach(f => dt.items.add(f));
        modalImgInput.files = dt.files;
    }

    // ── Modal Form Validate ──────────────────────────────────
    modalForm?.addEventListener('submit', function(e) {
        if (parseInt(modalSoSao.value) < 1) {
            e.preventDefault();
            modalStarError.style.display = 'block';
            modalStarPicker.style.outline = '2px solid var(--color-danger)';
            modalStarPicker.style.borderRadius = '4px';
            setTimeout(() => { modalStarPicker.style.outline = ''; }, 2000);
        }
    });

    // ── Lightbox ─────────────────────────────────────────────
    function openLightbox(src) {
        const lb = document.getElementById('lightbox');
        if (lb) {
            document.getElementById('lightbox-img').src = src;
            lb.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Nếu có review-modal đang mở, ẩn overflow của nó đi để ưu tiên lightbox
            const reviewOverlay = document.getElementById('review-modal-overlay');
            if (reviewOverlay && reviewOverlay.style.display !== 'none') {
                reviewOverlay.style.overflow = 'hidden';
            }
        }
    }

    function closeLightbox() {
        const lb = document.getElementById('lightbox');
        if (lb) {
            lb.style.display = 'none';
            document.body.style.overflow = '';
            
            const reviewOverlay = document.getElementById('review-modal-overlay');
            if (reviewOverlay && reviewOverlay.style.display !== 'none') {
                reviewOverlay.style.overflow = '';
                document.body.style.overflow = 'hidden'; // Restore body hidden for modal
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const lb = document.getElementById('lightbox');
            if (lb && lb.style.display === 'flex') {
                closeLightbox();
            } else if (reviewOverlay && reviewOverlay.style.display !== 'none') {
                closeReviewModal();
            }
        }
    });
</script>
@endpush
