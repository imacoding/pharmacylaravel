@if ($message = session('success'))
<div class="alert alert-success alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
    <h5 style="text-align: center">
	    <strong>{{ $message }}</strong>
	</h5>
</div>
@endif

@if (session('status'))
	<div class="alert alert-success alert-block">
		<button type="button" class="close" data-dismiss="alert">×</button>	
		<h5 style="text-align: center">
			<strong>{{ session('status') }}</strong>
		</h5>
	</div>
@endif

@if ($message = session('error'))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<h5 style="text-align: center">
		<strong>{{ $message }}</strong>
	</h5>
</div>
@endif


@if ($message = session('warning'))
<div class="alert alert-warning alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<h5 style="text-align: center">
		<strong>{{ $message }}</strong>
	</h5>
</div>
@endif


@if ($message = session('info'))
<div class="alert alert-info alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<h5 style="text-align: center">
		<strong>{{ $message }}</strong>
	</h5>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<h5 style="text-align: center"> 
		@foreach ($errors->all() as $error)
			<strong>{{ $error }}</strong><br>
		@endforeach
	</h5>
</div>
@endif



