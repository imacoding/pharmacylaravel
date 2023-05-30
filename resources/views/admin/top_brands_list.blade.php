@include('admin/header')
<style>
    .dropdown-menu{
        /* right: 0; */
        left:0;
    }
    .js-mand {
        color:red;
    }
</style>
<script>
    $(function()
    {
        $('#dash').removeClass('active');
        $('#top_brands').addClass('active');
    });
</script>
<section id="content">
    <!-- <section class="vbox">
      <section class="scrollable padder"> -->
    <div class="m-b-md">
        <h3 class="m-b-none" style="margin-bottom: 10px;">Top Brands</h3>
        <div class="row" style="text-align: right; padding-right: 20px;">
            <a class="btn btn-s-md btn-success btn-rounded" href="add-brand"><i class="fa fa-fw fa-plus"></i> Add Brand</a>
        </div>
    </div>
    <section class="panel panel-default">
        <table id="js-provider-list" class="table table-striped m-b-none table-hover new-med-tble">
            <thead>
            <tr>
                <th>No.</th>
                <th>Title</th>
                <th>Content</th>
                <th>Brand Image</th>
                <th>Is Delete</th>
                <th>Actions</th>
            </tr>
            </thead>
        </table>
    </section>
    <!-- </section>
</section> -->
</section>
<script>

    var url = $('#siteurl').val();

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
                "url": "{{ route('admin.topbrandslist') }}",
                "dataType": "json",
                "type": "GET",
                "data": {
                    _token: "{{csrf_token()}}"
                }
            },
            "language": {
                "searchPlaceholder": "Search brands by name",
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
                    name: 'title',
                    data: 'title'
                },
                {
                    name: 'content',
                    data: 'content',
                },
                { name: 'brand_image',
                  data: 'null',
                    render : function(data,type,full) {
                    return '<img height="100" width="100" src="'+url+'/storage/brand/'+full.brand_image+'">';
                    }
                },
                { name: 'is_delete',
                    data: null,
                    render: function(data){
                        return (data.is_delete) ? 'Yes' : 'No';
                    }
                },
                {
                    data: null,
                    "render": function(data) {
                        var html = '';
                        var edit_route = "{{ route('admin.topbrands-edit', ['id' => 'BRAND_ID' ]) }}";
                        var delete_route = "{{ route('admin.topbrands-delete', ['id' => 'BRAND_ID' ]) }}";
                        edit_route = 	edit_route.replace('BRAND_ID', data.id);
                        delete_route = 	delete_route.replace('BRAND_ID', data.id);
                        var html = '<div class="btn-group">' +
                            '<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>' +
                            '<ul class="dropdown-menu" role="menu">' +
                            '<li><a href="' + edit_route + '" >Edit</a></li>' +
                            '<li><a href="' + delete_route + '" onclick="return confirm("Do you really want to make this "' + data.name + '" top brand?");">Delete</a></li>' +
                            '</ul>' +
                            '</div>';

                        return html;
                    }
                },
            ],
            columnDefs: [
                { orderable: false, targets: [0, 3, 5] },
                {
                    targets: [ 1 ],
                    orderData: [ 1, 2]
                }, {
                    targets: [ 3 ],
                    orderData: [ 3, 1 ]
                }
            ]
        });
    });

</script>
@include('admin/footer')
