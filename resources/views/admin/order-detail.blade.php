@extends('layouts.admin')

@section('title', 'Chi tiết Đơn hàng')

@section('content')
    <div class="admin-topbar">
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            <a href="{{ route('admin.orders') }}" class="btn btn-ghost btn-sm">
                <span class="material-icons">arrow_back</span>
            </a>
            <h1>Chi tiết Đơn hàng #MB{{ str_pad($donHang->id, 6, '0', STR_PAD_LEFT) }}</h1>
        </div>
        <div style="display:flex; gap: var(--space-2);">
            <form action="{{ route('admin.orders.updateStatus', $donHang->id) }}" method="POST" style="display:flex; gap:var(--space-2);">
                @csrf
                @method('PATCH')
                <select name="trang_thai" class="input" style="width:200px;" required>
                    <option value="cho_thanh_toan" {{ $donHang->trang_thai == 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="cho_xac_nhan" {{ $donHang->trang_thai == 'cho_xac_nhan' ? 'selected' : '' }}>Chờ xác nhận</option>
                    <option value="dang_xu_ly" {{ $donHang->trang_thai == 'dang_xu_ly' ? 'selected' : '' }}>Đang lấy hàng</option>
                    <option value="dang_giao" {{ $donHang->trang_thai == 'dang_giao' ? 'selected' : '' }}>Đang giao</option>
                    <option value="da_giao" {{ $donHang->trang_thai == 'da_giao' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="huy" {{ $donHang->trang_thai == 'huy' ? 'selected' : '' }}>Đã hủy</option>
                </select>
                <button type="submit" class="btn btn-primary">Cập nhật Trạng thái</button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: var(--space-6);">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: var(--space-6);">
        @foreach($errors->all() as $err)
            <div>{{ $err }}</div>
        @endforeach
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-6);">
        {{-- Trái: Sản phẩm --}}
        <div>
            <div class="card" style="padding: var(--space-6); margin-bottom: var(--space-6);">
                <h2 style="font-size: var(--font-lg); margin-bottom: var(--space-4); border-bottom: 1px solid var(--color-border); padding-bottom: var(--space-3);">Sản phẩm đã đặt</h2>
                
                @foreach($donHang->chiTiets as $ct)
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border-light); padding: var(--space-3) 0;">
                    <div style="display: flex; gap: var(--space-4); align-items:center;">
                        @if($ct->sach->file_anh_bia || $ct->sach->link_anh_bia)
                            <img src="{{ $ct->sach->file_anh_bia ? asset('uploads/books/'.$ct->sach->file_anh_bia) : $ct->sach->link_anh_bia }}" style="width: 50px; height: 75px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div style="width: 50px; height: 75px; background:var(--color-bg); display:flex; align-items:center; justify-content:center; border-radius:4px;">
                                <span class="material-icons">book</span>
                            </div>
                        @endif
                        <div>
                            <div style="font-weight: var(--font-medium);">{{ $ct->sach->tieu_de }}</div>
                            <div style="font-size: var(--font-size-sm); color: var(--color-text-muted);">Số lượng: x{{ $ct->so_luong }}</div>
                        </div>
                    </div>
                    <div style="font-weight: var(--font-medium);">
                        {{ number_format($ct->gia * $ct->so_luong, 0, ',', '.') }}đ
                    </div>
                </div>
                @endforeach

                <div style="margin-top: var(--space-6);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-2);">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($donHang->tong_tien + ($donHang->maGiamGia ? $donHang->maGiamGia->gia_tri ?? 0 : 0), 0, ',', '.') }}đ</span>
                    </div>
                    @if($donHang->maGiamGia)
                    <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-2); color: var(--color-success);">
                        <span>Mã giảm giá ({{ $donHang->maGiamGia->ma_code }}):</span>
                        <span>-{{ number_format($donHang->maGiamGia->loai === 'percent' ? ($donHang->tong_tien * $donHang->maGiamGia->gia_tri/100) : $donHang->maGiamGia->gia_tri, 0, ',', '.') }}đ</span>
                    </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; font-weight: var(--font-bold); font-size: var(--font-lg); border-top: 1px solid var(--color-border); padding-top: var(--space-3); margin-top: var(--space-2);">
                        <span>Tổng cộng:</span>
                        <span style="color: var(--color-danger);">{{ number_format($donHang->tong_tien, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Phải: Thông tin giao hàng --}}
        <div>
            <div class="card" style="padding: var(--space-6); margin-bottom: var(--space-6);">
                <h3 style="font-size: var(--font-lg); margin-bottom: var(--space-4); border-bottom: 1px solid var(--color-border); padding-bottom: var(--space-3);">Thông tin Khách hàng</h3>
                <div style="margin-bottom: var(--space-3);">
                    <strong>Họ tên:</strong> {{ $donHang->ho_ten ?? ($donHang->user->ho_ten ?? 'N/A') }}
                </div>
                <div style="margin-bottom: var(--space-3);">
                    <strong>Số điện thoại:</strong> {{ $donHang->so_dien_thoai }}
                </div>
                <div style="margin-bottom: var(--space-3);">
                    <strong>Email:</strong> {{ $donHang->user->email ?? 'N/A' }}
                </div>
                <div style="margin-bottom: var(--space-3);">
                    <strong>Địa chỉ:</strong> {{ $donHang->dia_chi_giao_hang }}
                </div>
                <div style="margin-bottom: var(--space-3);">
                    <strong>Ghi chú:</strong> {{ $donHang->ghi_chu ?? 'Không có' }}
                </div>
                <hr style="margin: var(--space-4) 0; border: none; border-top: 1px solid var(--color-border-light);">
                <div>
                    <strong>Phương thức TT:</strong> 
                    <span style="text-transform: uppercase; font-weight: var(--font-medium);">{{ $donHang->phuong_thuc_thanh_toan }}</span>
                </div>
                @if($donHang->ngay_thanh_toan)
                <div style="margin-top: var(--space-2); color: var(--color-success);">
                    <span class="material-icons" style="font-size: 16px; vertical-align: middle;">check_circle</span>
                    Đã thanh toán ({{ \Carbon\Carbon::parse($donHang->ngay_thanh_toan)->format('d/m/Y H:i') }})
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
