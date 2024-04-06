<?php

namespace App\Http\Classes\LogicalModels\Gallery;

use App\Http\Classes\Structure\Lang;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MySql\Biodeposit\{
    Images,
    Category_images,
    Category_image_translations,
};

class GalleryModel
{
    public function __construct(
        private Images  $images,
        private Category_images  $category_images,
        private Category_image_translations $category_image_translations,
    ){}

    public function getAlbums(): array
    {
        $nameRu = $this->getSubQueryLocale(Lang::RUS,'name');
        $nameUk = $this->getSubQueryLocale(Lang::UKR,'name');
        $nameEn = $this->getSubQueryLocale(Lang::ENG,'name');
        $nameGe = $this->getSubQueryLocale(Lang::GEO,'name');
        $count = $this->getSubQueryCountItems();
        return $this->category_images
            ->from($this->category_images->getTable(), 'category_images')
            ->select([
                'category_images.id',
                'category_images.category_image as album_label',
                'category_images.position',
            ])
            //В слаге должно быть надпись видео если это альбом видео
            ->selectRaw("IF(slug LIKE '%video%', 0, 1) as is_image")
            ->selectRaw("({$nameRu->toSql()}) as name_ru")
            ->mergeBindings($nameRu->getQuery())
            ->selectRaw("({$nameUk->toSql()}) as name_uk")
            ->mergeBindings($nameUk->getQuery())
            ->selectRaw("({$nameEn->toSql()}) as name_en")
            ->mergeBindings($nameEn->getQuery())
            ->selectRaw("({$nameGe->toSql()}) as name_ge")
            ->mergeBindings($nameGe->getQuery())

            ->selectRaw("({$count->toSql()}) as count")
            ->mergeBindings($count->getQuery())
            ->where('status','=',true)
            ->whereNotNull('category_images.category_image')
            ->orderByDesc('category_images.id')
            ->get()
            ->toArray();
    }
    private function getSubQueryLocale(string $lang,string $filed): Builder
    {
        return $this->category_image_translations
            ->where('locale',$lang)
            ->whereRaw('category_image_id = category_images.id')
            ->select([$filed]);
    }
    private function getSubQueryCountItems(): Builder
    {
        return $this->images
            ->whereRaw('category_id = category_images.id')
            ->selectRaw('count(*)');
    }
    public function getAlbumsDetails(array $data): array
    {
        $link = !!$data['is_image'] ? 'image' : 'video';
        return $this->images
            ->where('category_id',$data['id'])
            ->where('status','=',true)
            ->select([
                "$link as link",
                'lang',
            ])
            ->orderBy('position')
            ->get()
            ->toArray();
    }
}
