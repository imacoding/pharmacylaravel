var url = $('#siteurl').val();
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    fetch_alternatives();
    $('a.add-to-cart-btn').click(addToCart);
});

function fetch_alternatives()
{
    var hidden_medicine=$('#hidden_medicine').val(),
        hidden_medicine_id=$('#hidden_medicine_id').val(),
        alternative="",
        count=1;
        $.ajax({
            type: "GET",
            url: url + '/medicine/load-sub-medicine/',
            data: {
                n: hidden_medicine,
                id:hidden_medicine_id
            },
            datatype: 'json',
            beforeSend: function (){
                $('#user_loader').show();
                $('div.alert').remove();
            },
            success: function (results) {
                $('#user_loader').hide();
                if(results.status) {
                    var price = results.data.price;
                    html = '<div class="alternatives-wrap">';
                    if (results.data.medicines.length > 0) {
                        $.each(results.data.medicines, function(key, medicine) {
                            var st="",
                                hrefs = url + '/medicine-detail/' + medicine.item_code;
                            if(parseFloat(price) >= parseFloat(medicine.selling_price)) var st="style='color:green'";
                            else if(parseFloat(price) < parseFloat(medicine.selling_price)) var st="style='color:red'";
                            
                            html += '<a href="' + hrefs + '">' +
                                        '<div class="product-wrap-left">' +
                                            '<figure> ' +
                                                '<img src="' + medicine.product_image + '" alt="add-to-cart-image"> ' +
                                            '</figure> ' +
                                            '<div class="product-details"> ' +
                                                '<h6>' + medicine.item_name + '</h6> ';
                                            if (medicine.manufacturer) html +='<p class="manufacturer">Mfr: ' + medicine.manufacturer + '</p> ';
                                            html += '<p class="pr-price">Best price* <span class="prod-price" '+ st + '>' + medicine.mrp + '</span></p> ' +
                                            '</div> ' +
                                        '</div>' +
                                    '</a>';
                        });
                    } else {
                        html += '<div class="product-wrap-left">' + 
                                    '<p>' + results.msg +'</p>' +
                                '</div>';
                    }
                    html += '</div>';
                    $('.alternatives-wrap').replaceWith(html);
                }
            }
        });
}

function addToCart () {
    var _this = $(this),
        parent_div = _this.parents('.single-item-wrap'),
        med_details_div = parent_div.find('.product-details');
    event.preventDefault();
    $.ajax({
        type: "GET",
        url: url + '/medicine/add-cart/1',
        data: {
            id: med_details_div.attr('data-med-id'),
            medicine: med_details_div.find('h6').text(),
            med_quantity: parent_div.find('.prd-qty').val(),
            item_code: med_details_div.attr('data-med-code'),
            selling_price: med_details_div.attr('data-sel-prz'),
            pres_required: med_details_div.attr('data-pres')
        },
        datatype: 'json',
        beforeSend: function () {
            $('#user_loader').show();
            $(this).attr('disabled', true);
        },
        success: function (results) {
            $('#user_loader').hide();
            $(this).removeAttr('disabled');

            if(results.status) {
                if('data' in results && results.data.logged == 0){ $('[data-target="#login-modal"]')[0].click(); return;}

                if(results.msg == "Updated" || results.msg == "Inserted") $('<div class="alert alert-success">Your cart has been successfully updated!</div>').insertBefore('.addto-cart-outer').delay(5000).fadeOut("slow");

                if (results.msg == "Inserted") window.location.replace( url + '/my-cart');
            } else $('<div class="alert alert-danger">Something went wrong. Try again later!</div>').insertBefore('.addto-cart-outer').delay(5000).fadeOut("slow");     

        }
    });
}