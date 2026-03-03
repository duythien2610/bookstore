@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="admin-topbar">
        <h1>Dashboard</h1>
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            <span style="font-size: var(--font-size-sm); color: var(--color-text-muted);">Hôm nay: {{ now()->format('d/m/Y') }}</span>
            <button class="btn btn-outline btn-sm"><span class="material-icons" style="font-size: 16px;">download</span> Xuất báo cáo</button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid" id="stats">
        <div class="stat-card">
            <div class="stat-label">Tổng doanh thu</div>
            <div class="stat-value">45.2M</div>
            <div class="stat-change up"><span class="material-icons" style="font-size: 14px;">trending_up</span> +12.5% so với tháng trước</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Đơn hàng mới</div>
            <div class="stat-value">128</div>
            <div class="stat-change up"><span class="material-icons" style="font-size: 14px;">trending_up</span> +8.2%</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Khách hàng mới</div>
            <div class="stat-value">56</div>
            <div class="stat-change up"><span class="material-icons" style="font-size: 14px;">trending_up</span> +15.3%</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Sách bán ra</div>
            <div class="stat-value">342</div>
            <div class="stat-change down"><span class="material-icons" style="font-size: 14px;">trending_down</span> -3.1%</div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div style="background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); margin-bottom: var(--space-8);" id="recent-orders">
        <div style="padding: var(--space-6); display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border-light);">
            <h3>Đơn hàng gần đây</h3>
            <a href="{{ url('/admin/orders') }}" style="font-size: var(--font-size-sm);">Xem tất cả →</a>
        </div>
        <div class="table-wrapper" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Sản phẩm</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statuses = [
                            ['label' => 'Hoàn thành', 'class' => 'badge-success'],
                            ['label' => 'Đang giao', 'class' => 'badge-info'],
                            ['label' => 'Chờ xử lý', 'class' => 'badge-warning'],
                            ['label' => 'Đang giao', 'class' => 'badge-info'],
                            ['label' => 'Hoàn thành', 'class' => 'badge-success'],
                        ];
                    @endphp
                    @for ($i = 1; $i <= 5; $i++)
                    <tr>
                        <td style="font-weight: var(--font-semibold);">#MB2024{{ str_pad($i, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>Khách hàng {{ $i }}</td>
                        <td>{{ $i + 1 }} sản phẩm</td>
                        <td>{{ number_format(rand(200, 800) * 1000, 0, ',', '.') }}đ</td>
                        <td><span class="badge {{ $statuses[$i-1]['class'] }}">{{ $statuses[$i-1]['label'] }}</span></td>
                        <td>{{ now()->subDays($i)->format('d/m/Y') }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Products --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6);">
        <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light);" id="top-products">
            <h3 style="margin-bottom: var(--space-6);">Sách bán chạy</h3>
            @for ($i = 1; $i <= 5; $i++)
            <div style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-3) 0; {{ $i < 5 ? 'border-bottom: 1px solid var(--color-border-light);' : '' }}">
                <span style="width: 24px; font-size: var(--font-size-sm); color: var(--color-text-muted); font-weight: var(--font-semibold);">{{ $i }}</span>
                <div style="width: 44px; height: 56px; background: var(--color-bg-alt); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span class="material-icons" style="font-size: 20px; color: var(--color-text-muted);">book</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-size: var(--font-size-sm); font-weight: var(--font-medium);">Sách bán chạy {{ $i }}</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">{{ rand(50, 200) }} đã bán</div>
                </div>
                <div style="font-size: var(--font-size-sm); font-weight: var(--font-semibold);">{{ number_format(rand(100, 300) * 1000, 0, ',', '.') }}đ</div>
            </div>
            @endfor
        </div>

        <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light);" id="recent-customers">
            <h3 style="margin-bottom: var(--space-6);">Khách hàng mới</h3>
            @for ($i = 1; $i <= 5; $i++)
            <div style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-3) 0; {{ $i < 5 ? 'border-bottom: 1px solid var(--color-border-light);' : '' }}">
                <div style="width: 40px; height: 40px; background: var(--color-primary-light); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-weight: var(--font-bold); color: var(--color-primary-dark); font-size: var(--font-size-sm);">
                    {{ chr(64 + $i) }}
                </div>
                <div style="flex: 1;">
                    <div style="font-size: var(--font-size-sm); font-weight: var(--font-medium);">Khách hàng {{ $i }}</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">customer{{ $i }}@email.com</div>
                </div>
                <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">{{ now()->subDays($i)->format('d/m') }}</div>
            </div>
            @endfor
        </div>
    </div>
@endsection
