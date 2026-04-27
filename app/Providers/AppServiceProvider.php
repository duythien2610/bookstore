<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Fix Ngrok / Reverse Proxy HTTPS issue cho Login form:
        if (request()->header('x-forwarded-proto') === 'https' || str_contains(request()->getHost(), 'ngrok-free.app')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Pagination\Paginator::useBootstrap();
        // Share tree category globally for Header Navigation
        view()->composer('*', function ($view) {
            // Thể loại cha cho sách trong nước (loai_sach = trong_nuoc hoặc tat_ca)
            $menuCategoriesTrongNuoc = \App\Models\TheLoai::with(['children' => function($q){
                $q->orderBy('ten_the_loai');
            }])
            ->whereNull('parent_id')
            ->whereIn('loai_sach', ['trong_nuoc', 'tat_ca'])
            ->orderBy('ten_the_loai')
            ->get();

            // Thể loại cha cho sách nước ngoài (loai_sach = nuoc_ngoai hoặc tat_ca)
            $menuCategoriesNuocNgoai = \App\Models\TheLoai::with(['children' => function($q){
                $q->orderBy('ten_the_loai');
            }])
            ->whereNull('parent_id')
            ->whereIn('loai_sach', ['nuoc_ngoai', 'tat_ca'])
            ->orderBy('ten_the_loai')
            ->get();

            // Giữ lại biến cũ (tất cả) cho sidebar filter trang products
            $menuCategories = $menuCategoriesTrongNuoc->merge($menuCategoriesNuocNgoai);

            $view->with('menuCategoriesTrongNuoc', $menuCategoriesTrongNuoc);
            $view->with('menuCategoriesNuocNgoai', $menuCategoriesNuocNgoai);
            $view->with('menuCategories', $menuCategories);
        });
    }
}
