jQuery( document ).ready(function() {

		var price = jQuery(".occp_price_total").attr("data-total");
		var currency = jQuery(".formate").val();
		var total_items = jQuery(".product_check:checked").length;

		if(jQuery(".layout").val() == "layout1"){
        	jQuery(".product .single_add_to_cart_button").html("Add to cart ("+total_items+")");
        }else{
        	jQuery(".occp_add_cart_button").val("Add to cart ("+total_items+")");
        }
});