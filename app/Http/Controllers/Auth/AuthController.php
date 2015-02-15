<?php namespace App\Http\Controllers\Auth;

use Auth;
use Validator;
use Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller {

	public function getLogin(){
		return view('auth.login');
	}

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate()
    {

    	$email = Request::get('email');
    	$password = Request::get('password');

        if (Auth::attempt(['email' => $email, 'password' => $password]))
        {
        	// Find authenticated user
        	$user = \App\User::find(Auth::id());

			// Switch Schema
        	\PGSchema::switchTo($user->tenant->schema_name);

        	// Redirect them home
            return redirect()->intended('/');
        }
    }


    public function logout(){
    	Auth::logout();

    	// Switch back to the public schema
    	\PGSchema::switchTo('public');

    	return redirect('/')
    		->withMessage('You have been logged out.');
    }
}