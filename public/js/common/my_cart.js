var url = $('#siteurl').val();
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    cartData();
    $('input').on('focus', function(){
        $('span.common-error').remove();
        $('input[type="text"]').removeClass('error');
    });
});

document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
    const dropZoneElement = inputElement.closest(".drop-zone");
  
    dropZoneElement.addEventListener("click", (e) => {
      inputElement.click();
    });
  
    inputElement.addEventListener("change", (e) => {
      if (inputElement.files.length) {
        for (var i = 0; i < inputElement.files.length; i++) {
            updateThumbnail(inputElement.files[i]);
        }
      }
    });
  
    dropZoneElement.addEventListener("dragover", (e) => {
      e.preventDefault();
      dropZoneElement.classList.add("drop-zone--over");
    });
  
    ["dragleave", "dragend"].forEach((type) => {
      dropZoneElement.addEventListener(type, (e) => {
        dropZoneElement.classList.remove("drop-zone--over");
      });
    });
  
    dropZoneElement.addEventListener("drop", (e) => {
      e.preventDefault();
      if (e.dataTransfer.files.length) {
        inputElement.files = e.dataTransfer.files;
        console.log('drop');

        for (var i = 0; i < e.dataTransfer.files.length; i++) {
            updateThumbnail(e.dataTransfer.files[i]);
          }
      }     

      dropZoneElement.classList.remove("drop-zone--over");
    });
});
  
  /**
   * Updates the thumbnail on a drop zone element.
   *
   * @param {HTMLElement} dropZoneElement
   * @param {File} file
   */
function updateThumbnail(file) {
    // Show thumbnail for image files
    if (file.type.startsWith("image/")) {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = () => {
        var html = '<div class="upload-item-outer">'+
                        '<form method="" class="panel-body" action="" >' +
                            '<div class="upload-item">' +
                                '<div class="upload-item-lft">' +
                                    '<figure>' +
                                        '<img src="' + reader.result + '" alt="tablet-bottle">' +
                                        '<input type="file" name="files" style="display: none;" value="' + reader.result + '"/>'+
                                    '</figure>' +
                                    '<div>' +
                                        '<p>' + file.name + '</p>' +
                                        '<p>' + Math.round((file.size / 1024)) + 'KB </p>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="placeorder-btn">' +
                                    '<button type="submit" class="btn btn-primary w-100">Place order</button>' +
                                '</div>' +
                            '</div>'+
                        '</form>' +
                        '<div class="remove-bttn">'+
                            '<a href="javascript:void(0)" class="remove-pres">'+
                                '<img src="' + url + '/public/assets/images/close.svg">'+
                            '</a>'+
                        '</div>'+
                    '</div>';
                    $('.upload-file-sec h5').removeClass('d-none');
                    $('#pres-req').addClass('d-none');
        $(html).appendTo('.upload-file-sec');
      };
    }
}
$(document).on('click', '.remove-pres', function(){
    $(this).parents('.upload-item-outer').remove();
    if ($(this).parents('.upload-file-sec').find('.upload-item-outer').length === 0) {
        $('.upload-file-sec h5').addClass('d-none');
        if ($('#pres-req').attr('pres-re') == 1) $('#pres-req').removeClass('d-none');
    }
});
$(document).on('click', '.upload-item-outer button[type="submit"]', function(e){
    e.preventDefault();
    $(this).attr('disabled', true);
    var parent = $(this).parents('.upload-item'),
        parent_div = parent.parents('.upload-item-outer'),
        form_data = new FormData(),
        img = parent.find('figure input[type="file"]').attr('value');
        if(img) {
            img = b64toBlob(img);
            form_data.append('_token', $('meta[name="csrf-token"]').attr('content'));
            form_data.append('files', img)
            placeOrder(parent_div, form_data);
        } else return false;

});

function placeOrder(parent_div = null, form_data = null) {
    var is_pres = false;
    if(!form_data) {
        var _this = $(this),
            parent_div = _this.parent();
        is_pres = true;
    }
    $.ajax({
        type: "POST",
        dataType: 'JSON',
        url: url + "/upload-prescription/1",
        data: form_data,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#user_loader').show();
            $(this).attr('disabled', 'disabled');
            $('button[type="submit"]').attr('disabled', true);
            $('span.error, span.success').remove();
            $('#pres-req').addClass('d-none');
        },
        success: function (result) {
            $('#user_loader').hide();
            $(this).attr('disabled', false);
            $('button[type="submit"]').removeAttr('disabled');

            if(result.status) {       
                $("#triggerMe").trigger("click");
                ($('.upload-item-outer').length === 1) ? $('div.upload-file-sec h5').addClass('d-none') : '';
                parent_div.remove();                        
            } else {
                
                if(is_pres) cartData(result.msg);
                else $('<span class="common-error error"> ' + result.msg + '</span>').insertAfter(parent_div);
            }
        }
    });
}
$(document).on('click', '#order-placed-popup button', function(){
    cartData();
});

