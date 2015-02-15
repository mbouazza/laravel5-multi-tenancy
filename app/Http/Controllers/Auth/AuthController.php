<?php namespace App\Http\Controllers\Auth;

use Auth;
use Validator;
use Request;
use Hash;
use Illuminate\Routing\Controller;

class AuthController extends Controller {

	public function getRegister(){
		return view('auth.register');		
	}

	public function postRegister(){

		$data = Request::all();

		$validator = Validator::make($data, [
			'company_name' 		=> 'required|max:255',
			'company_username'	=> 'required|max:255|unique:tenants,schema_name',
			'name' 				=> 'required|max:255',
			'email' 			=> 'required|email|max:255|unique:users',
			'password' 			=> 'required|confirmed|min:6',
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withInput()
				->withErrors($validator);
		}

		$tenant = $this->createTenant($data);

		$this->createUser($data, $tenant);

		// Send email confirmation

		return redirect('/');
	}


	public function createTenant($data){
		$tenant = new \App\Tenant;
		$tenant->company_name 	= $data['company_name'];
		$tenant->schema_name 	= $data['company_username'];
		$tenant->save();

		\PGSchema::create($data['company_username']);
		// Do all migrations in here

		return $tenant->id; 
	}

	public function createUser($data, $tenantId){
		$user = new \App\User;
		$user->name 	= $data['name'];
		$user->email 	= $data['email'];
		$user->password = Hash::make($data['password']);
		$user->tenant_id = $tenantId;
		$user->save();

		return $user->id;
	}

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