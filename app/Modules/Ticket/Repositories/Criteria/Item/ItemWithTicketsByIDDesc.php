<?php namespace App\Modules\Ticket\Repositories\Criteria\Item;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class ItemWithTicketsByIDDesc extends Criteria {


    /**
     * @param $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply( $model, Repository $repository )
    {
        $model = $model->with(['tickets' => function ($query) {
            $query->orderBy('id', 'desc');
        }]);

        return $model;
    }

}
