@include('admin/header')
<script>
  $(function()
  {
      $('#dash').removeClass('active');
      $('#presc').addClass('active');
  });
</script>

<section id="content">
  <!-- <section class="vbox"> -->
    <!-- <section class="scrollable padder"> -->
      <div class="row m-b-md">
        <div class="col-lg-9">
          <h3 class="m-b-none">Deleted Prescriptions</h3>
        </div>
      </div>
      <input type="hidden" value="{{ \App\Models\InvoiceStatus::PAID() }}" id="invoice_status" />
      <section class="panel panel-default">
        <!-- <table class="table table-striped m-b-none dataTable"> -->
        <table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble">

          <thead>
            <tr>
              <th>No.</th>
              <th>From</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
	
	      </table>
      </section>
    <!-- </section> -->
  <!-- </section> -->
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
        "url": "{{ route('admin.load-deleted-prescription-list') }}",
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
          data: null,
          render:	function (data) {
            return "Deleted";
          }
        }
      ],
      'columnDefs': [
        { orderable: false, targets: [0, 3] },
        { targets: [ 1 ], order: [ 1, 2] },
        { targets: [ 2 ], order: [ 2, 1] }
      ],
    });
  });
</script>
@include('admin/footer')
