@include('admin/header')
<style>
  .dropdown-menu{
    right: 0;
    left:auto;
  }
  .js-mand {
    color:red;
  }
</style>
<script>
  $(function()
  {
    $('#dash').removeClass('active');
    $('#medicine').addClass('active');
  });
</script>
<section id="content">
  <!-- <section class="vbox">
    <section class="scrollable padder"> -->
      <div class="m-b-md">
        <h3 class="m-b-none" style="margin-bottom: 10px;">Medicine</h3>
        <div class="row" style="text-align: right; padding-right: 20px;">
          <a class="btn btn-s-md btn-success btn-rounded" href="add-med"><i class="fa fa-fw fa-plus"></i> Add Medicine</a>
          <a class="btn btn-s-md btn-info btn-rounded" href="javascript:void(0)" id="upload-med"><i class="fa fa-fw fa-plus"></i> Upload Medicine</a>
        </div>
      </div>
			<section class="panel panel-default">
				<table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble">
					<thead>
						<tr>
							<th>No.</th>
							<th>Item Name</th>
							<th>Item Code</th>
							<th>Expiry Date</th>
							<th>Batch No.</th>
							<th>MFG</th>
							<th>Nature</th>
							<th>MRP</th>
              <th>Composition</th>
              <th>Is Prescription Required</th>
              <th>Actions</th>
						</tr>
					</thead>
				</table>
			</section>
		<!-- </section>
	</section> -->
</section>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload Medicine List</h4>
      </div>
      <div class="modal-body">
        <div class="alert alert-success hide">
          <strong>Success!</strong> Medicine successfully updated.
        </div>
        <div class="alert alert-danger hide">

      </div>
      <form class="" enctype="multipart/form-data" id="frmUpload">
        <div class="form-group">
         {{ csrf_field() }}

          <p>Upload .xls .xlsx file with following headers to update the medicine list. <b>(item_code ,item_name ,batch_no ,quantity ,cost_price ,purchase_price ,rack ,composition ,manufactured_by ,marketed_by ,group ,tax ,expiry ,MRP ,discount)</b></b></p>
          <p><span class="js-mand">*</span>Please enter date by this format mm/dd/yyyy</p>
          <input class="form-control" type="file" name="file" id="file" />
        </div>

      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-info" id="file_upload">Submit</button>
      </div>
    </div>

  </div>
</div>

<script>
  $(document).ready(function(e){

    $('#upload-med').click(function(e){
        $('#myModal').modal('show');
    });
    $(document).on('change', '#file', function(){
      $('#file').removeClass('error');
      $('.alert-danger').addClass('hide');
    });
    $("#file_upload").click(function(e){
      if($('#file').val() == ""){
        $('#file').addClass('error');
        $('.alert-danger').removeClass('hide').html('Please add a file to upload.');
          return false;
      } else{
        $('#file').removeClass('error');

        var fd = new FormData();
        var file_data = $('#file').prop('files')[0];
        var _token = $('#frmUpload input[name="_token"]').val();
        fd.append("file", file_data);
        fd.append("_token", _token);
        $.ajax({
            url:'{{ route('admin.bulkUpload') }}',
            type:'POST',
            data:fd,
            dataType:'JSON',
            contentType: false,
            cache: false,
            processData: false,
            statusCode:{
                400:function(data){
                    $('.alert-danger').html(data.responseJSON.msg).removeClass('hide');
                },
                403:function(data){
                    $('.alert-danger').html(data.responseJSON.msg).removeClass('hide');
                }
            },
            success:function(data){
                $('.alert-success').removeClass('hide');
                $('.alert-danger').addClass('hide');
                setTimeout(function(){ window.location.reload()},1000);
            }
        });
      }
    });
   
    });
    
    jQuery(function($) {
      $('#js-provider-list').DataTable({
      "processing": true,
      "serverSide": true,
      "ordering": true,
      "lengthMenu": [[30, 60, 120], [30, 60, 120]],
      // "pageLength" : 1,
      "info": true,
      scrollY: 650,
      scrollCollapse: true,
      scroller:       true,
      "autoWidth": false,
      "responsive": true,
      "ajax": {
        "url": "{{ route('admin.medicineslist') }}",
        "dataType": "json",
        "type": "GET",
        "data": {
        _token: "{{csrf_token()}}"
        }
      },
      "language": {
        "searchPlaceholder": "Search medicine by name",
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
          name: 'item_name',
          data: 'name'
        },
        {
          name: 'item_code',
          data: 'item_code',			
        },
        { name: 'expiry',data: 'exp'},
        { name: 'batch_no',data: 'batch_no'},
        { name: 'manufacturer',data: 'mfg'},
        { name: 'group',data: 'group'},
        { name: 'selling_price',data: 'mrp'},
        { name: 'composition',data: 'composition'},
        { name: 'is_pres_required',
          data: null,
          render: function(data){
            return (data.is_pres_required) ? 'Yes' : 'No';
          }
        },
        {
          data: null,
          "render": function(data) {
            var html = '';
            var edit_route = "{{ route('admin.medicine-edit', ['id' => 'MED_ID' ]) }}";
            var delete_route = "{{ route('admin.medicine-delete', ['id' => 'MED_ID' ]) }}";
            var prsfn_route = "{{ route('admin.medicine-prescription', ['id' => 'MED_ID' ]) }}";
            edit_route = 	edit_route.replace('MED_ID', data.id);
            delete_route = 	delete_route.replace('MED_ID', data.id);
            prsfn_route = 	prsfn_route.replace('MED_ID', data.id);
            var html = '<div class="btn-group">' +
	            '<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>' +
              '<ul class="dropdown-menu" role="menu">' +
                '<li><a href="' + edit_route + '" >Edit</a></li>' +
                '<li><a href="' + delete_route + '" onclick="return confirm("Do you really want to make this "' + data.name + '" medicine?");">Delete</a></li>' +
                '<li><a href="' + prsfn_route + '">Toggle Prescription Status</a></li>' +
              '</ul>' +
            '</div>';

            return html;
          }
        },
      ],
      columnDefs: [
          { orderable: false, targets: [0, 2, 4,5, 6, 10] },
          {
            targets: [ 1 ],
            orderData: [ 1, 2]
          }, {
            targets: [ 3 ],
            orderData: [ 3, 1 ]
          }, {
            targets: [ 9 ],
            orderData: [ 9, 1 ]
          }, {
            targets: [ 8 ],
            orderData: [ 8, 1 ]
          }
        ]
      });
    });

  </script>
@include('admin/footer')
