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
                    <h3 class="m-b-none">Paid Prescriptions</h3>
                </div>
            </div>
            <input type="hidden" value="{{ \App\Models\ShippingStatus::SHIPPED() }}" id="shipping_status" />
            <section class="panel panel-default">
              <table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>From</th>
                            <th>Date</th>
                            <th>Status/Action</th>
                            <th>Invoice</th>
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
        "url": "{{ route('admin.load-paid-prescription-list') }}",
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
          name: 'prescription.pres_id',
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
            var html = 'Shipped';
            if(row.shipping_status != $('#shipping_status').val()) {
              var ship_route = "{{ route('admin.ship-order', ['pres_id' => 'PRES_ID']) }}";
              html = "<a class='btn btn-s-md btn-info btn-rounded' href='" + ship_route.replace('PRES_ID', row.pres_id) + "'  onclick='return confirm('Do you really want to make this order as shipped?');'>Ship Order</a>";
            }
            
            return html;
          },		
        },
        { 
          name: 'invoice',
          "render": function(data, type, row) {
            if (row.invoice != "") {
              var invoice_route = "{{ route('admin.load-invoice', ['id' => 'INOV_ID' ]) }}";
              return "<a class='text-info' href='" + invoice_route.replace('INOV_ID', row.pres_id) + "'>" + row.invoice + "</a>";
            }
          },
        } 
      ]
    });
  });
</script>
@include('admin/footer')
