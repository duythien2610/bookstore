<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\DonHang;
use App\Models\TheLoai;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Stats tổng quan
        $tongSach      = Sach::count();
        $tongDonHang   = DonHang::count();
        $tongKhachHang = User::whereHas('role', function($q) {
            $q->where('ten_vai_tro', 'khach_hang');
        })->count();
        
        if ($tongKhachHang == 0) $tongKhachHang = User::count(); 
        
        $tongTheLoai   = TheLoai::count();
        
        $tongDoanhThu  = DonHang::whereIn('trang_thai', ['da_giao', 'hoan_thanh'])->sum('tong_tien');
        $donHangMoi    = DonHang::where('trang_thai', 'cho_xac_nhan')->count();
        
        $sachMoi       = Sach::with('tacGia')->orderByDesc('created_at')->take(5)->get();
        
        // Data cho biểu đồ xu hướng (6 tháng gần đây)
        $months = collect();
        $orderCounts = collect();
        $revenueCounts = collect();
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M'));
            
            $orders = DonHang::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $orderCounts->push($orders);
            
            $revenue = DonHang::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->whereIn('trang_thai', ['da_giao', 'hoan_thanh'])
                ->sum('tong_tien');
            $revenueCounts->push($revenue);
        }

        $topSellingSachs = Sach::mostSold(5)->get();

        $theLoaiStats = TheLoai::withCount('sachs')
            ->orderByDesc('sachs_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'tongSach', 'tongDonHang', 'tongKhachHang', 'tongTheLoai', 'tongDoanhThu', 'donHangMoi',
            'sachMoi', 'months', 'orderCounts', 'revenueCounts', 'topSellingSachs', 'theLoaiStats'
        ));
    }

    public function exportReport()
    {
        $fileName = 'bao_cao_modtra_books_' . date('Ymd_His') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Thêm BOM để Excel hiển thị đúng UTF-8 (Vietnamese)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Section 1: Tổng quan
            fputcsv($file, ['BÁO CÁO THỐNG KÊ HỆ THỐNG MODTRA BOOKS']);
            fputcsv($file, ['Ngày xuất báo cáo:', date('d/m/Y H:i')]);
            fputcsv($file, []);

            fputcsv($file, ['THỐNG KÊ TỔNG QUAN']);
            fputcsv($file, ['Chỉ số', 'Giá trị']);
            fputcsv($file, ['Tổng số sách', Sach::count()]);
            fputcsv($file, ['Tổng số đơn hàng', DonHang::count()]);
            fputcsv($file, ['Tổng doanh thu', number_format(DonHang::whereIn('trang_thai', ['da_giao', 'hoan_thanh'])->sum('tong_tien'), 0, ',', '.') . ' VNĐ']);
            fputcsv($file, ['Tổng số khách hàng', User::count()]);
            fputcsv($file, []);

            // Section 2: Top Sách Bán Chạy
            fputcsv($file, ['TOP 20 SÁCH BÁN CHẠY NHẤT']);
            fputcsv($file, ['STT', 'Tên sách', 'Giá bán', 'Số lượng đã bán', 'Doanh thu dự tính']);
            
            $topSachs = Sach::mostSold(20)->get();
            foreach ($topSachs as $index => $sach) {
                fputcsv($file, [
                    $index + 1,
                    $sach->tieu_de,
                    $sach->gia_ban,
                    $sach->tong_ban ?? 0,
                    ($sach->gia_ban * ($sach->tong_ban ?? 0))
                ]);
            }
            fputcsv($file, []);

            // Section 3: Doanh thu theo tháng (6 tháng gần đây)
            fputcsv($file, ['DOANH THU 6 THÁNG GẦN ĐÂY']);
            fputcsv($file, ['Tháng/Năm', 'Số lượng đơn', 'Doanh thu (VNĐ)']);
            
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $orders = DonHang::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
                $revenue = DonHang::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->whereIn('trang_thai', ['da_giao', 'hoan_thanh'])->sum('tong_tien');
                
                fputcsv($file, [
                    $date->format('m/Y'),
                    $orders,
                    $revenue
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
