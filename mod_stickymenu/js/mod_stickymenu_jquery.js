jQuery(document).ready( function($) {
  // grab the initial top offset of the navigation 
	var sticky_navigation_offset_top = jQuery('div.sticky').offset().top;
	
	// our function that decides weather the navigation bar should have "fixed" css position or not.
	var sticky_navigation = function(){
		var scroll_top = jQuery(window).scrollTop(); // our current vertical position from the top
		
		// if we've scrolled more than the navigation, change its position to fixed to stick to top, otherwise change it back to relative
		if (scroll_top > sticky_navigation_offset_top) { 
			jQuery('#topmenu div.sticky').css({ 'position': 'fixed', 'top':10});
			jQuery('#topmenu div.topbar').css({ 'display': 'block' });
		} else {
			jQuery('#topmenu div.sticky').css({ 'position': 'static' });
			jQuery('#topmenu div.topbar').css({ 'display': 'none' });
		}   
	};
	
	// run our function on load
	sticky_navigation();
	
	// and run it again every time you scroll
	jQuery(window).scroll(function() {
		 sticky_navigation();
	});
});
