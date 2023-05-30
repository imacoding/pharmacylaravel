@include('admin/header')
<script>
  $(function()
  {
      $('#dash').removeClass('active');
      $('#requested').addClass('active');
  });
</script>
<section id="content">
  <!-- <section class="vbox">
    <section class="scrollable padder"> -->
      <div class="m-b-md">
        <h3 class="m-b-none">Requested Medicines List</h3>
      </div>
      <section class="panel panel-default">
        <!-- <table class="table table-striped m-b-none dataTable new-med-tble"> -->
        <table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble">
	        <thead>
            <tr>
              <th>No.</th>
              <th>Medicine Name</th>
              <th>Requested Count</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
	        </thead>	      
	      </table>
      </section>
    <!-- </section>
  </section> -->
</section>

@include('admin/footer')

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Requested Emails</h4>
      </div>
      <div class="modal-body" id="emails">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  function show_email(med)
  {
    $.ajax({
      url:'{{ URL::to('admin/new-medicine-email' )}}',
      type:'GET',
      data:'med='+med,
      datatype: 'JSON',
      success: function(data){
        if(data.length > 0) {
          var email="";
          $.each(data,function($key,$d) {
            email+="<p>"+$d.email+"</p>";
          });
        } else email = 'Not Available';
        $('#emails').html(email);
      }
    });
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
			"url": "{{ route('admin.newMedList') }}",
			"dataType": "json",
			"type": "GET",
			"data": {
			_token: "{{csrf_token()}}"
			}
		},
		"language": {
			"searchPlaceholder": "Search by medicine name",
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
				name: 'name',
				data: 'name'
			},
			{
				name: 'count',
				data: 'count',			
			},
			{
        name: 'created_at',
        "render": function (data, type, row) {
          return moment(new Date(row.created_at)).format("DD-MMM-YYYY");
        },
      },
			{
				data: null,
				"render": function(data) {
					var html = '';
					var delete_route = "{{ route('admin.delete-new-medicine', ['newMedID' => 'M_PROF_ID' ]) }}";

					delete_route = 	delete_route.replace('M_PROF_ID', data.id);

          html = '<a class="btn btn-s-md btn-info btn-rounded"  data-toggle="modal" data-target="#myModal" onclick="show_email(' + data.id +')">View Requested Emails</a>' +
	                '<a class="btn btn-s-md btn-danger btn-rounded" href="' + delete_route + '" onclick="return confirm("Do you really want to delete this requested medicine?");">Delete</a>';
		
					return html;
				}
			},
		],
		columnDefs: [
    		{ orderable: false, targets: [0, 4] },
        {
          targets: [ 1 ],
          orderData: [ 1, 2, 3]
        }, {
          targets: [ 3 ],
          orderData: [ 3, 1 ]
        } 
      ]
		});
	});
</script>