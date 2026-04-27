{{-- Table rows for Order Management. Shared between full page render
     and AJAX search endpoint (OrderController@index). --}}
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
        @if(request()->hasAny(['search', 'date', 'trang_thai']))
            Không tìm thấy đơn hàng nào phù hợp.
            <br>
            <a href="{{ route('admin.orders') }}" class="btn btn-outline" style="margin-top: var(--space-4);">Xóa bộ lọc</a>
        @else
            Không tìm thấy đơn hàng nào.
        @endif
    </td>
</tr>
@endforelse
