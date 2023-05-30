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
                    <h3 class="m-b-none">All Prescriptions</h3>
                </div>
            </div>
            <!-- Status -->
            <input type="hidden" value="{{ \App\Models\ShippingStatus::SHIPPED() }}" id="shipping_status" />
            <input type="hidden" value="{{ \App\Models\InvoiceStatus::PAID() }}" id="invoice_status" />
            <input type="hidden" value="{{ \App\Models\PrescriptionStatus::VERIFIED() }}" id="prescription_status" />
            <section class="panel panel-default all-pres-sec">
                <table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble all-pres">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>From</th>
                            <th>Date</th>
                            <th>Prescription Status</th>
                            <th>Payment Status</th>
                            <th>Shipping Status</th>
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
    function confirm_deletion(element) {
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
        "info": true,
        scrollY: 650,
        scrollCollapse: true,
        scroller:       true,
        "autoWidth": false,
        "responsive": true,
        "ajax": {
          "url": "{{ route('admin.load-all-prescription-list') }}",
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
            name: 'invoice.payment_status',
            "render": function(data, type, row){
              switch (row.payment_status) {
                case 3:
                  return "Failed";
                case 2:
                  return "Paid";                
                case 1:
                  default:
                    return "Pending";
              }
            }		
          },
          {
            name: 'invoice.shipping_status',
            "render": function(data, type, row){
              switch (row.shipping_status) {
                  case 4:
                    return "Received";
                  case 3:
                    return "Returned";
                  case 2:
                    return "Shipped";
                  case 1:
                    default:
                      return "Not Shipped";
              }
            }		
          },
          { 
              name: 'invoice',
              "render": function(data, type, row) {
                  if (row.invoice) {
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
                prfUnverified = <?php echo \App\Models\PrescriptionStatus::UNVERIFIED() ?>,
                prfVerified = <?php echo \App\Models\PrescriptionStatus::VERIFIED() ?>;
              var edit_route = "{{ route('admin.pres-edit', ['pres_id' => 'PRES_ID','status' => 'EDIT' ]) }}";
              var delete_route = "{{ route('admin.pres-delete', ['pres_id' => 'PRES_ID' ], 'all') }}";
              var ship_route = "{{ route('admin.ship-order', ['pres_id' => 'PRES_ID' ]) }}";
              if(data.status === prfUnverified) edit=1;
              else edit=0;

              edit_route = 	edit_route.replace('PRES_ID', data.pres_id).replace('EDIT',edit);
              delete_route = 	delete_route.replace('PRES_ID', data.pres_id);
              ship_route = 	ship_route.replace('PRES_ID', data.pres_id);
              var html = "<a class='btn btn-s-md btn-info btn-rounded' href='" + edit_route + "' >Details</a>";
              if(data.status === prfUnverified)
                html += "<a class='btn btn-s-md btn-danger btn-rounded' data-href='" + delete_route + "' onclick='confirm_deletion(this);'>Delete</a>";
              if(data.status ===  prfVerified) html += "<a class='btn btn-s-md btn-success btn-rounded' href='" + ship_route + "'  onclick='return confirm('Do you really want to make this order as shipped?');'>Ship Order</a>";

              return html;
            }
          },        
        ],
        "columnDefs": [
          {
            "width": "10%",
            "targets": [2, 3, 4, 5]              
          }
        ]
      });
    });
</script>
@include('admin/footer')
