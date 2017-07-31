Event.observe(window, 'load', function() {
		var iDiv = document.createElement('div');
		iDiv.id = 'button-callforprice';
		iDiv.className = 'button-callforprice';
        if(document.getElementsByClassName('product-shop')[0]){
            document.getElementsByClassName('product-shop')[0].appendChild(iDiv);    
        }

	});
    
function loadCallForPriceFormFromListProduct(reloadurl,proId)
{
     jQuery.fancybox({
        href: reloadurl,
        type: "ajax",
        ajax: {
            dataType : 'json',
            type: "POST",
            data: {
                proId: proId
            }
        },
        maxWidth  : 400,
        width        : '400px',
        fitToView: true,
        modal: false
    });
}

function submitcallforpriceform(f,submiturl){
        jQuery('#img_loader_callforprice').show();
        new Ajax.Request(submiturl, {
            method: 'post',
            parameters:Form.serialize($('cp_form')),
            onSuccess: function(transport) {
                var json = transport.responseText.evalJSON();
                var displayString = json.message;
                if(json.success) {
                    //jQuery('#messages_product_view').html('<ul class="messages"><li class="success-msg"><ul><li><span>'+displayString+'</span></li></ul></li></ul>');
//                    jQuery.fancybox.close();
                    jQuery('#cp_form').hide();
                    jQuery('#img_loader_callforprice').hide();
                    jQuery('#messageresponse').show();
                    jQuery('#messageresponse').html('<ul class="messages"><li class="success-msg"><ul><li><span>'+displayString+'</span></li></ul></li></ul>');
                    setTimeout(function(){ jQuery.fancybox.close(); }, 4000);
                }
                else
                {
                    jQuery('#cp_form').hide();
                    jQuery('#img_loader_callforprice').hide();
                    jQuery('#messageresponse').show();
                    jQuery('#messageresponse').html('<ul class="messages"><li class="error-msg"><ul><li><span>'+displayString+'</span></li></ul></li></ul>');
                }
            }
        });
}