@include('admin/header')
<script>
  $(document).ready( function()
  {
    $('#dash').removeClass('active');
    $('#presc').addClass('active');
  });
</script>

<section id="content">
      <div class="row m-b-md">
        <div class="col-lg-9"><h3 class="m-b-none">Verified Prescriptions</h3></div>
      </div>
      <section class="panel panel-default">
        <table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble all-pres">
          <thead>
            <tr>
              <th>No.</th>
              <th>From</th>
              <th>Date</th>
              <th>Prescription Status</th>
              <th>Invoice</th>
              <th>Actions</th>
            </tr>
          </thead>
        </table>
      </section>
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
        "url": "{{ route('admin.load-active-prescription-list') }}",
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
          }		
        },
        { 
          name: 'invoice',
          "render": function(data, type, row) {
            if (row.invoice != "") {
              var invoice_route = "{{ route('admin.load-invoice', ['id' => 'INOV_ID' ]) }}";
              return "<a class='text-info' href='" + invoice_route.replace('INOV_ID', row.id) + "'>" + row.invoice + "</a>";
            } else {
              switch (row.shipping_status) {
                case 1:
                  return "Pending";
                case 2:
                  return "Paid";
                case 3:
                  return "Unpaid";
                case 4:
                  return "Cancelled";
                default:
                  return "Invoice Not Created";
              }
            }

          },
        },
        {
          data: null,
          "render": function(data) {
            var html = '',
              edit = '',
              edit_route = "{{ route('admin.pres-edit', ['pres_id' => 'PRES_ID','status' => '0' ]) }}",
              payment_route = "{{ route('admin.admin-pay-success', ['pres_id' => 'PRES_ID' ]) }}";

            html = "<a class='btn btn-s-md btn-info btn-rounded' href='" + edit_route.replace('PRES_ID', data.pres_id) + "' >Details</a>" +
                    "<a class='btn btn-s-md btn-danger btn-rounded' href='" + payment_route.replace('PRES_ID', data.id) + "' onclick='return confirm('Do you really want to make this order as paid?');'>Pay</a>";
                        
            return html;
          }
        },        
      ]
    });
  });
</script>
@include('admin/footer')
