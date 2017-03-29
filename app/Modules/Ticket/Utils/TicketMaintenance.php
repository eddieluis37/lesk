<?php namespace App\Modules\Ticket\Utils;

use App\Models\Setting;
use DB;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use Sroutier\LESKModules\Contracts\ModuleMaintenanceInterface;
use Sroutier\LESKModules\Traits\MaintenanceTrait;

class TicketMaintenance implements ModuleMaintenanceInterface
{

    use MaintenanceTrait;


    static public function initialize()
    {
        DB::transaction(function () {

            /////////////////////////////////////////////////
            // Build database or run migration.
            self::buildDB();

            /////////////////////////////////////////////////
            // Seed the database.
            self::seed('ticket');

            //////////////////////////////////////////
            // Create permissions.
            $permTicketSeeIndex = self::createPermission(  'ticket.see-index',
                'Ticket: See item list',
                'Allows a user to see the Ticket module item list.');
            $permTicketSeeItem = self::createPermission(  'ticket.see-item',
                'Ticket: See item details',
                'Allows a user to see the Ticket module item details.');
            $permTicketSignIn = self::createPermission(  'ticket.sign-in',
                'Ticket: Sign-in item',
                'Allows a user to sign-in an item of the Ticket module.');
            $permTicketSignOut = self::createPermission(  'ticket.sign-out',
                'Ticket: Sign-out item',
                'Allows a user to sign-out an item of the Ticket module.');
            $permTicketCreateItem = self::createPermission(  'ticket.create-item',
                'Ticket: Create a new item',
                'Allows a user to create a new item in the Ticket module.');
            $permTicketEditItem = self::createPermission(  'ticket.edit-item',
                'Ticket: Edit an existing item',
                'Allows a user to edit an existing item in the Ticket module.');
            $permTicketDeleteItem = self::createPermission(  'ticket.delete-item',
                'Ticket: Delete an existing item',
                'Allows a user to delete an existing item in the Ticket module.');

            ///////////////////////////////////////
            // Register routes.
            $routeHome = self::createRoute( 'ticket.index',
                'ticket',
                'App\Modules\Ticket\Http\Controllers\TicketController@index',
                $permTicketSeeIndex );
            $routeShow = self::createRoute( 'ticket.show',
                'ticket/{itemID}',
                'App\Modules\Ticket\Http\Controllers\TicketController@show',
                $permTicketSeeItem );
            $routeGetModalSignIn = self::createRoute( 'ticket.confirm-sign-in',
                'ticket/{itemID}/confirm-sign-in',
                'App\Modules\Ticket\Http\Controllers\TicketController@getModalSignIn',
                $permTicketSignIn );
            $routeSignIn = self::createRoute( 'ticket.sign-in',
                'ticket/{itemID}/sign-in',
                'App\Modules\Ticket\Http\Controllers\TicketController@signIn',
                $permTicketSignIn );
            $routeGetSignOut = self::createRoute( 'ticket.sign-out',
                'ticket/{itemID}/sign-out',
                'App\Modules\Ticket\Http\Controllers\TicketController@getSignOut',
                $permTicketSignOut );
            $routePostSignOut = self::createRoute( 'ticket.post-sign-out',
                'ticket/{itemID}/sign-out',
                'App\Modules\Ticket\Http\Controllers\TicketController@postSignOut',
                $permTicketSignOut,
                'POST' );
            $routeCreate = self::createRoute( 'ticket.create',
                'ticket/create',
                'App\Modules\Ticket\Http\Controllers\TicketController@create',
                $permTicketCreateItem );
            $routeStore = self::createRoute( 'ticket.store',
                'ticket/',
                'App\Modules\Ticket\Http\Controllers\TicketController@store',
                $permTicketCreateItem,
                'POST' );
            $routeEdit = self::createRoute( 'ticket.edit',
                'ticket/{itemID}/edit',
                'App\Modules\Ticket\Http\Controllers\TicketController@edit',
                $permTicketEditItem );
            $routeUpdate = self::createRoute( 'ticket.patch',
                'ticket/{itemID}',
                'App\Modules\Ticket\Http\Controllers\TicketController@update',
                $permTicketEditItem,
                'PATCH' );
            $routeGetModalDeleteItem = self::createRoute( 'ticket.confirm-delete-item',
                'ticket/{itemID}/confirm-delete',
                'App\Modules\Ticket\Http\Controllers\TicketController@getModalDeleteItem',
                $permTicketDeleteItem );
            $routeDestroyItem = self::createRoute( 'ticket.delete',
                'ticket/{itemID}/delete',
                'App\Modules\Ticket\Http\Controllers\TicketController@destroyItem',
                $permTicketDeleteItem );

            ////////////////////////////////////
            // Create roles.
            self::createRole( 'ticket-users',
                'Ticket users',
                'Regular users of the Ticket module.',
                [
                    $permTicketSeeIndex->id,
                    $permTicketSeeItem->id,
                    $permTicketSignIn->id,
                    $permTicketSignOut->id,
                ] );
            self::createRole( 'ticket-power-users',
                'Ticket power users',
                'Power users of the Ticket module.',
                [
                    $permTicketSeeIndex->id,
                    $permTicketSeeItem->id,
                    $permTicketSignIn->id,
                    $permTicketSignOut->id,
                    $permTicketCreateItem->id,
                    $permTicketEditItem->id,
                ] );
            self::createRole( 'ticket-admins',
                'Ticket administrators',
                'Administrators of the Ticket module.',
                [
                    $permTicketSeeIndex->id,
                    $permTicketSeeItem->id,
                    $permTicketSignIn->id,
                    $permTicketSignOut->id,
                    $permTicketCreateItem->id,
                    $permTicketEditItem->id,
                    $permTicketDeleteItem->id,
                ] );

            ////////////////////////////////////
            // Create menu system for the module
            $menuToolsContainer = self::createMenu( 'tools-container', 'Tools', 10, 'ion ion-settings', 'home', true );
            self::createMenu( 'ticket.index', 'Ticket', 0, 'fa fa-file', $menuToolsContainer, false, $routeHome );
        }); // End of DB::transaction(....)
    }