function cartData(error_msg = null) {
    $.ajax({
        type: "GET",
        url: url + '/medicine/get-my-cart/1',
        datatype: 'json',       
        beforeSend: function() {
            $('#user_loader').show();
            $(this).attr('disabled', true);
            $('span.error, span.success').remove();
            $('.cart-items, .empty-section, .subtotal-section').remove();
        },
        success: function (results) {
            $('#user_loader').hide();
            var img = '',
                pres_required = false,
                html = '';
            if (results.data.current_orders.length > 0) {
                $.each(results.data.current_orders, function(key, orders) {
                    img = orders.product_image;
                    if (orders.is_pres_required !== 0) pres_required = true;
                    html += '<div class="cart-items d-flex" data-item=' + orders.session_id + '>' +
                                '<div class="cart-item-info">' +
                                    '<span>Item Info</span>' +
                                        '<a href="javascript:void(0)" class="item-remove">' +
                                            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"> <defs> <style>  .a8 { fill: #909090; } .b8 { fill: none; stroke: #fff; stroke-linecap: round; stroke-width: 2px; } </style>  </defs> <g transform="translate(0 -0.346)"> <circle class="a8" cx="10" cy="10" r="10" transform="translate(0 0.346)" /> <g transform="translate(6.233 6.233)"> <path class="b8" d="M1772.372,2663.943l7.533,7.533" transform="translate(-1772.372 -2663.943)" /> <path class="b8" d="M1779.9,2663.943l-7.533,7.533" transform="translate(-1772.371 -2663.943)" /> </g> </g> </svg>' +
                                        '</a>' +
                                        '<img src=" ' + img + '" alt="tablet-bottle">' +
                                        '<p>paracetamol</p>' +
                                '</div>' +
                                '<div class="cartitems2-outer">' +
                                    '<ul class="cartitems2">' +
                                        '<li class="cart-item-qty">' +
                                            '<p>Qty</p><input type="text" value="' + orders.med_count + '">' +
                                        '</li>' +
                                        '<li class="cart-ppu">' +
                                            '<p>Price / unit</p>' + orders.unit_price + 
                                        '</li>' +
                                        '<li class="cart-dpu">' +
                                            '<p>Dis/ unit</p>' + orders.discount +
                                        '</li>' +
                                        '<li class="cart-sub-total">' +
                                            '<p>Total</p>' + orders.total +
                                        '</li>' +
                                    '</ul>' +
                                '</div>' +
                            '</div>';
                });
                subtotal_html = '<div class="subtotal-section">' +
                                '<h4>Sub Total : <span class="total-amnt"> ' + results.data.sub_total + '</span></h4>' +
                                '<span>(this is an approximate total, price may change)</span>' +
                                '<p class="info">If you are done with adding medicines to cart, please browse and upload the prescription from the link below. Alternatively, you may even upload a prescription without adding any medicine to cart. We will identify the medicines and process the order further.</p>' +
                            '</div>';
                $(html).insertAfter('ul.item-info-bar');
                $(subtotal_html).insertAfter('.cart-items:last');

                if (pres_required) $('#pres-req').removeClass('d-none').attr('pres-re', 1);
                else {
                    $('#pres-req').addClass('d-none').attr('pres-re', 0);
                    $('<div class="placeorder-btn"><button type="submit" class="btn btn-primary w-100">Place order</button></div>').insertBefore('.subtotal-section .info');
                }
                $('.subtotal-section button[type="submit"]').click(placeOrder);
                $('.cart-item-info a').click(removeFromCart);
                
            } else {
                html = '<div class="empty-section">' +
                            '<p>Cart is empty</p>' +
                        '</div>';
                $(html).insertAfter('ul.item-info-bar');
            }

            if(error_msg) {
                $('#pres-req').removeClass('d-none');
                $('<span class="common-error error"> ' + error_msg + '</span>').insertAfter('div.placeorder-btn');
            }
        }
    });
}

function removeFromCart () {
    $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: url + "/medicine/remove-from-cart",
        data: {
            session_id: $(this).parents('.cart-items').attr('data-item') 
        },
        beforeSend: function() {
            $('#user_loader').show();
            $('span.error').remove();
            $('span.success').remove();
            $(this).attr('disabled', true);
        },
        success: function (results) {
            $('#user_loader').hide();
            $(this).removeAttr('disabled');

            if(results.status) {
                cartData();
            } else $('<span class="common-error error"> ' + results.msg + '</span>').insertBefore('.subtotal-section');
        }
    });
}
/* Data image to Blob/File  */
function b64toBlob(dataImage, sliceSize) {
	var block = dataImage.split(";");
	// Get the content type of the image
	var contentType = block[0].split(":")[1]; // In this case "image/gif"
	// get the real base64 content of the file
	var b64Data = block[1].split(",")[1]; // In this case "R0lGODlhPQBEAPeoAJosM...."
	contentType = contentType || '';
	sliceSize = sliceSize || 512;
	var byteCharacters = atob(b64Data);
	var byteArrays = [];
	for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
		var slice = byteCharacters.slice(offset, offset + sliceSize);
		var byteNumbers = new Array(slice.length);
		for (var i = 0; i < slice.length; i++) {
			byteNumbers[i] = slice.charCodeAt(i);
		}
		var byteArray = new Uint8Array(byteNumbers);
		byteArrays.push(byteArray);
	}
	var blob = new Blob(byteArrays, {
		type: contentType
	});
	return blob;
}
$(document).on('change', '.cart-item-qty input[type="text"]', function() {
    $(this).removeClass('error').val(parseInt($(this).val()));
    $('span.common-error').remove();

    if($(this).val() > 10000 || $(this).val() < 1) {
        $(this).addClass('error');
        $('<span class="common-error error">Quantity should between 1 - 10000</span>').insertAfter($(this).parents('div.cart-items'));
    } else {
        $.ajax({
            type: 'GET',
            dataType: 'JSON',
            url: url + "/medicine/add-cart/1",
            data: {
                session_id: $(this).parents('.cart-items').attr('data-item'),
                med_quantity: parseInt($(this).val())
            },
            beforeSend: function() {
                $('span.error, span.success').remove();
            },
            success: function (results) {
                if(results.status) {
                    cartData();
                } else $('<span class="common-error error"> ' + results.msg + '</span>').insertBefore('.subtotal-section');
            }
        });
    }
});