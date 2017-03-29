<?php

/*
|--------------------------------------------------------------------------
| Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the module.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['prefix' => 'ticket', 'middleware' => ['web']], function () {
	//
});



// Routes in this group must be authorized.
Route::group(['middleware' => 'authorize'], function () {

    // Ticket routes
    Route::group(['prefix' => 'ticket'], function () {
        Route::get(   '/',                        ['as' => 'ticket.index',               'uses' => 'TicketController@index']);
        Route::post(  '/',                        ['as' => 'ticket.store',               'uses' => 'TicketController@store']);
        Route::get(   'create',                   ['as' => 'ticket.create',              'uses' => 'TicketController@create']);
        Route::get(   '{itemID}',                 ['as' => 'ticket.show',                'uses' => 'TicketController@show']);
        Route::patch( '{itemID}',                 ['as' => 'ticket.patch',               'uses' => 'TicketController@update']);
        Route::get(   '{itemID}/edit',            ['as' => 'ticket.edit',                'uses' => 'TicketController@edit']);
        Route::get(   '{itemID}/confirm-delete',  ['as' => 'ticket.confirm-delete-item', 'uses' => 'TicketController@getModalDeleteItem']);
        Route::get(   '{itemID}/delete',          ['as' => 'ticket.delete',              'uses' => 'TicketController@destroyItem']);
        Route::get(   '{itemID}/sign-out',        ['as' => 'ticket.sign-out',            'uses' => 'TicketController@getSignOut']);
        Route::post(  '{itemID}/sign-out',        ['as' => 'ticket.post-sign-out',       'uses' => 'TicketController@postSignOut']);
        Route::get(   '{itemID}/confirm-sign-in', ['as' => 'ticket.confirm-sign-in',     'uses' => 'TicketController@getModalSignIn']);
        Route::get(   '{itemID}/sign-in',         ['as' => 'ticket.sign-in',             'uses' => 'TicketController@signIn']);

    }); // End of Ticket group


}); // end of AUTHORIZE middleware group
