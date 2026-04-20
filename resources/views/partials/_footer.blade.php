{{-- Site Footer --}}
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            {{-- Brand --}}
            <div class="footer-brand">
                <div class="brand-name">
                    <img src="{{ asset('images/bookverse-logo.png') }}" alt="Bookverse" class="footer-brand__logo">
                </div>
                <p>Bookverse — Nhà sách trực tuyến hàng đầu Việt Nam. Khám phá hàng ngàn đầu sách chất lượng từ trong nước và quốc tế.</p>
                <div class="footer-social">
                    <a href="#" title="Facebook"><span class="material-icons">facebook</span></a>
                    <a href="#" title="Instagram"><span class="material-icons">photo_camera</span></a>
                    <a href="#" title="YouTube"><span class="material-icons">play_circle</span></a>
                    <a href="#" title="Email"><span class="material-icons">email</span></a>
                </div>
            </div>

            {{-- Links --}}
            <div class="footer-col">
                <h4>Khám phá</h4>
                <ul>
                    <li><a href="{{ url('/products') }}">Sách mới</a></li>
                    <li><a href="{{ url('/products?sort=bestseller') }}">Bán chạy</a></li>
                    <li><a href="{{ url('/products?view=categories') }}">Thể loại</a></li>
                    <li><a href="{{ url('/blog') }}">Blog</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Hỗ trợ</h4>
                <ul>
                    <li><a href="{{ url('/contact') }}">Liên hệ</a></li>
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                    <li><a href="#">Hướng dẫn mua hàng</a></li>
                    <li><a href="#">Chính sách đổi trả</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Về chúng tôi</h4>
                <ul>
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Điều khoản dịch vụ</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">Tuyển dụng</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© {{ date('Y') }} Bookverse. Tất cả quyền được bảo lưu.</p>
            <div>
                <a href="#">Điều khoản</a> · <a href="#">Bảo mật</a>
            </div>
        </div>
    </div>
</footer>
