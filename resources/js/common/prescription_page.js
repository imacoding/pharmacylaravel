var url = $('#siteurl').val();
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    getData();
});

$(document).on('click', '.remove-pres', function(){
    $(this).parents('.upload-item-outer').remove();
    if ($(this).parents('.upload-file-sec').find('.upload-item-outer').length === 0) $('.upload-file-sec h5').addClass('d-none');
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
        $(html).appendTo('.upload-file-sec');
      };
    }
}

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
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                url: url + "/upload-prescription/1",
                data: form_data,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(this).attr('disabled', true);
                    $('.loader-overlay.typ2').show();
                    $('span.error').remove();
                    $('span.success').remove();
                },
                success: function (result) {
                    $('.loader-overlay.typ2').hide();
                    $(this).removeAttr('disabled');
                    if(result.status) {       
                        $("#triggerMe").trigger("click");
                        ($('.upload-item-outer').length === 1) ? $('div.upload-file-sec h5').addClass('d-none') : '';
                        parent_div.remove();
                        getData();
                    } else $('<span class="common-error error"> ' + result.msg + '</span>').insertAfter(parent_div);
                }
            });
        } else return false;

});
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

function getData() {
    $('.loader-overlay.typ2').show();    
    $.ajax({
        type: "GET",
        url: url + '/medicine/getMyPrescriptions',
        data: 'is_category=' + $('select.form-select').val(),
        datatype: 'json',
        beforeSend: function() {
            $(".user-name.error").remove();
        },
        success: function (results) {
            $('.loader-overlay.typ2').hide();

            var content_div = $('#prescription-clone div.card').clone(),
            html = '',
            i = 0;
            html = '<div class="prescription-items">';
            if (results.data.prescription.length > 0) {
                html += '<div class="accordion" id="accordion-prescription"> ';
                var i=0; 
                $.each(results.data.prescription, function(key, prescriptions) {
                    var med_name = ((prescriptions.invoice.length > 0) && prescriptions.cart.length > 0) ? prescriptions.cart[0].item_name : '',
                        img = "window.open('" + prescriptions.img + "','_self')",
                        hide_download = prescriptions.img ? '' : 'd-none',
                        hide_payment_url = prescriptions.invoice_id ? '' : 'd-none',
                        payment_function = '';
                    if(prescriptions.payment_mode == 'payu') payment_function = "purchase(this)";
                    if(prescriptions.payment_mode == 'paypal') payment_function = "purchase_paypal(this)";
                    html += '<div class="card">' +
                        '<div class="card-head" id="heading' + i +'">' +
                            '<ul data-toggle="collapse" data-target="#collapse' + i +'" aria-expanded="false" aria-controls="collapse' + i +'" class="collapsed">' +
                                '<li>' +
                                    '<p>Date</p>' +
                                    prescriptions.created_on +
                                '</li>' + 
                                '<li>' +
                                    '<p>Status</p>' +
                                    prescriptions.pres_status +

                                '</li>' +
                                '<li>' +
                                    '<div class="item-details-outer">' +
                                        '<div class="item-details-inner">' +
                                            '<figure>' +
                                                '<img src="' + prescriptions.path + '" alt="tablet-bottle">' + 

                                            '</figure>' +
                                            '<div>' + 
                                                '<p>' + med_name + '</p>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="download-pdf-btn ' + hide_download + '">' +
                                            '<a onclick="' + img + '" class="">' +
                                                '<svg xmlns="http://www.w3.org/2000/svg" width="42" height="36" viewBox="0 0 42 36">' +
                                                    '<defs>' +
                                                        '<style>' +
                                                            '.a9, .b9, .d9 { fill: none;  } .a9 { stroke: #e3e3e3; } .b9 { stroke: #000; stroke-linecap: round; stroke-linejoin: round; }' +
                                                                '.c9 { stroke: none; }' +
                                                        '</style>' +
                                                    '</defs>' +
                                                    '<g transform="translate(-1550 -356)"> <g class="a9" transform="translate(1550 356)"> <rect class="c9" width="42" height="36" rx="8" /> <rect class="d9" x="0.5" y="0.5" width="41" height="35" rx="7.5" /> </g><g transform="translate(1558.5 361.5)"><path class="b9" d="M20.8,22.5v3.623a1.812,1.812,0,0,1-1.812,1.812H6.312A1.812,1.812,0,0,1,4.5,26.123V22.5" transform="translate(0 -7.13)" /> <path class="b9" d="M10.5,15l4.529,4.529L19.558,15" transform="translate(-2.377 -4.159)" /> <path class="b9" d="M18,15.37V4.5" transform="translate(-5.348)" /></g></g>' +
                                                '</svg>' +
                                            '</a>' +
                                        '</div>' +
                                    '</div>' +
                                '</li>' +
                            '</ul>' +
                        '</div>' +
                        '<div id="collapse' + i +'" class="collapse" aria-labelledby="heading' + i +'" data-parent="#accordion-prescription">';
                        if(prescriptions.invoice.length > 0) {
                            html += '<div class="card-body">' +
                                        '<div class="prescription-details-table">' +
                                            '<div class="prescription-tbl-head">' +
                                                '<p>Medicine</p>' +
                                                '<p>Quantity</p>' +
                                                '<p>Unit Price</p>' +                                                
                                                '<p>Sub Total</p>' +
                                                '<p>Unit Disc</p>' +
                                                '<p>Discount</p>' +
                                                '<p>Total Price</p>' +
                                            '</div>' +
                                            '<div class="table-contents-wrap">';
                                                $.each(prescriptions.cart, function(keys, cartValue) {
                                                    html += '<div class="table-contents">' +
                                                        '<div class="medicine-name">' +
                                                            '<p>' + cartValue.item_name + '</p>' +
                                                        '</div>' +
                                                        '<div class="medicine-qty">' +
                                                            '<p>' + cartValue.quantity + '</p>' +
                                                        '</div>' +
                                                        '<div class="unitprice">' +
                                                            '<p>' + cartValue.unit_price + '</p>' +
                                                        '</div>' +                            
                                                        '<div class="medicine-s-total">' +
                                                            '<p>' + cartValue.sub_total + '</p>' +
                                                        '</div>' +
                                                        '<div class="med-unit-discnt">' +
                                                            '<p>' + cartValue.discount_percent + '</p>' +
                                                        '</div>' +
                                                        '<div class="med-discount">' +
                                                            '<p>' + cartValue.discount + '</p>' +
                                                        '</div>' +
                                                        '<div class="med-total">' +
                                                            '<p>' + cartValue.total_price + '</p>' +
                                                        '</div>' +
                                                    '</div>';
                                                });
                                        html += '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="medicine-total-outer">' +
                                        '<p>' +
                                            '<span>Sub Total :</span>' +
                                            '<span>' + prescriptions.sub_total + '</span>' +
                                        '</p>' +
                                        '<p>' +
                                            '<span>Shipping Cost :</span>' +
                                            '<span>' + prescriptions.shipping + '</span>' +
                                        '</p>' +
                                        '<p>' +
                                            '<span>Discount :</span>' +
                                            '<span>' + prescriptions.discount + '</span>' +
                                        '</p>' +
                                        '<p>' +
                                            '<span>Net Payable :</span>' +
                                            '<span>' + prescriptions.net_payable + '</span>' +
                                        '</p>' +
                                    '</div>' + 
                                    '<div class="prescription-buynow-btn ' + hide_payment_url + '">' +
                                        '<a href="javascript:void(0)" onclick="' + payment_function + '" data-invoice="'+ prescriptions.invoice_id +'" class="">Buy Now</a>' +
                                    '</div>';
                        } else {
                            html += '<div class="empty-section pb40">' +
                                '<p>Invoice not generated!</p>' +
                            '</div>';
                        }
                        html += '</div>' +                                                          
                    '</div>';
                    i++;
                });
                html += '</div>';
            // html += '</div>';
            } else {
                html += '<div class="empty-section pb40">' +
                    '<p>Prescriptions Not Available!</p>' +
                '</div>';
            }
            html += '</div>';
            // $(html).appendTo($('.prescription-table .prescription-items'));
            $('.prescription-table .prescription-items').replaceWith(html);
            $(".download-pdf-btn").click(function(event) {event.stopImmediatePropagation() ;
                event.preventDefault();
                
            });
        }
    });
}

function purchase(_this) {
    var invoice = $(_this).attr('data-invoice');
    $(this).attr('disabled', true);
    window.location = url + "/medicine/make-payment/" + invoice;

}
function purchase_paypal(_this) {
    var invoice = $(_this).attr('data-invoice');
    $(this).attr('disabled', true);
    window.location = url + "/medicine/make-paypal-payment/" + invoice;

}