(function( $ ) {
	'use strict';
    $(document).ready(function () {
		$('.wsv_quantity').on("change", function () {
			var product_qty = $(this).val();
			var product_id = $(this).prop('placeholder');
			var add_cart = $('.wsv-add-to-cart-'+product_id);
			add_cart.attr('data-quantity', product_qty);
		});

		$('.wsv-add-to-cart').click( function ( e ) {
			e.preventDefault();
			$(this).addClass('loading');
			var product_id = $(this).attr('data-product_id');
			var product_qty = $(this).attr('data-quantity');
			$.ajax({
				url: ajaxurl,
				type: "POST",
				data: {
				  'action': 'wsv_add_product_to_cart',
				  'product_id': product_id,
				  'product_qty': product_qty,
				},
				success: function()
				{
					$('.wsv-add-to-cart').removeClass('loading');
					jQuery(document.body).trigger('wc_fragment_refresh');
				}
			});
		});

		$('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            language: { search: '', searchPlaceholder: "Search" },
        });
	})

})( jQuery );
