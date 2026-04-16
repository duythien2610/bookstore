@extends('layouts.admin')

@section('title', 'Dashboard Tổng Quan')

@push('styles')
<style>
    /* Premium Dashboard Styles */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-8);
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: var(--space-6);
        margin-bottom: var(--space-8);
    }
    
    .stat-card-premium {
        background: var(--color-white);
        padding: var(--space-6);
        border-radius: var(--radius-xl);
        border: 1px solid var(--color-border-light);
        display: flex;
        flex-direction: column;
        transition: all var(--transition-normal);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card-premium:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-primary-light);
    }
    
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-4);
    }
    
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stat-trend {
        font-size: var(--font-size-xs);
        font-weight: var(--font-semibold);
        display: flex;
        align-items: center;
        gap: 2px;
    }
    
    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }
    
    .stat-label {
        font-size: var(--font-size-sm);
        color: var(--color-text-muted);
        font-weight: var(--font-medium);
    }
    
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-text);
        margin-top: 2px;
    }

    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: var(--space-6);
        margin-bottom: var(--space-6);
    }

    .card-panel {
        background: var(--color-white);
        border-radius: var(--radius-xl);
        border: 1px solid var(--color-border-light);
        padding: var(--space-6);
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-6);
    }

    .panel-title {
        font-size: var(--font-size-lg);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }

    /* List items for Top Selling */
    .top-selling-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-4);
    }

    .selling-item {
        display: flex;
        align-items: center;
        gap: var(--space-4);
        padding-bottom: var(--space-4);
        border-bottom: 1px solid var(--color-border-light);
    }

    .selling-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .book-mini-img {
        width: 48px;
        height: 64px;
        background: #f3f4f6;
        border-radius: var(--radius-md);
        object-fit: cover;
    }

    .book-rank {
        width: 24px;
        height: 24px;
        background: var(--color-primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        position: absolute;
        top: -8px;
        left: -8px;
    }
    
    .revenue-badge {
        background: #ecfdf5;
        color: #059669;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
    <div class="dashboard-header">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">Dashboard Tổng Quan</h1>
            <p style="color: var(--color-text-muted); font-size: var(--font-size-sm);">Chào mừng trở lại! Hôm nay là {{ now()->translatedFormat('l, d/m/Y') }}</p>
        </div>
        <div style="display: flex; gap: var(--space-3);">
            <a href="{{ route('admin.dashboard.export') }}" class="btn btn-outline" style="background: white; display: flex; align-items: center; gap: 8px; text-decoration: none;">
                <span class="material-icons" style="font-size: 18px;">download</span> Xuất báo cáo
            </a>
            <a href="{{ route('admin.inventory') }}" class="btn btn-primary">
                <span class="material-icons" style="font-size: 18px;">add</span> Quản lý kho
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="stats-container">
        {{-- Doanh thu --}}
        <div class="stat-card-premium">
            <div class="stat-header">
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <span class="material-icons">payments</span>
                </div>
                <div class="stat-trend trend-up">
                    <span class="material-icons" style="font-size: 14px;">trending_up</span> 12.5%
                </div>
            </div>
            <div class="stat-label">Tổng doanh thu</div>
            <div class="stat-number">{{ number_format($tongDoanhThu, 0, ',', '.') }}đ</div>
        </div>

        {{-- Đơn hàng --}}
        <div class="stat-card-premium">
            <div class="stat-header">
                <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                    <span class="material-icons">shopping_bag</span>
                </div>
                <div class="stat-trend trend-up">
                    <span class="material-icons" style="font-size: 14px;">trending_up</span> 4.2%
                </div>
            </div>
            <div class="stat-label">Tổng đơn hàng</div>
            <div class="stat-number">{{ number_format($tongDonHang) }}</div>
            @if($donHangMoi > 0)
            <div style="position: absolute; bottom: 12px; right: 12px; font-size: 10px; color: #3b82f6; font-weight: 700;">
                +{{ $donHangMoi }} đơn mới chờ
            </div>
            @endif
        </div>

        {{-- Sách trong kho --}}
        <div class="stat-card-premium">
            <div class="stat-header">
                <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                    <span class="material-icons">auto_stories</span>
                </div>
                <div class="stat-trend trend-down">
                    <span class="material-icons" style="font-size: 14px;">trending_down</span> 0.5%
                </div>
            </div>
            <div class="stat-label">Tổng số sách</div>
            <div class="stat-number">{{ number_format($tongSach) }}</div>
        </div>

        {{-- Khách hàng --}}
        <div class="stat-card-premium">
            <div class="stat-header">
                <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                    <span class="material-icons">group</span>
                </div>
                <div class="stat-trend trend-up">
                    <span class="material-icons" style="font-size: 14px;">trending_up</span> 8.1%
                </div>
            </div>
            <div class="stat-label">Khách hàng</div>
            <div class="stat-number">{{ number_format($tongKhachHang) }}</div>
        </div>
    </div>

    {{-- Main Activity Section --}}
    <div class="main-grid">
        {{-- Sales Chart --}}
        <div class="card-panel">
            <div class="panel-header">
                <h3 class="panel-title"><span class="material-icons" style="color: #3b82f6;">show_chart</span> Xu hướng tăng trưởng</h3>
                <select style="font-size: 12px; padding: 4px 8px; border-radius: 6px; border: 1px solid var(--color-border-light);">
                    <option>6 tháng gần đây</option>
                    <option>12 tháng gần đây</option>
                </select>
            </div>
            <div style="height: 350px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Top Selling --}}
        <div class="card-panel">
            <div class="panel-header">
                <h3 class="panel-title"><span class="material-icons" style="color: #f59e0b;">workspace_premium</span> Sách bán chạy nhất</h3>
            </div>
            <div class="top-selling-list">
                @foreach($topSellingSachs as $index => $sach)
                <div class="selling-item">
                    <div style="position: relative;">
                        <img src="{{ $sach->link_anh_bia ?? ($sach->file_anh_bia ? asset('uploads/books/'.$sach->file_anh_bia) : 'https://via.placeholder.com/150x200?text=No+Image') }}" alt="{{ $sach->tieu_de }}" class="book-mini-img">
                        <div class="book-rank">{{ $index + 1 }}</div>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 700; font-size: 13px; color: #1e293b; line-height: 1.3; margin-bottom: 2px;">{{ Str::limit($sach->tieu_de, 40) }}</div>
                        <div style="font-size: 11px; color: var(--color-text-muted);">{{ $sach->tacGia->ten_tac_gia ?? 'Nhiều tác giả' }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; color: #3b82f6; font-size: 14px;">{{ $sach->tong_ban ?? 0 }}</div>
                        <div style="font-size: 10px; color: var(--color-text-muted);">đã bán</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="main-grid" style="grid-template-columns: 1fr 1.5fr;">
         {{-- Category Breakdown --}}
         <div class="card-panel">
            <div class="panel-header">
                <h3 class="panel-title"><span class="material-icons" style="color: #8b5cf6;">pie_chart</span> Cơ cấu thể loại</h3>
            </div>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        {{-- Recent Books --}}
        <div class="card-panel" style="padding: 0; overflow: hidden;">
            <div style="padding: var(--space-6); border-bottom: 1px solid var(--color-border-light); display: flex; justify-content: space-between; align-items: center;">
                <h3 class="panel-title"><span class="material-icons" style="color: #ec4899;">new_releases</span> Sách mới nhập</h3>
                <a href="{{ route('admin.inventory') }}" style="font-size: 12px; font-weight: 600; color: #3b82f6;">Xem tất cả</a>
            </div>
            <div class="table-wrapper" style="border: none; margin: 0;">
                <table class="table" style="font-size: 13px;">
                    <thead>
                        <tr>
                            <th>Tên sách</th>
                            <th>Thể loại</th>
                            <th>Tồn kho</th>
                            <th>Ngày thêm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sachMoi as $sach)
                        <tr>
                            <td style="font-weight: 600;">{{ Str::limit($sach->tieu_de, 45) }}</td>
                            <td><span class="revenue-badge" style="background:#f3f4f6; color:#4b5563;">{{ $sach->theLoai->ten_the_loai ?? 'Chưa rõ' }}</span></td>
                            <td>{{ $sach->so_luong_ton }}</td>
                            <td style="color: #94a3b8;">{{ $sach->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Sales & Revenue Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    {
                        label: 'Doanh thu (đ)',
                        data: {!! json_encode($revenueCounts) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#3b82f6',
                        yAxisID: 'yRevenue'
                    },
                    {
                        label: 'Đơn hàng',
                        data: {!! json_encode($orderCounts) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 3,
                        borderDash: [5, 5],
                        pointBackgroundColor: '#10b981',
                        yAxisID: 'yOrders'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end' }
                },
                scales: {
                    yRevenue: {
                        type: 'linear',
                        position: 'left',
                        grid: { display: false },
                        ticks: {
                            callback: function(val) { return (val/1000000) + 'M'; }
                        }
                    },
                    yOrders: {
                        type: 'linear',
                        position: 'right',
                        grid: { display: true, borderDash: [2, 2] },
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // 2. Category Pie Chart
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($theLoaiStats->pluck('ten_the_loai')) !!},
                datasets: [{
                    data: {!! json_encode($theLoaiStats->pluck('sachs_count')) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20 }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush
