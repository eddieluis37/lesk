<?php
namespace App\Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Libraries\FlashLevel;
use App\Libraries\Utils;
use App\Modules\Ticket\Repositories\Criteria\Item\ItemWithTicketsByIDDesc;
use App\Modules\Ticket\Repositories\ItemRepository as Item;
use App\Modules\Ticket\Repositories\Criteria\Item\ItemsByNamesAscending;
use App\Modules\Ticket\Repositories\TicketRepository as Ticket;
use App\Repositories\AuditRepository as Audit;
use Auth;
use Illuminate\Http\Request;
use Validator;

class TicketController extends Controller
{

    /**
     * @var Item
     */
    protected $item;

    /**
     * @var Ticket
     */
    protected $ticket;

    /**
     * @param Item $item
     * @param Ticket $ticket
     */
    public function __construct(Item $item, Ticket $ticket)
    {
        $this->item         = $item;
        $this->ticket  = $ticket;
        // Set default crumbtrail for controller.
        session(['crumbtrail.leaf' => 'ticket.index']);
    }


    public function index()
    {
        Audit::log(Auth::user()->id, trans('ticket::general.audit-log.category'), trans('ticket::general.audit-log.msg-index'));

        $page_title = trans('ticket::general.page.index.title');
        $page_description = trans('ticket::general.page.index.description');

        $items = $this->item->pushCriteria(new ItemsByNamesAscending())->paginate(20);

        return view('ticket::index', compact('items', 'page_title', 'page_description'));
    }

    public function show($id)
    {

        $item = $this->item->pushCriteria(new ItemWithTicketsByIDDesc())->find($id);

        Audit::log(Auth::user()->id, trans('ticket::general.audit-log.category'), trans('ticket::general.audit-log.msg-show', ['name' => $item->name]));

        $page_title = trans('ticket::general.page.show.title');
        $page_description = trans('ticket::general.page.show.description', ['name' => $item->name]);

        return view('ticket::show', compact('item', 'page_title', 'page_description'));
    }

    public function edit($id)
    {
        $item = $this->item->find($id);

        $page_title = trans('ticket::general.page.edit.title');
        $page_description = trans('ticket::general.page.edit.description', ['name' => $item->name]);

        Audit::log(Auth::user()->id, trans('ticket::general.audit-log.category'), trans('ticket::general.audit-log.msg-edit', ['name' => $item->name]));

        return view('ticket::edit', compact('item', 'page_title', 'page_description'));
    }

    public function update(Request $request, $id)
    {
        $audit_log_cat    = trans('ticket::general.audit-log.category');

        $attributes = $request->all();

        $item = $this->item->find($id);

        $validator = Validator::make($attributes, [
            'name'          => 'required|unique:' . $item->getTable() . ',name,' . $id,
            'description'   => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticket.edit', ['itemID' => $id])
                ->withErrors($validator)
                ->withInput();
        }
        else {
            $item->update($attributes);

            $msg  = trans('ticket::general.audit-log.msg-update', ['name' => $item->name]);
            Utils::FlashAndAudit($audit_log_cat, $msg, FlashLevel::SUCCESS);
        }

        return redirect()->route('ticket.index');
    }


    public function create()
    {
        $page_title = trans('ticket::general.page.create.title');
        $page_description = trans('ticket::general.page.create.description');

        $item = new \App\Modules\Ticket\Models\Item();

        return view('ticket::create', compact('item', 'page_title', 'page_description'));
    }

    public function store(Request $request)
    {
        $audit_log_cat    = trans('ticket::general.audit-log.category');

        $attributes = $request->all();

        $item = new \App\Modules\Ticket\Models\Item();

        $validator = Validator::make($attributes, [
            'name'          => 'required|unique:' . $item->getTable() . ',name',
            'description'   => 'required',
        ]);


        if ($validator->fails()) {
            return redirect()->route('ticket.create')
                ->withErrors($validator)
                ->withInput();
        }
        else {
            $this->item->create($attributes);

            $msg  = trans('ticket::general.audit-log.msg-store', ['name' => $attributes['name']]);
            Utils::FlashAndAudit($audit_log_cat, $msg, FlashLevel::SUCCESS);
        }

        return redirect()->route('ticket.index');
    }

    public function getSignOut($id)
    {
        $item = $this->item->pushCriteria(new ItemWithTicketsByIDDesc())->find($id);
        $newTicket = new \App\Modules\Ticket\Models\Ticket();

        $page_title = trans('ticket::general.page.sign-out.title');
        $page_description = trans('ticket::general.page.sign-out.description', ['name' => $item->name]);

        return view('ticket::sign-out', compact('item', 'newTicket', 'page_title', 'page_description'));

    }

    public function PostSignOut(Request $request)
    {
        $audit_log_cat    = trans('ticket::general.audit-log.category');

        $attributes = $request->all();

        $validator = Validator::make($attributes, [
            'user_id'       => 'required', // Build into the form
            'item_id'       => 'required', // Build into the form
            'reason'        => 'required',
            'from_date'     => 'required',
            'to_date'       => 'required',
        ]);


        if ($validator->fails()) {
            return redirect()->route('ticket.sign-out', $attributes['item_id'])
                ->withErrors($validator)
                ->withInput();
        }
        else {
            $itemID = $attributes['item_id'];
            $item = $this->item->find($itemID);

            $item->sign_out($attributes);

            $msg  = trans('ticket::general.audit-log.msg-signed-out', ['name' => $item->name]);
            Utils::FlashAndAudit($audit_log_cat, $msg, FlashLevel::SUCCESS);
        }

        return redirect()->route('ticket.index');
    }

    public function getModalSignIn($id)
    {
        $error = null;

        $item = $this->item->find($id);

        $modal_title = trans('ticket::general.sign-in-confirm.title');
        $modal_route = route('ticket.sign-in', array('id' => $item->id));
        $modal_body = trans('ticket::general.sign-in-confirm.body', ['id' => $item->id, 'name' => $item->name]);

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }



    public function signIn($id)
    {
        $audit_log_cat    = trans('ticket::general.audit-log.category');

        $item = $this->item->find($id);

        try {
            $item->sign_in();
            $msg  = trans('ticket::general.audit-log.msg-signed-in', ['name' => $item->name]);
            Utils::FlashAndAudit($audit_log_cat, $msg, FlashLevel::SUCCESS);
        } catch ( \Exception $e ) {
            $msg  = trans('ticket::general.status.signed-in_failed', ['name' => $item->name]);
            Utils::FlashAndAudit($audit_log_cat, $msg, FlashLevel::ERROR, $e);
        }

        return redirect()->route('ticket.index');
    }

    public function getModalDeleteItem($id)
    {
        $error = null;

        $item = $this->item->find($id);

        $modal_title = trans('ticket::general.delete-confirm.title');
        $modal_route = route('ticket.delete', array('id' => $item->id));
        $modal_body = trans('ticket::general.delete-confirm.body', ['id' => $item->id, 'name' => $item->name]);

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    public function destroyItem($id)
    {
        $audit_log_cat    = trans('ticket::general.audit-log.category');

        $item = $this->item->find($id);

        try {
            $this->item->delete($id);
            $msg  = trans('ticket::general.audit-log.msg-destroyed', ['name' => $item->name]);
            Utils::FlashAndAudit($audit_log_cat, $msg, FlashLevel::SUCCESS);
        } catch ( \Exception $e ) {
            $msg  = trans('ticket::general.status.delete_failed', ['name' => $item->name]);
            Utils::FlashAndAudit($audit_log_cat, $msg, FlashLevel::ERROR, $e);
        }

        return redirect()->route('ticket.index');
    }


}
