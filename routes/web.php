<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// all users have access to this routes


// index action
Route::get('/', 'WelcomeController@index');


// login action
Route::get('login', function () {
    return view('login');
});

// authenticated users have access to this routes

// route to get user profile
Route::get('user/{userId}',
    ['as' => 'user', 'uses' => 'UserController@getUserProfile']
);

// route to contact list adding action
Route::get('add/{userId}/{contact_list_user_id}',
    ['as' => 'addToContactList', 'uses' => 'ContactListController@addToContactList']
);

// route to send message action
Route::post('sendMessage/',
    ['as' => 'sendMessage', 'uses' => 'MessageController@sendMessage']
);

// route to change users status
Route::post('changeStatus/',
    ['as' => 'changeStatus', 'uses' => 'UserController@changeStatus']
);

// route to remove contact list
Route::get('removeFromContactList/{userId}/{contact_list_user_id}',
    ['as' => 'removeFromContactList', 'uses' => 'ContactListController@removeFromContactList']
);

// route to add contact list
Route::post('addContactList',
    ['as' => 'addContactList', 'uses' => 'HomeController@addContactList']
);

// route to remove contact list
Route::get('removeContactList/{contact_list_id}',
    ['as' => 'removeContactList', 'uses' => 'HomeController@removeContactList']
);


Auth::routes();

// route to main page with user profile, chat, inbox,  contact list and users list
Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index']);
