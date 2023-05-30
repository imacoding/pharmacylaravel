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
          <h3 class="m-b-none">Shipped Prescriptions</h3>
        </div>
      </div>
      <input type="hidden" value="{{ \App\Models\InvoiceStatus::PAID() }}" id="invoice_status" />
      <section class="panel panel-default">
        <table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble">
          <thead>
            <tr>
              <th>No.</th>
              <th>From</th>
              <th>Date</th>
              <th>Invoice Payment Status</th>
              <th>Prescription Status</th>
              <th>Invoice</th>
              <th>Actions</th>
            </tr>
          </thead>
	
	      </table>
      </section>
    <!-- </section>
  </section> -->
</section>
<script>
jQuery(function($) {
    $('#js-provider-list').DataTable({
      "processing": true,
      "serverSide": true,
      "ordering": true,
      "lengthMenu": [[30, 60, 120], [30, 60, 120]],
      "info": true,
      scrollY: 650,
      scrollCollapse: true,
      scroller:       true,
      "autoWidth": false,
      "responsive": true,
      "ajax": {
        "url": "{{ route('admin.load-shipped-prescription-list') }}",
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
          name: 'prescription.id',
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
          name: 'invoice.status_id',
          "render": function(data, type, row){
            var html = '';
            if(row.in_status == $('#invoice_status').val()) html = "<i class='fa fa-check'  style='color:#01DF01'></i>";
            else html = "<i class='fa fa-times'  style='color:#DF0101'></i>";
            
            return html;
          },		
        },
        {
          name: 'prescription.status',
          "render": function(data, type, row){
            switch (row.status) {
              case 3:
                return "Rejected";
              case 2:
                return "Verified";
              default:              
              case 1:
                return "Unverified";
            }
          },		
        },
        { 
          name: 'invoice',
          "render": function(data, type, row) {
            if (row.invoice != "") {
              var invoice_route = "{{ route('admin.load-invoice', ['id' => 'PRES_ID' ]) }}";
              return "<a class='text-info' href='" + invoice_route.replace('PRES_ID', row.pres_id) + "'>" + row.invoice + "</a>";
            }
          },
        },
        { 
          name: 'invoice.invoice',
          "render": function(data, type, row) {
            var html = '',
              edit_route = "{{ route('admin.pres-edit', ['pres_id' => 'PRES_ID', 'status' => '0' ]) }}";

              return "<a class='btn btn-s-md btn-info btn-rounded' href='" + edit_route.replace('PRES_ID', row.pres_id) + "' >Details</a>"
          },
        } 
      ],
      'columnDefs': [
        {
          "targets": 3, // your case first column
          "className": "text-center",
          "text-align": 'center'
        },
        { orderable: false, targets: [0, 3, 4, 6] },
        { targets: [ 6 ], order: [ 6, 1, 2] },
        { targets: [ 2 ], order: [ 2, 1] }
      ],
    });
  });
</script>
@include('admin/footer')
