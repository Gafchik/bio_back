<?php

namespace App\Providers;

use App\Http\Classes\Core\Archive\Archive;
use App\Http\Classes\Core\Archive\ArchiveInterface;
use App\Http\Classes\Core\BaseResponse\BaseResponse;
use App\Http\Classes\Core\BaseResponse\BaseResponseInterface;
use App\Http\Classes\Core\Pdf\PdfGenerator;
use App\Http\Classes\Core\Pdf\PdfGeneratorInterface;
use App\Http\Classes\Core\UserInfo\UserInfo;
use App\Http\Classes\Core\UserInfo\UserInfoInterface;
use App\Http\Classes\LogicalModels\Common\AvailableTree\AvailableTree;
use App\Http\Classes\LogicalModels\Common\AvailableTree\AvailableTreeInterface;
use App\Http\Classes\LogicalModels\Common\WorkWithPromo\WorkWithPromo;
use App\Http\Classes\LogicalModels\Common\WorkWithPromo\WorkWithPromoInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //        response
        $this->app->singleton(BaseResponseInterface::class, BaseResponse::class);
        $this->app->singleton('base_response_facade', BaseResponseInterface::class);
        //        pdf
        $this->app->singleton(PdfGeneratorInterface::class, PdfGenerator::class);
        $this->app->singleton('pdf_facade', PdfGeneratorInterface::class);
//        archive_facade
        $this->app->singleton(ArchiveInterface::class, Archive::class);
        $this->app->singleton('archive_facade', ArchiveInterface::class);
//      available_tree_facade
        $this->app->singleton(AvailableTreeInterface::class, AvailableTree::class);
        $this->app->singleton('available_tree_facade', AvailableTreeInterface::class);
        //work_with_promo_facade
        $this->app->singleton(WorkWithPromoInterface::class, WorkWithPromo::class);
        $this->app->singleton('work_with_promo_facade', WorkWithPromoInterface::class);
        //        api auth
        $this->app->singleton(UserInfoInterface::class, UserInfo::class);
        $this->app->singleton('user_info_facade', UserInfoInterface::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
