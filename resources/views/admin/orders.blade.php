@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý đơn hàng ({{ $tongTatCa }})</h1>
        <form action="{{ route('admin.orders') }}" method="GET" style="display: flex; align-items: center; gap: var(--space-4);">
            <div class="header-search" style="max-width: 300px;">
                <span class="material-icons search-icon">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm mã ĐH, SĐT, Tên..." id="order-search">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: var(--space-6);">
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter Tabs --}}
    <div style="display: flex; gap: var(--space-2); margin-bottom: var(--space-6); overflow-x: auto; padding-bottom: 4px;" id="order-tabs">
        <a href="{{ route('admin.orders') }}" class="btn btn-sm {{ !request('trang_thai') ? 'btn-primary' : 'btn-outline' }}">
            Tất cả ({{ array_sum($statusCounts) }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'cho_thanh_toan']) }}" class="btn btn-sm {{ request('trang_thai') == 'cho_thanh_toan' ? 'btn-primary' : 'btn-outline' }}">
            Chờ thanh toán ({{ $statusCounts['cho_thanh_toan'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'cho_xac_nhan']) }}" class="btn btn-sm {{ request('trang_thai') == 'cho_xac_nhan' ? 'btn-primary' : 'btn-outline' }}">
            Chờ xác nhận ({{ $statusCounts['cho_xac_nhan'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'dang_xu_ly']) }}" class="btn btn-sm {{ request('trang_thai') == 'dang_xu_ly' ? 'btn-primary' : 'btn-outline' }}">
            Đang lấy hàng ({{ $statusCounts['dang_xu_ly'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'dang_giao']) }}" class="btn btn-sm {{ request('trang_thai') == 'dang_giao' ? 'btn-primary' : 'btn-outline' }}">
            Đang giao ({{ $statusCounts['dang_giao'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'da_giao']) }}" class="btn btn-sm {{ request('trang_thai') == 'da_giao' ? 'btn-primary' : 'btn-outline' }}">
            Hoàn thành ({{ $statusCounts['da_giao'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'huy']) }}" class="btn btn-sm {{ request('trang_thai') == 'huy' ? 'btn-primary' : 'btn-outline' }}">
            Đã hủy ({{ $statusCounts['huy'] ?? 0 }})
        </a>
    </div>

    {{-- Orders Table --}}
    <div class="table-wrapper" id="orders-table">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>P.Thức</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donHangs as $dh)
                <tr>
                    <td style="font-weight: var(--font-semibold);">#MB{{ str_pad($dh->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div style="font-weight: var(--font-medium);">{{ $dh->ho_ten ?? ($dh->user->ho_ten ?? 'Khách vãng lai') }}</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">{{ $dh->so_dien_thoai }}</div>
                    </td>
                    <td style="color: var(--color-text-muted);">{{ $dh->created_at->format('d/m/Y H:i') }}</td>
                    <td style="font-weight: var(--font-semibold); color: var(--color-danger);">{{ number_format($dh->tong_tien ?? 0, 0, ',', '.') }}đ</td>
                    <td>
                        <span class="badge" style="background:var(--color-bg); color:var(--color-text-muted);">
                            {{ strtoupper($dh->phuong_thuc_thanh_toan) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $st = $dh->trang_thai;
                            if($st == 'cho_thanh_toan')      $badge = 'badge-warning';
                            elseif($st == 'cho_xac_nhan')    $badge = 'badge-info';
                            elseif($st == 'dang_xu_ly')      $badge = 'badge-primary';
                            elseif($st == 'dang_giao')       $badge = 'badge-info';
                            elseif($st == 'da_giao')         $badge = 'badge-success';
                            else                             $badge = 'badge-danger';

                            $labels = [
                                'cho_thanh_toan' => 'Chờ TT',
                                'cho_xac_nhan'   => 'Chờ xác nhận',
                                'dang_xu_ly'     => 'Đang lấy hàng',
                                'dang_giao'      => 'Đang giao',
                                'da_giao'        => 'Hoàn thành',
                                'huy'            => 'Đã hủy',
                            ];
                        @endphp
                        <span class="badge {{ $badge }}">{{ $labels[$st] ?? $st }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.show', $dh->id) }}" class="btn btn-outline btn-sm" title="Xem chi tiết">Chi tiết</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: var(--space-10); color: var(--color-text-muted);">
                        <span class="material-icons" style="font-size: 48px; display: block; margin-bottom: var(--space-3);">receipt_long</span>
                        Không tìm thấy đơn hàng nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($donHangs->hasPages())
    <div style="margin-top: var(--space-6);">
        {{ $donHangs->links('pagination::bootstrap-4') }}
    </div>
    @endif
@endsection
