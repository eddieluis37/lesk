<?php namespace App\Modules\Ticket\Repositories;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;

class TicketRepository extends Repository {

    public function model()
    {
        return 'App\Modules\Ticket\Models\Ticket';
    }

}
