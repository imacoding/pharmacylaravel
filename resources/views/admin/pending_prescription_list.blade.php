@include('admin/header')
<script>
    $(function()
    {
        $('#dash').removeClass('active');
        $('#presc').addClass('active');
    });
</script>
<section id="content">
    <!-- <section class="vbox">
        <section class="scrollable padder"> -->
            <div class="row m-b-md">
                <div class="col-lg-9">
                    <h3 class="m-b-none">Unverified Prescriptions</h3>
                </div>
            </div>
            <section class="panel panel-default">            
                <!-- <table class="table table-striped m-b-none dataTable"> -->
  				<table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble">

	                <thead>
	                    <tr>
	                        <th>No.</th>
	                        <th>From</th>
	                        <th>Date</th>
	                        <th>Prescription Status</th>
	                        <th>Actions</th>
	                    </tr>
	                </thead>
	                
	            </table>
            </section>
        <!-- </section>
    </section> -->
</section>
<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.js"></script> -->
<script src="{{ url('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ url('assets/js/jquery-1.11.2.min.js') }}"></script>
<script>
    function confirm_deletion(element){
        var conf = confirm("Do you really want to delete this order ?");
        if(conf){
            $.ajax({
                url:$(element).data('href'),
                type:'POST',
                dataType:'JSON',
                "data": {
                    _token: "{{csrf_token()}}"
                },
                statusCode:{
                    401:function(data){
                        alert('Please log in to continue...')
                        window.location.href = "/";
                    },
                    500:function(data){

                    }

                },
                success:function(data){
                      window.location.reload();
                }
            })
        }
    }

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
			"url": "{{ route('admin.load-pending-prescription-list') }}",
			"dataType": "json",
			"type": "GET",
			"data": {
            _token: "{{csrf_token()}}"
			}
		},
		"language": {
			"searchPlaceholder": "Search prescription by email",
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
				name: 'users.email',
				data: 'email'
			},
			{
				name: 'prescription.created_at',
				"render": function (data, type, row) {
                    return moment(new Date(row.created_date)).format("DD-MMM-YYYY");
                },		
			},
            {
                name: '',
                render: function(data, type, row){
                    return (row.status == 1) ? 'Pending Verification' : '';
                }
            },
			{
				data: null,
				"render": function(data) {
					var html = '';
					var delete_route = "{{ route('admin.pres-delete', ['pres_id' => 'PRES_ID' ]) }}";
					var edit_route = "{{ route('admin.pres-edit', ['pres_id' => 'PRES_ID', 'status' => 'STATUS' ]) }}";

                    edit_route = 	edit_route.replace('PRES_ID', data.pres_id).replace('STATUS', 1);
                    delete_route = 	delete_route.replace('PRES_ID', data.pres_id);

                    html = "<a class='btn btn-s-md btn-info btn-rounded' href='" + edit_route + "' >Details</a>" +
	                        "<a class='btn btn-s-md btn-danger btn-rounded' data-href='" + delete_route + "' onclick='confirm_deletion(this);'>Delete</a>";

					return html;
				}
			},
		],
		columnDefs: [
    		{ orderable: false, targets: [0, 3, 4] },
			{
				targets: [ 2 ],
				orderData: [ 2, 1 ]
			} ,
            {
				targets: [ 1 ],
				orderData: [ 1, 2 ]
			} 
  		]
		});
	});
</script>
@include('admin/footer')
