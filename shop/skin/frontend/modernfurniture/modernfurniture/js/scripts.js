$j = jQuery.noConflict();
$j(document).ready(function(){

	$j("a[data-gal^='prettyPhoto']").prettyPhoto({
		animationSpeed: 'normal',
		padding: 40,
		opacity: 0.35,
		showTitle: true,
		allowresize: true,
		counter_separator_label: '/',          
		theme: 'facebook' 
	});
	
	$j('.col-main li.item').hover(function(){
		$j(this).toggleClass('over');
	},function(){
		$j(this).toggleClass('over');
	});
	
	if($j.browser.safari) { $j( function() { $j('body').addClass('safari-fix'); } ); };
	if(navigator.userAgent.indexOf('IE 9')!=-1){
		$j('body').addClass('ie-9');
	};
	
	var Site = Site || {}
	Site.HolePuncher = Class.create({
		
		url: '/shop/ms/ajax/holepunch',
		
		initialize: function() {
			var params = {};
			
			if ($j('.block-cart').length) {
				params.cart = true;
			}
			
			if ($j('.block-compare').length) {
				params.compare = true;
			}
			
			$j.get(this.url, params, this.fillHoles.bind(this));
		},
		
		fillHoles: function (resp) {
			resp = $j.parseJSON(resp);
			
			if (resp.cart_link) {
				$j('.header .links li a.top-link-cart').html($j(resp.cart_link).find('a').html());
				$j('.header .links li .count').fadeIn();
			}
			
			if (resp.cart) {
				$j('.block-cart').html($j(resp.cart).html());
				$j('.block-cart').slideDown();
			}
			
			if (resp.compare) {
				$j('.block-compare').html($j(resp.compare).html());
				$j('.block-compare').slideDown();
			}
		}
	});
	//var puncher = new Site.HolePuncher();
});