    static public function unInitialize()
    {
        DB::transaction(function () {

            self::destroyMenu('ticket.index');
            self::destroyMenu('tools-container');

            self::destroyRole('ticket-admins');
            self::destroyRole('ticket-power-users');
            self::destroyRole('ticket-users');

            self::destroyRoute('ticket.delete');
            self::destroyRoute('ticket.confirm-delete-item');
            self::destroyRoute('ticket.patch');
            self::destroyRoute('ticket.edit');
            self::destroyRoute('ticket.store');
            self::destroyRoute('ticket.create');
            self::destroyRoute('ticket.post-sign-out');
            self::destroyRoute('ticket.sign-out');
            self::destroyRoute('ticket.sign-in');
            self::destroyRoute('ticket.confirm-sign-in');
            self::destroyRoute('ticket.show');
            self::destroyRoute('ticket.index');

            self::destroyPermission('ticket.delete-item');
            self::destroyPermission('ticket.edit-item');
            self::destroyPermission('ticket.create-item');
            self::destroyPermission('ticket.sign-out');
            self::destroyPermission('ticket.sign-in');
            self::destroyPermission('ticket.see-item');
            self::destroyPermission('ticket.see-index');

            /////////////////////////////////////////////////
            // Destroy database or rollback migration.
            self::destroyDB();

        }); // End of DB::transaction(....)
    }


    static public function enable()
    {
        DB::transaction(function () {
            self::enableMenu('ticket.index');
        });
    }


    static public function disable()
    {
        DB::transaction(function () {
            self::disableMenu('ticket.index');
        });
    }


    static public function buildDB()
    {
        Schema::create('mod_ticket_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->longText('description');
            $table->boolean('available')->default(true);
            $table->timestamps();
        });

        Schema::create('mod_ticket_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('item_id');
            $table->string('reason');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->dateTime('return_date')->nullable();
            $table->timestamps();
        });

    }


    static public function destroyDB()
    {
        Schema::dropIfExists('mod_ticket_items');
        Schema::dropIfExists('mod_ticket_tickets');

    }

}