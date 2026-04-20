@extends('layouts.app')

@section('title', 'Liên hệ')

@section('content')
    {{-- ============================================================
         CONTACT HERO — gradient header with soft autumn blobs
         ============================================================ --}}
    <section class="page-header contact-hero">
        <div class="contact-hero-blob contact-hero-blob--1" aria-hidden="true"></div>
        <div class="contact-hero-blob contact-hero-blob--2" aria-hidden="true"></div>
        <div class="contact-hero-blob contact-hero-blob--3" aria-hidden="true"></div>
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Liên hệ</span>
            </div>
            <h1>Liên hệ với <span class="contact-hero-highlight">chúng tôi</span></h1>
            <p class="contact-hero-lead">
                Có câu hỏi về sách, đơn hàng hay muốn hợp tác? Bookverse luôn sẵn sàng lắng nghe —
                nhắn tin, gọi điện hoặc ghé thăm văn phòng của chúng tôi tại khuôn viên
                <strong>Trường Đại học Ngân Hàng TP.HCM</strong>.
            </p>
        </div>
    </section>

    <div class="container">
        {{-- ============================================================
             INFO CARDS — one per contact channel, colour-coded accents
             ============================================================ --}}
        <div class="contact-info-cards" id="contact-info">
            <a href="https://www.google.com/maps/dir/?api=1&destination=56+Ho%C3%A0ng+Di%E1%BB%87u+2,+Linh+Chi%E1%BB%83u,+Th%E1%BB%A7+%C4%90%E1%BB%A9c,+TP.HCM"
               target="_blank" rel="noopener"
               class="contact-info-card contact-info-card--address">
                <div class="contact-info-card__icon">
                    <span class="material-icons">location_on</span>
                </div>
                <h4>Địa chỉ</h4>
                <p>56 Hoàng Diệu 2, Linh Chiểu<br>Thủ Đức, TP.HCM</p>
                <span class="contact-info-card__link">Chỉ đường <span class="material-icons">arrow_forward</span></span>
            </a>

            <a href="tel:19001234" class="contact-info-card contact-info-card--phone">
                <div class="contact-info-card__icon">
                    <span class="material-icons">call</span>
                </div>
                <h4>Điện thoại</h4>
                <p>1900 1234<br>0909 000 000</p>
                <span class="contact-info-card__link">Gọi ngay <span class="material-icons">arrow_forward</span></span>
            </a>

            <a href="mailto:support@bookverse.vn" class="contact-info-card contact-info-card--email">
                <div class="contact-info-card__icon">
                    <span class="material-icons">email</span>
                </div>
                <h4>Email</h4>
                <p>support@bookverse.vn<br>hello@bookverse.vn</p>
                <span class="contact-info-card__link">Gửi email <span class="material-icons">arrow_forward</span></span>
            </a>

            <div class="contact-info-card contact-info-card--hours">
                <div class="contact-info-card__icon">
                    <span class="material-icons">schedule</span>
                </div>
                <h4>Giờ làm việc</h4>
                <p>T2 – T7: 8:00 – 21:00<br>Chủ nhật: 9:00 – 18:00</p>
                <span class="contact-info-card__badge">
                    <span class="contact-info-card__dot"></span>
                    Đang mở cửa
                </span>
            </div>
        </div>

        {{-- ============================================================
             MAP SECTION — real Google Maps embed with floating info card
             ============================================================ --}}
        <section class="contact-map-section">
            <div class="contact-map-wrapper">
                <iframe
                    src="https://www.google.com/maps?q=56+Ho%C3%A0ng+Di%E1%BB%87u+2,+Linh+Chi%E1%BB%83u,+Th%E1%BB%A7+%C4%90%E1%BB%A9c,+TP.HCM&hl=vi&z=17&output=embed"
                    class="contact-map-iframe"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen=""
                    title="Bản đồ Bookverse tại ĐH Ngân Hàng TP.HCM"></iframe>

                <div class="contact-map-card">
                    <div class="contact-map-card__badge">
                        <span class="material-icons">storefront</span>
                        Văn phòng chính
                    </div>
                    <h3 class="contact-map-card__title">Bookverse</h3>
                    <p class="contact-map-card__sub">Trong khuôn viên Trường Đại học Ngân Hàng TP.HCM</p>

                    <div class="contact-map-card__row">
                        <span class="material-icons">location_on</span>
                        <span>56 Hoàng Diệu 2, Linh Chiểu, Thủ Đức, TP.HCM</span>
                    </div>
                    <div class="contact-map-card__row">
                        <span class="material-icons">directions_car</span>
                        <span>~5 phút từ Ngã tư Thủ Đức • Có chỗ đậu xe</span>
                    </div>
                    <div class="contact-map-card__row">
                        <span class="material-icons">access_time</span>
                        <span>Mở cửa đến 21:00 hôm nay</span>
                    </div>

                    <a href="https://www.google.com/maps/dir/?api=1&destination=56+Ho%C3%A0ng+Di%E1%BB%87u+2,+Linh+Chi%E1%BB%83u,+Th%E1%BB%A7+%C4%90%E1%BB%A9c,+TP.HCM"
                       target="_blank" rel="noopener"
                       class="btn btn-primary contact-map-card__btn">
                        <span class="material-icons">directions</span>
                        Chỉ đường đến đây
                    </a>
                </div>
            </div>
        </section>

        {{-- ============================================================
             FORM + ASIDE
             ============================================================ --}}
        <div class="contact-grid" id="contact-form-section">
            <div class="contact-form-card">
                <div class="contact-form-head">
                    <span class="contact-form-kicker">
                        <span class="material-icons">forum</span>
                        Liên hệ trực tiếp
                    </span>
                    <h3>Gửi tin nhắn cho chúng tôi</h3>
                    <p class="contact-form-sub">Chúng tôi sẽ phản hồi trong vòng 24 giờ làm việc.</p>
                </div>

                @if(session('success'))
                <div class="contact-success-alert">
                    <span class="material-icons">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form method="POST" action="{{ url('/contact') }}" id="contact-form" class="contact-form">
                    @csrf
                    <div class="contact-form-row">
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

            <aside class="contact-aside">
                <div class="contact-aside-card contact-aside-card--social">
                    <h4>Kết nối với Bookverse</h4>
                    <p class="contact-aside-sub">Theo dõi để cập nhật sách mới, ưu đãi và sự kiện.</p>
                    <div class="contact-socials">
                        <a href="#" class="contact-social contact-social--facebook" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M13.5 21v-7.5h2.5l.4-3H13.5V8.7c0-.9.3-1.5 1.6-1.5H17V4.5c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4v2.1H8.5v3h2.3V21h2.7z"/>
                            </svg>
                            <span>Facebook</span>
                        </a>
                        <a href="#" class="contact-social contact-social--instagram" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2.2c3.2 0 3.6 0 4.8.1 1.2.1 1.8.3 2.2.4.6.2 1 .5 1.4.9.4.4.7.9.9 1.4.2.5.4 1.1.4 2.2.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 1.2-.3 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.9.7-1.4.9-.5.2-1.1.4-2.2.4-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-1.2-.1-1.8-.3-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.9-.9-1.4-.2-.5-.4-1.1-.4-2.2-.1-1.3-.1-1.6-.1-4.8s0-3.6.1-4.8c.1-1.2.3-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.9-.7 1.4-.9.5-.2 1.1-.4 2.2-.4 1.3-.1 1.6-.1 4.8-.1zM12 0C8.7 0 8.3 0 7 .1 5.7.1 4.8.3 4 .6c-.8.3-1.5.7-2.2 1.4C1.1 2.7.7 3.4.4 4.2c-.3.8-.5 1.7-.5 3C0 8.3 0 8.7 0 12s0 3.7.1 5c.1 1.3.3 2.2.6 3 .3.8.7 1.5 1.4 2.2.7.7 1.4 1.1 2.2 1.4.8.3 1.7.5 3 .6C8.3 24 8.7 24 12 24s3.7 0 5-.1c1.3-.1 2.2-.3 3-.6.8-.3 1.5-.7 2.2-1.4.7-.7 1.1-1.4 1.4-2.2.3-.8.5-1.7.6-3 .1-1.3.1-1.7.1-5s0-3.7-.1-5c-.1-1.3-.3-2.2-.6-3-.3-.8-.7-1.5-1.4-2.2C21.3 1.1 20.6.7 19.8.4c-.8-.3-1.7-.5-3-.5C15.7 0 15.3 0 12 0zm0 5.8c-3.4 0-6.2 2.8-6.2 6.2s2.8 6.2 6.2 6.2 6.2-2.8 6.2-6.2-2.8-6.2-6.2-6.2zm0 10.2c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4zm7.8-10.4c0 .8-.6 1.4-1.4 1.4s-1.4-.6-1.4-1.4.6-1.4 1.4-1.4 1.4.6 1.4 1.4z"/>
                            </svg>
                            <span>Instagram</span>
                        </a>
                        <a href="#" class="contact-social contact-social--zalo" aria-label="Zalo">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2C6.5 2 2 5.8 2 10.5c0 2.7 1.5 5.1 3.8 6.6L5 20.5c-.1.3.2.6.5.4l3.7-2c.9.2 1.8.3 2.8.3 5.5 0 10-3.8 10-8.5S17.5 2 12 2zm-4 9.5c-.5 0-1-.4-1-1s.5-1 1-1 1 .4 1 1-.5 1-1 1zm4 0c-.5 0-1-.4-1-1s.5-1 1-1 1 .4 1 1-.5 1-1 1zm4 0c-.5 0-1-.4-1-1s.5-1 1-1 1 .4 1 1-.5 1-1 1z"/>
                            </svg>
                            <span>Zalo</span>
                        </a>
                        <a href="#" class="contact-social contact-social--youtube" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M23 7.4c-.3-1-1-1.8-2-2.1C19 5 12 5 12 5s-7 0-9 .3C2 5.6 1.3 6.4 1 7.4.7 9.4.7 12 .7 12s0 2.6.3 4.6c.3 1 1 1.8 2 2.1 2 .3 9 .3 9 .3s7 0 9-.3c1-.3 1.7-1.1 2-2.1.3-2 .3-4.6.3-4.6s0-2.6-.3-4.6zM9.8 15.5v-7l6 3.5-6 3.5z"/>
                            </svg>
                            <span>YouTube</span>
                        </a>
                    </div>
                </div>

                <div class="contact-aside-card contact-aside-card--why">
                    <h4>Tại sao chọn Bookverse?</h4>
                    <ul class="contact-why-list">
                        <li>
                            <span class="contact-why-icon contact-why-icon--green">
                                <span class="material-icons">verified</span>
                            </span>
                            <div>
                                <strong>Sách chính hãng 100%</strong>
                                <span>Hợp tác trực tiếp với NXB uy tín</span>
                            </div>
                        </li>
                        <li>
                            <span class="contact-why-icon contact-why-icon--blue">
                                <span class="material-icons">local_shipping</span>
                            </span>
                            <div>
                                <strong>Giao hàng toàn quốc</strong>
                                <span>Nội thành HCM nhận trong 2 giờ</span>
                            </div>
                        </li>
                        <li>
                            <span class="contact-why-icon contact-why-icon--amber">
                                <span class="material-icons">replay</span>
                            </span>
                            <div>
                                <strong>Đổi trả dễ dàng</strong>
                                <span>Trong 7 ngày, không cần lý do</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
@endsection
