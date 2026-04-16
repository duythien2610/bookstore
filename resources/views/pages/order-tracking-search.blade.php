@extends('layouts.app')

@section('title', 'Tra cứu đơn hàng')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Tra cứu đơn hàng</span>
            </div>
            <h1>Theo dõi hành trình đơn hàng</h1>
        </div>
    </div>

    <div class="container" style="max-width: 500px; margin: var(--space-12) auto;">
        <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-8); border: 1px solid var(--color-border-light); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);">
            <div style="text-align: center; margin-bottom: var(--space-6);">
                <span class="material-icons" style="font-size: 48px; color: var(--color-primary); margin-bottom: var(--space-2);">search</span>
                <p style="color: var(--color-text-muted);">Nhập thông tin để tìm kiếm đơn hàng của bạn</p>
            </div>

            @if(session('error'))
                <div style="padding: var(--space-3) var(--space-4); background: #fef2f2; color: #dc2626; border-radius: var(--radius-md); font-size: var(--font-size-sm); margin-bottom: var(--space-4); border: 1px solid #fecaca;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('tracking.find') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: var(--space-4);">
                    <label class="form-label" for="order_id">Mã đơn hàng <span style="color: var(--color-danger);">*</span></label>
                    <input type="text" name="order_id" id="order_id" class="form-control" placeholder="Ví dụ: #MB000105" required value="{{ old('order_id') }}">
                    <p style="font-size: 11px; color: var(--color-text-muted); margin-top: 4px;">Bạn có thể tìm thấy mã này trong email xác nhận hoặc hóa đơn.</p>
                </div>

                <div class="form-group" style="margin-bottom: var(--space-6);">
                    <label class="form-label" for="phone">Số điện thoại đặt hàng <span style="color: var(--color-danger);">*</span></label>
                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="Ví dụ: 090xxx" required value="{{ old('phone') }}">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; height: 48px; font-weight: 600;">
                    Tra cứu ngay
                </button>
            </form>
        </div>

        <div style="text-align: center; margin-top: var(--space-6);">
            <p style="font-size: var(--font-size-sm); color: var(--color-text-muted);">
                Bạn đã đăng nhập? <a href="{{ route('my-orders') }}" style="color: var(--color-primary); font-weight: 600;">Xem lịch sử đơn hàng</a>
            </p>
        </div>
    </div>
@endsection
