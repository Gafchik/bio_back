<?php

namespace App\Http\Classes\LogicalModels\News;

use App\Http\Classes\Structure\Lang;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MySql\Biodeposit\{
    News as NewsTable,
    News_translations,
};

class NewsModel
{
    private const PER_PAGE = 10;
    public function __construct(
        private NewsTable $news,
        private News_translations $newsTranslations,
    ){}

    public function getCards(int $page): array
    {
        // Вычисляем смещение для выборки данных
        $offset = ($page - 1) * self::PER_PAGE;
        $nameRu = $this->getSubQuery(Lang::RUS,'name');
        $nameUk = $this->getSubQuery(Lang::UKR,'name');
        $nameEn = $this->getSubQuery(Lang::ENG,'name');
        $nameGe = $this->getSubQuery(Lang::GEO,'name');

        $shortContentRu = $this->getSubQuery(Lang::RUS,'short_content');
        $shortContentUk = $this->getSubQuery(Lang::UKR,'short_content');
        $shortContentEn = $this->getSubQuery(Lang::ENG,'short_content');
        $shortContentGe = $this->getSubQuery(Lang::GEO,'short_content');

        return $this->news->select([
            'id as id_card',
            'image',
            'view_count',
            'date',
            'created_at',
        ])
            ->selectRaw("({$nameRu->toSql()}) as name_ru")
            ->mergeBindings($nameRu->getQuery())
            ->selectRaw("({$nameUk->toSql()}) as name_uk")
            ->mergeBindings($nameUk->getQuery())
            ->selectRaw("({$nameEn->toSql()}) as name_en")
            ->mergeBindings($nameEn->getQuery())
            ->selectRaw("({$nameGe->toSql()}) as name_ge")
            ->mergeBindings($nameGe->getQuery())
            ->selectRaw("({$shortContentRu->toSql()}) as short_content_ru")
            ->mergeBindings($shortContentRu->getQuery())
            ->selectRaw("({$shortContentUk->toSql()}) as short_content_uk")
            ->mergeBindings($shortContentUk->getQuery())
            ->selectRaw("({$shortContentEn->toSql()}) as short_content_en")
            ->mergeBindings($shortContentEn->getQuery())
            ->selectRaw("({$shortContentGe->toSql()}) as short_content_ge")
            ->mergeBindings($shortContentGe->getQuery())
            ->where('status','=',true)
            ->orderByDesc('id')
            ->skip($offset)
            ->take(self::PER_PAGE)
            ->get()
            ->toArray();
    }
    private function getSubQuery(string $lang,string $filed): Builder
    {
        return $this->newsTranslations
            ->where('locale',$lang)
            ->whereRaw('news_id = id_card')
            ->select([$filed]);
    }
}
