<?php namespace App\Http\Controllers\Auth;

use Validator;
use Hash;
use Request;
use Illuminate\Routing\Controller;

class RegistrationController extends Controller{
	public function getRegister(){
		return view('auth.register');		
	}

	public function postRegister(){

		$data = Request::all();

		$validator = Validator::make($data, [
			'company_name' 		=> 'required|max:255',
			'company_username'	=> 'required|max:255|unique:tenants,schema_name|regex:/^[A-z]+$/',
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
		
		$this->doMigrations($data)

		return $tenant->id; 
	}

	public function doMigrations($data){
		// Add in migrations using...
		//\PGSchema::Migrate($data['company_username'], []);
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
}