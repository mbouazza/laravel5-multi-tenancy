@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Register</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					{!! Form::open(['class' => 'form-horizontal', 'role'=> 'form', 'method'=> 'POST', 'route' => 'auth-register-send']) !!}
					<form class="form-horizontal" role="form" method="POST" action="/auth/register">
						<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">Company Name</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="company_name" value="{{ old('company_name') }}">
							</div>
						</div>

						<div class="form-group" id="company_url_group">
							<label class="col-md-4 control-label">Company Username</label>
							<div class="col-md-6">
							  	<input type="text" class="form-control" name="company_username" id="company_username">
								<p class="help-block" id="already-exists">Domain already exists.</p>
								<p class="help-block" id="letters-underscores">URLs can only contain letters and underscores.</p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Name</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="name" value="{{ old('name') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{ old('email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Confirm Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password_confirmation">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" id="submit">
									Register
								</button>
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

@endsection


@section('pagescripts')

<script type="text/javascript">
	
	function enableError(){
		companyUsernameGroup.classList.add('has-error');
		$('button[type="submit"]').attr('disabled','disabled');
	}

	function disableError(){
		companyUsernameGroup.classList.remove('has-error');
		$('button[type="submit"]').removeAttr('disabled');
	}

	var domainExists = $('#already-exists');
	domainExists.hide();

	var lettersUnderscores = $('#letters-underscores');
	lettersUnderscores.hide();

	var companyUsernameGroup = document.getElementById('company_username_group');
	var companyUsername 	= document.getElementById('company_username');
	var reg	= /^[A-z]+$/;

	companyUrl.addEventListener("keyup", function(){
		//console.log(companyUrl.value + "\n");
		var test = reg.test(companyUrl.value);
		if (test == false) {
			lettersUnderscores.show();
			enableError();
		}else{
			lettersUnderscores.hide();
			disableError();

			companyUsernamelVal = companyUrl.value;

			var prepPost = {
				'_token': $('#token').val(),
				'company_username': companyUrlVal
			};

			setTimeout(function() {
				$.post("/ajax/name-available", prepPost)
					.done(function(result){
						var returnedVal = $.parseJSON(result);
						console.log(returnedVal);
						if (returnedVal == true) {
							domainExists.show();
							enableError();
						}else{
							domainExists.hide()
							disableError();;
						}
					});

			}, 500);
		}
	});

</script>

@stop