<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\Sach;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class SampleOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('vi_VN');
        $users = User::all();
        $sachs = Sach::where('so_luong_ton', '>', 0)->get();

        if ($users->isEmpty()) {
            echo "No users found. Please seed users first.\n";
            return;
        }

        if ($sachs->isEmpty()) {
            echo "No books in stock found. Please seed books first.\n";
            return;
        }

        echo "Generating 100 sample orders...\n";

        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $itemCount = rand(1, 5);
            $randomBooks = $sachs->random(min($itemCount, $sachs->count()));
            
            $totalAmount = 0;
            
            // Create DonHang
            $donHang = DonHang::create([
                'user_id' => $user->id,
                'ngay_dat' => $faker->dateTimeBetween('-6 months', 'now'),
                'trang_thai' => $faker->randomElement(['cho_duyet', 'da_xac_nhan', 'dang_giao', 'hoan_thanh', 'da_huy']),
                'tong_tien' => 0, // Will calculate
                'dia_chi_giao' => $faker->address,
                'phuong_thuc_tt' => $faker->randomElement(['cod', 'vnpay', 'bank', 'momo']),
                'trang_thai_tt' => $faker->randomElement(['chua_thanh_toan', 'da_thanh_toan']),
                'ghi_chu' => $faker->optional(0.3)->sentence(),
            ]);

            foreach ($randomBooks as $sach) {
                $qty = rand(1, 3);
                $price = $sach->gia_ban;
                $lineTotal = $price * $qty;
                $totalAmount += $lineTotal;

                DonHangChiTiet::create([
                    'don_hang_id' => $donHang->id,
                    'sach_id' => $sach->id,
                    'so_luong' => $qty,
                    'don_gia' => $price,
                    'thanh_tien' => $lineTotal,
                ]);
            }

            // Update total
            $donHang->update(['tong_tien' => $totalAmount]);
        }

        echo "Successfully created 100 sample orders.\n";
    }
}
