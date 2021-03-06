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

Route::get('/',
    [
        'as' => 'welcome', 'uses' => 'WelcomeController@index'
    ]);

Route::get('home',
    [
        'as' => 'home', 'uses' => 'HomeController@index'
    ]);

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::get('profile',
    [
        'as' => 'profile.edit',
        'uses' => 'ProfileController@edit',
        'middleware' => 'auth'
    ]);

Route::put('profile',
    [
        'as' => 'profile.update',
        'uses' => 'ProfileController@update',
        'middleware' => 'auth'
    ]);

/*
|--------------------------------------------------------------------------
| Main Routes
|--------------------------------------------------------------------------
*/

Route::get('owner',
    [
        'as' => 'owner.index', 'uses' => 'OwnerController@index'
    ]);

Route::get('reviewer',
    [
        'as' => 'reviewer.index', 'uses' => 'ReviewerController@index'
    ]);

Route::get('approver',
    [
        'as' => 'approver.index', 'uses' => 'ApproverController@index'
    ]);

Route::get('signer',
    [
        'as' => 'signer.index', 'uses' => 'SignerController@index'
    ]);

/*
|--------------------------------------------------------------------------
| Admin namespace
|--------------------------------------------------------------------------
*/

Route::group(
    [
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'middleware' => 'admin'
    ],
    function () {

        /*
        |--------------------------------------------------------------------------
        | Admin/Users Routes
        |--------------------------------------------------------------------------
        */

        Route::get('/',
            [
                'as' => 'admin.index', 'uses' => 'MainController@index'
            ]);


        Route::post('/users/filter',
            [
                'as' => 'admin.users.filter',
                'uses' => 'UsersController@filter'
            ]);

        Route::get('/users/trash/{trash?}',
            [
                'as' => 'admin.users.trash',
                'uses' => 'UsersController@trash'
            ]);

        Route::get('/users/sort/{column?}/{order?}',
            [
                'as' => 'admin.users.sort',
                'uses' => 'UsersController@sort'
            ]);

        Route::delete('/users/{users}/forcedelete',
            [
                'as' => 'admin.users.forcedelete',
                'uses' => 'UsersController@forcedelete'
            ]);

        Route::post('/users/{users}/restore',
            [
                'as' => 'admin.users.restore',
                'uses' => 'UsersController@restore'
            ]);

        Route::resource('/users', 'UsersController');

        /*
        |--------------------------------------------------------------------------
        | Admin/Roles Routes
        |--------------------------------------------------------------------------
        */

        Route::post('/roles/filter',
            [
                'as' => 'admin.roles.filter',
                'uses' => 'RolesController@filter'
            ]);

        Route::get('/roles/trash/{trash?}',
            [
                'as' => 'admin.roles.trash',
                'uses' => 'RolesController@trash'
            ]);

        Route::get('/roles/sort/{column?}/{order?}',
            [
                'as' => 'admin.roles.sort',
                'uses' => 'RolesController@sort'
            ]);

        Route::delete('/roles/{roles}/forcedelete',
            [
                'as' => 'admin.roles.forcedelete',
                'uses' => 'RolesController@forcedelete'
            ]);

        Route::post('/roles/{roles}/restore',
            [
                'as' => 'admin.roles.restore',
                'uses' => 'RolesController@restore'
            ]);

        Route::resource('/roles', 'RolesController');

        /*
        |--------------------------------------------------------------------------
        | Admin/Permissions Routes
        |--------------------------------------------------------------------------
        */

        Route::post('/permissions/filter',
            [
                'as' => 'admin.permissions.filter',
                'uses' => 'PermissionsController@filter'
            ]);

        Route::get('/permissions/trash/{trash?}',
            [
                'as' => 'admin.permissions.trash',
                'uses' => 'PermissionsController@trash'
            ]);

        Route::get('/permissions/sort/{column?}/{order?}',
            [
                'as' => 'admin.permissions.sort',
                'uses' => 'PermissionsController@sort'
            ]);

        Route::delete('/permissions/{permissions}/forcedelete',
            [
                'as' => 'admin.permissions.forcedelete',
                'uses' => 'PermissionsController@forcedelete'
            ]);

        Route::post('/permissions/{permissions}/restore',
            [
                'as' => 'admin.permissions.restore',
                'uses' => 'PermissionsController@restore'
            ]);

        Route::resource('/permissions', 'PermissionsController');

        /*
        |--------------------------------------------------------------------------
        | Admin/Departments Routes
        |--------------------------------------------------------------------------
        */

        Route::post('/departments/filter',
            [
                'as' => 'admin.departments.filter',
                'uses' => 'DepartmentsController@filter'
            ]);

        Route::get('/departments/trash/{trash?}',
            [
                'as' => 'admin.departments.trash',
                'uses' => 'DepartmentsController@trash'
            ]);

        Route::get('/departments/sort/{column?}/{order?}',
            [
                'as' => 'admin.departments.sort',
                'uses' => 'DepartmentsController@sort'
            ]);

        Route::delete('/departments/{departments}/forcedelete',
            [
                'as' => 'admin.departments.forcedelete',
                'uses' => 'DepartmentsController@forcedelete'
            ]);

        Route::post('/departments/{departments}/restore',
            [
                'as' => 'admin.departments.restore',
                'uses' => 'DepartmentsController@restore'
            ]);

        Route::resource('/departments', 'DepartmentsController');


    });

/*
|--------------------------------------------------------------------------
| Auth/Password Routes
|--------------------------------------------------------------------------
*/

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
