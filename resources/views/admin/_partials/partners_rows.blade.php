{{-- Table rows for partners (Tác giả / NXB / NCC).
     `$tab` (string) drives column labelling + empty-state CTA.
     `$rows` (Collection) is the filtered partner collection. --}}
@php
    $tabConfig = [
        'tac-gia'       => [
            'name_field' => 'ten_tac_gia',
            'icon'       => 'person',
            'bg'         => '#eff6ff',
            'color'      => '#2563eb',
            'create_url' => route('admin.tac-gia.create'),
            'entity'     => 'tác giả',
        ],
        'nha-xuat-ban'  => [
            'name_field' => 'ten_nxb',
            'icon'       => 'business',
            'bg'         => '#f0fdf4',
            'color'      => '#16a34a',
            'create_url' => route('admin.nha-xuat-ban.create'),
            'entity'     => 'NXB',
        ],
        'nha-cung-cap'  => [
            'name_field' => 'ten_ncc',
            'icon'       => 'local_shipping',
            'bg'         => '#fef3c7',
            'color'      => '#d97706',
            'create_url' => route('admin.nha-cung-cap.create'),
            'entity'     => 'NCC',
        ],
    ];
    $cfg = $tabConfig[$tab] ?? $tabConfig['tac-gia'];
@endphp

@forelse($rows as $row)
<tr>
    <td><input type="checkbox"></td>
    <td style="color: var(--color-text-muted);">#{{ $row->id }}</td>
    <td>
        <div style="display: flex; align-items: center; gap: var(--space-3);">
            <div style="width: 36px; height: 36px; background: {{ $cfg['bg'] }}; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span class="material-icons" style="font-size: 18px; color: {{ $cfg['color'] }};">{{ $cfg['icon'] }}</span>
            </div>
            <span style="font-weight: var(--font-medium);">{{ $row->{$cfg['name_field']} }}</span>
        </div>
    </td>
    <td><span class="badge badge-primary">{{ $row->sachs->count() }} sách</span></td>
    <td style="color: var(--color-text-muted); font-size: var(--font-size-sm);">{{ optional($row->created_at)->format('d/m/Y') }}</td>
    <td>
        <div style="display: flex; gap: var(--space-1);">
            <button class="btn btn-ghost btn-sm" title="Sửa"><span class="material-icons" style="font-size: 18px;">edit</span></button>
            <button class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 18px;">delete</span></button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" style="text-align: center; padding: var(--space-8); color: var(--color-text-muted);">
        @if(request('search'))
            Không tìm thấy {{ $cfg['entity'] }} nào khớp với "<strong>{{ request('search') }}</strong>".
        @else
            Chưa có {{ $cfg['entity'] }} nào. <a href="{{ $cfg['create_url'] }}">Thêm ngay!</a>
        @endif
    </td>
</tr>
@endforelse
