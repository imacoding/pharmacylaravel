@include('admin/header')
<script>
	$(function()
	{
		$('#dash').removeClass('active');
		$('#users').addClass('active');
	});
</script>
<section id="content">
	<!-- <section class="vbox">
  		<section class="scrollable padder"> -->
  			<div class="m-b-md">
                <h3 class="m-b-none">Medical Professionals</h3>
  			</div>
  			<section class="panel panel-default">
  				<table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble users-list">
					<thead>
	  					<tr>
							<th>No.</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Name</th>
							<th>Address</th>
							<th>Pincode</th>
							<th></th>
	  					</tr>
					</thead>
	
				</table>
 			<!-- </section>
		</section> -->
	</section>
</section>
<script src="{{ url('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ url('assets/js/jquery-1.11.2.min.js') }}"></script>
<script>
	jQuery(function($) {
		$('#js-provider-list').DataTable({
		"processing": true,
		"serverSide": true,
		"ordering": true,
		"lengthMenu": [[30, 60, 120], [30, 60, 120]],
		scrollY: 700,
		scrollCollapse: true,
		scroller:       true,
		"info": true,
		"autoWidth": false,
		"responsive": true,
		"ajax": {
			"url": "{{ route('admin.mproflist') }}",
			"dataType": "json",
			"type": "GET",
			"data": {
			_token: "{{csrf_token()}}"
			}
		},
		"language": {
			"searchPlaceholder": "Search user by email",
			search: "",
		},
		"columns": [
			{
				name: 'id',
				data: "id",
				render: function (data, type, row, meta) {
					return meta.row + meta.settings._iDisplayStart + 1;
				}
			},
			{
				name: 'prof_mail',
				data: 'prof_mail'
			},
			{
				name: 'prof_phone',
				data: 'prof_phone',			
			},
			{ name: 'prof_name',data: 'prof_name'},
			{ name: 'prof_address',data: 'prof_address'},
			{ name: 'prof_pincode',data: 'prof_pincode'},
			{
				data: null,
				"render": function(data) {
					var html = '';
					var delete_route = "{{ route('admin.mprof-delete', ['mprof_id' => 'M_PROF_ID' ]) }}";
					var status_route = "{{ route('admin.prof-change-status', ['mprof_id' => 'M_PROF_ID' ]) }}";

					delete_route = 	delete_route.replace('M_PROF_ID', data.id);
					html = '<a href="' + delete_route.replace('M_PROF_ID', data.id) + '" class="btn btn-s-md btn-danger btn-rounded usr-delt" onclick="return confirm("Do you really want to delete ' + data.prof_mail + '?");>Delete</a>';
					if (data.userStatus == 1) {
						status_route = status_route.replace('M_PROF_ID', data.id);
						html += '<a href="' + status_route + '" class="btn btn-s-md btn-success btn-rounded customer-publish" >Publish</a>'
					}
						
					return html;
				}
			},
		],
		columnDefs: [
    		{ orderable: false, targets: [0, 2, 4,5, 6] },
			{
				targets: [ 1 ],
				orderData: [ 1]
			}, {
				targets: [ 3 ],
				orderData: [ 3, 4 ]
			}, 
			{
				"width": "6%",
				"targets": [6]              
			},
			{
				"width": "11%",
				"targets": [2]              
			}
  		]
		});
	});
</script>
@include('admin/footer')
