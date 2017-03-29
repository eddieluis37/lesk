<?php namespace App\Modules\Ticket\Repositories;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;

class ItemRepository extends Repository {

    public function model()
    {
        return 'App\Modules\Ticket\Models\Item';
    }

}
