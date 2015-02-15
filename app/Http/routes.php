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

Route::group(['domain' => '{tenant}.pg.app'], function()
{

	// Route::get('/', function($account){
	// 	$tenant = App\Tenant::where('subdomain', $account)->first();

	// 	if (count($tenant) > 0) {
	// 		PGSchema::switchTo($tenant->subdomain);

	//

	// 		// Display login page
	// 	}

	// });

});


Route::get('/', function(){
	return View::make('welcome');
});

Route::get('/home', 'HomeController@index');


Route::group(['prefix' => 'ajax'], function(){

	Route::post('name-available', function(){
		
		$url = Request::get('company_username');
		$tenant = App\Tenant::where('schema_name', $url)->count();

		if ($tenant > 0) {
			return response()->json(['true']);
		}else{
			return response()->json(['false']);
		}

	});

});



Route::group(['prefix' => 'auth'], function(){

	Route::get('register', ['as' => 'auth-register', 'uses' => 'Auth\RegistrationController@getRegister']);
	Route::post('register', ['as' => 'auth-register-send', 'uses' => 'Auth\RegistrationController@postRegister']);

	Route::get('login', 'Auth\AuthController@getLogin');
	Route::post('login','Auth\AuthController@authenticate');
	
	Route::get('logout','Auth\AuthController@logout');

});

// Route::controllers([
// 	'auth' => 'Auth\AuthController',
// 	'password' => 'Auth\PasswordController',
// ]);
