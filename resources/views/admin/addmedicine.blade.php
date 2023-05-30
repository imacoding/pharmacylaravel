@include('admin/header')
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.js"></script>
	<section id="content">
		<section class="vbox">
  			<section class="scrollable padder">
    			<div class="m-b-md">
                	<h3 class="m-b-none">Add/Edit Medicine</h3>
  				</div>
  
        		<section class="panel panel-default">
        			<?php $message = Session::get('message',null); ?>
        			@if(!is_null($message))
            			<p class="alert alert-danger">{{ $message }}</p>
        			@endif
 					<header class="panel-heading font-bold">Update Medicine Database</header>
					<strong id="div-notify" style="color: red"></strong>
					<form method="POST" class="panel-body" action="{{ url('/admin/new-med') }}" enctype="multipart/form-data">

						 {{ csrf_field() }}

						<div class="form-group">
							<label for="email">Item Code:</label>
							<input class="form-control"  name="item_code" type="text" value=<?php echo(isset($details['item_code']) ?"'".$details['item_code']."'":'""');?> required>
						</div>
						<input  name="id" type="hidden" value=<?php echo(isset($details['id']) ?"'".$details['id']."'":'""');?> >
						<div class="form-group">
							<label for="email">Item Name:</label>
							<input class="form-control"   name="item_name" type="text" value=<?php echo(isset($details['item_name']) ?"'".$details['item_name']."'":'""');?> required>
						</div>
						<div class="form-group">
							<label for="email">Batch No.:</label>
							<input class="form-control"   name="batch_no" type="text" value=<?php echo(isset($details['batch_no']) ?  "'".$details['batch_no']."'":'""');?> >
						</div>
						<div class="form-group">
							<label for="email">Cost Price:</label>
							<input class="form-control"  name="cost_price" type="text" value=<?php echo(isset($details['cost_price']) ?  "'".$details['cost_price']."'":'""');?> >
							<input class="form-control"  type="hidden" name="quantity"  value=<?php echo(isset($details['quantity']) ?  "'".$details['quantity']."'":'""');?> >
						</div>
						<div class="form-group">
							<label for="email">Purchase Price:</label>
							<input class="form-control"  name="purchase_price" type="text" value=<?php echo(isset($details['purchase_price']) ?  "'".$details['purchase_price']."'":'""');?> >
						</div>
						<div class="form-group">
							<label for="email">Rack:</label>
							<input class="form-control"  name="rack_number" type="text" value=<?php echo(isset($details['rack_number']) ?  "'".$details['rack_number']."'":'""');?> >
						</div>
						<div class="form-group">
							<label for="email">MRP:</label>
							<input class="form-control"  name="selling_price" type="text" value=<?php echo(isset($details['selling_price']) ?  "'".$details['selling_price']."'":'""');?> required>
						</div>
						<div class="form-group">
							<label for="email">Expiry Date:</label>
							<input class="form-control"  id="expiry" name="expiry" type="text" value=<?php echo(isset($details['expiry']) ?  "'".$details['expiry']."'":'""');?> required>
						</div>
						<div class="form-group">
							<label for="email">Tax:</label>
							<input class="form-control"  name="tax" type="text" value=<?php echo(isset($details['tax']) ?  "'".$details['tax']."'":'""');?> required>
						</div>
						<div class="form-group">
							<label for="email">Composition:</label>
							<input class="form-control"  name="composition" type="text" value=<?php echo(isset($details['composition'])?"'".$details['composition']."'":'""');?> >
						</div>
						<div class="form-group">
							<label for="email">Manufacturer:</label>
							<input class="form-control"  name="manufacturer" type="text" value=<?php echo(isset($details['manufacturer'])?"'".$details['manufacturer']."'":'""');?> >
						</div>
						<div class="form-group">
							<label for="email">Nature of drug:</label>
							<input class="form-control"  name="group" type="text" value=<?php echo(isset($details['group'])?"'".$details['group']."'":'""');?> >
						</div>
						<div class="form-group">
							<label for="email">Discount:</label>
							<input class="form-control"  name="discount" type="text" value=<?php echo(isset($details['discount'])?"'".$details['discount']."'":'"0"');?> >
						</div>
						<div class="form-group">
							<label for="email">Is Prescription Required:</label>
							<select class="form-control" name="is_prescription">
								<option value="1"  <?= (isset($details['is_pres_required']) && $details['is_pres_required'] == 1) ? 'selected' : '' ?>>Yes</option>
								<option value="0"  <?= (isset($details['is_pres_required']) && $details['is_pres_required'] == 0) ? 'selected' : '' ?>>No</option>
							</select>
						</div>
						<?php 
							$img = '' ;
							if (isset($details['product_image']) && !empty($details['product_image']) && Storage::disk('MEDICINE_THUMB')->exists($details['product_image']))
								$img = Storage::disk('MEDICINE_THUMB')->url($details['product_image']);
							else if (isset($details['product_image']) && !empty($details['product_image']) && Storage::disk('MEDICINE_PIC')->exists($details['product_image']))
								$img = Storage::disk('MEDICINE_PIC')->url($details['product_image']);
						?>
						<div class="form-group" id="img-wrap">
							<label for="email">Image:</label>
							<div class="img-wrap" >
								@if($img)
									<a href="{{ asset('storage/medicine/' . $details['product_image']) }}" target="_blank">
    <img src="{{ asset('storage/medicine/' . $details['product_image']) }}" />
</a>

								@endif
								<input name="file" type="file" accept="image/png, image/jpg, image/jpeg" onchange="readURL(this);">
							</div>
							
						</div>
						<div class="form-group">
							<button class="btn btn-default btn-lg" name="submit" value="Add" type="submit">Add</button>
						</div>
					</form>
 				</section>
			</section>
		</section>

		@include('admin/footer')
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script>
			$(document).ready(function(e){
				
    			$( "#expiry" ).datepicker({ minDate: 0});
			});
			function readURL(input) {
				if (input.files && input.files[0]) {
					var extension = input.files[0].type.split('/').pop().toLowerCase();
					if ($.inArray(extension, ['jpg', 'jpeg', 'png']) == -1) {
						$('body').removeClass('keyboard-open');
						$('<span class="img-error error">Upload only jpg, jpeg, png files.</span>').insertAfter('#img-wrap');
						return false;
					} 
					var reader = new FileReader();
					reader.onload = function (e) {
						$('.img-wrap a').remove();						
						$('<img src="' + e.target.result + '"/>').insertBefore('.img-wrap input[type="file"]');
					};
					reader.readAsDataURL(input.files[0]);
				}
			}
		</script>