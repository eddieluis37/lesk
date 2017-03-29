<?php namespace App\Modules\Ticket\Models;

use App\Modules\Ticket\Exceptions\ItemNotSignedInException;
use App\Modules\Ticket\Exceptions\ItemNotSignedOutException;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mod_ticket_items';

    /**
     * @var array
     */
    protected $fillable = ['name', 'description', 'available'];

    public function tickets()
    {
        return $this->hasMany('App\Modules\Ticket\Models\Ticket');
    }

    public function latest_ticket()
    {
        $tickets = $this->tickets()->orderBy('id', 'desc')->limit(1)->get();
        $latestRes = $tickets->first();

        return $latestRes;
    }

    public function sign_in()
    {
        if (!$this->available) {
            $latestRes = $this->latest_ticket();
            $latestRes->return_date = date("Y-m-d H:i:s");
            $this->available = true;
            $latestRes->save();
            $this->save();
        } else {
            throw new ItemNotSignedOutException(trans('ticket::general.exceptions.item-not-signed-out', ['name' => $this->name]));
        }
    }

    public function sign_out($attributes)
    {
        if ($this->available) {
            $attributes['from_date'] = Carbon::createFromFormat('Y/m/d', $attributes['from_date']);
            $attributes['to_date'] = Carbon::createFromFormat('Y/m/d', $attributes['to_date']);

            $this->tickets()->create($attributes);
            $this->available = false;
            $this->save();
        } else {
            throw new ItemNotSignedInException(trans('ticket::general.exceptions.item-not-signed-in', ['name' => $this->name]));
        }

    }


}