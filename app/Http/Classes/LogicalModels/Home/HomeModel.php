<?php

namespace App\Http\Classes\LogicalModels\Home;

use App\Models\MySql\Biodeposit\{
    Activations,
    News as NewsTable,
    Transactions,
    Trees, Users,
    Variables,
    Videos,
    News_translations,
};
use App\Http\Classes\Structure\Lang;
use Illuminate\Database\Eloquent\Builder;

class HomeModel
{
    public function __construct(
        private Trees $trees,
        private Users $users,
        private Activations $activationsEmail,
        private Transactions $transactions,
        private Variables $pageVariables,
        private Videos $videos,
        private NewsTable $news,
        private News_translations $newsTranslations,
    ){}

    public function getAllCountTrees(): int
    {
        return $this->trees->count();
    }
    public function getPriceToLiter(): int
    {
        return $this->pageVariables
            ->where('key','oil_price')
            ->pluck('value')
            ->first();
    }
    public function getTreeAllPrice(): int
    {
        $price = $this->trees->sum('current_price');
        return $price /100;
    }
    public function getCountTransactions(): int
    {
        return $this->transactions
            ->where('status',1)->count();
    }
    public function getCountYongTrees(): int
    {
        return $this->trees->where('is_young',true)->count();
    }
    public function getCountUsers(): int
    {
        return $this->users
            ->from($this->users->getTable(), 'users')
            ->leftJoin($this->activationsEmail->getTable() . ' as activationsEmail',
                'activationsEmail.user_id',
                '=',
                'users.id',
            )
            ->where('activationsEmail.completed', true)
            ->count();
    }
    public function getVideos(): array
    {
        return $this->videos
            ->where('status',true)
            ->orderBy('position')
            ->select([
                'id',
                'video',
            ])
            ->get()
            ->toArray();
    }
    public function getFirstNews()
    {
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
            ->first()
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
