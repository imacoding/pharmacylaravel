@include('admin/header')
<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.js"></script>
<section id="content">
    <section class="vbox">
        <section class="scrollable padder">
            <div class="m-b-md">
                <h3 class="m-b-none">Add/Edit Topbrands</h3>
            </div>

            <section class="panel panel-default">
                @if (\Session::has('success'))
                    <p class="alert alert-danger">{!! \Session::get('success') !!}</p>
                @endif
                <header class="panel-heading font-bold">Update Top Brands Database</header>
                <strong id="div-notify" style="color: red"></strong>
                <form method="POST" class="panel-body" action="{{ url('/admin/new-brand') }}" enctype="multipart/form-data">

                     {{ csrf_field() }}

                    <div class="form-group">
                        <label for="email">Title:</label>
                        <input class="form-control"  name="title" type="text" value=<?php echo(isset($details['title']) ?"'".$details['title']."'":'""');?> required>
                    </div>
                    <input  name="id" type="hidden" value=<?php echo(isset($details['id']) ?"'".$details['id']."'":'""');?> >
                    <div class="form-group">
                        <label for="email">Content:</label>
                        <input class="form-control"   name="content" type="text" value=<?php echo(isset($details['content']) ?"'".$details['content']."'":'""');?> required>
                    </div>
                    <div class="form-group">
                        <label for="email">Is Delete:</label>
                        <select class="form-control" name="is_delete">
                            <option value="0"  <?= (isset($details['is_delete']) && $details['is_delete'] == 0) ? 'selected' : '' ?>>No</option>
                            <option value="1"  <?= (isset($details['is_delete']) && $details['is_delete'] == 1) ? 'selected' : '' ?>>Yes</option>
                        </select>
                    </div>
                    @php
                        $img = '' ;
                    if (isset($details['brand_image']) && !empty($details['brand_image']) && Storage::disk('BRAND_THUMB')->exists($details['brand_image']))
                        $img = Storage::disk('BRAND_THUMB')->url($details['brand_image']);
                    else if (isset($details['brand_image']) && !empty($details['brand_image']) && Storage::disk('BRAND_PIC')->exists($details['brand_image']))
                        $img = Storage::disk('BRAND_PIC')->url($details['brand_image']);
                    @endphp
                    <div class="form-group" id="img-wrap">
                        <label for="email">Image(Size 656 px X 656 px):</label>
                        <div class="img-wrap" >
                            @if($img)
                                <a href="{{ Storage::disk('BRAND_PIC')->url($details['brand_image']) }}" target="_blank">
                                    <img src="{{ $img }}" />
                                </a>
                            @endif
                            <input name="file" type="file" accept="image/png, image/jpg, image/jpeg" onchange="readURL(this);">
                        </div>

                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-lg" name="submit" value="Add" type="submit">Add</button>
                    </div>
                </form>
            </section>
        </section>
    </section>

    @include('admin/footer')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script>
        $(document).ready(function(e){

            $( "#expiry" ).datepicker({ minDate: 0});
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var extension = input.files[0].type.split('/').pop().toLowerCase();
                if ($.inArray(extension, ['jpg', 'jpeg', 'png']) == -1) {
                    $('body').removeClass('keyboard-open');
                    $('<span class="img-error error">Upload only jpg, jpeg, png files.</span>').insertAfter('#img-wrap');
                    return false;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.img-wrap a').remove();
                    $('<img src="' + e.target.result + '"/>').insertBefore('.img-wrap input[type="file"]');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>