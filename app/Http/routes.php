<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix' => 'admin', 'namespace' => 'Backend'], function () {
    //login
    Route::get('/login', ['as' => 'admin.login', 'uses' => 'AuthController@getlogin']);
    Route::post('/login', ['uses' => 'AuthController@postlogin']);
    Route::get('/logout', ['as' => 'admin.logout', 'uses' => 'AuthController@logout']);
    
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/', ['as' => 'home.admin', 'uses' => 'HomeController@index']);
        Route::get('/apiborrow', 'HomeController@getApiBorrow');
        Route::get('/apiuser', 'HomeController@getApiUser');
        Route::resource('user', 'UserController');
        //book
        Route::resource('book', 'BookController');
        //category
        Route::resource('category', 'CategoryController', ['except' => ['show']]);
        //bookItem
        Route::resource('bookItem', 'BookItemController', ['only' => ['destroy']]);
        //borrow
        Route::resource('borrow', 'BorrowController', ['only' => ['index', 'show']]);
        // additional Book
        Route::resource('addbook', 'AddBookController', ['only' => ['edit', 'update']]);
        Route::resource('borrowdetail', 'BorrowDetailController', ['only' => ['index']]);
        Route::post('/back', ['as' => 'admin.back','uses' =>'BorrowDetailController@giveBack']);
        Route::get('/data/borrows', ['uses' => 'BorrowDetailController@getBorrow']);
        //add new borrow
        Route::resource('addborrow', 'AddBorrowController', ['except' => ['edit', 'update', 'destroy']]);
    });
});

Route::group(['namespace' => 'Frontend'], function () {
    // show index
    Route::get('/', ['as' => '/', 'uses' => 'IndexController@index']);
    //User login
    Route::get('/login', ['as' => 'login', 'uses' => 'AuthController@getlogin']);
    Route::post('/login', ['as' => 'login', 'uses' => 'AuthController@postlogin']);
    //User logout
    Route::get('/logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);

    // show book detail
    Route::get('/show/{show}', ['as' => 'show.book', 'uses' => 'IndexController@show']);
    // list book via category
    Route::get('/category/{category}', ['as' => 'list.category', 'uses' => 'IndexController@filter']);
    //Search
    Route::get('/search', ['as' => 'search','uses' => 'SearchController@getsearch']);
    Route::get('/search/book', ['uses' => 'SearchController@getjson']);
    //Contact
    Route::get('/contact', [ 'as' => 'contact', 'uses' => 'ContactController@getContact']);
    Route::post('/contact', ['as' => 'contact.send', 'uses' => 'ContactController@postContact']);
    
    Route::group(['middleware' => ['auth']], function () {
        //list borrow
        Route::resource('borrow', 'BorrowDetailController', ['only' => ['index']]);
        //profile
        Route::resource('profile', 'ProfileController', ['except' => ['index', 'create', 'store', 'destroy']]);
        //Change password
        Route::get('/change-password', ['as' => 'getChangePassword', 'uses' => 'ProfileController@getChangePassword']);
        Route::patch('/user/{id}/change-password', ['as' => 'changePassword', 'uses' => 'ProfileController@changePassword']);
    });
});
