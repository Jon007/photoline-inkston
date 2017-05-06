jQuery(document).ready(function($) {

	// Toggle Comment Form
	$(".comment-reply-title").on("click", function(){
		if (jQuery('body').hasClass('logged-in')){
			if ($(".comment-form").is(":hidden")) {
				$(this).toggleClass('active');
				$(".comment-form").slideDown();
				$(".comment-form #comment").focus();
				$('html, body').animate({ scrollTop: $(document).height() }, 1200);
			}
			else{
				$(".comment-form").slideUp("slow");      
				$(this).toggleClass('active');
			}
		} else {
			//like if you click on a link (it will be saved in the session history, 
			//so the back button will work as expected)
			window.location.href = window.loginurl + "?redirect_to=" + encodeURI(window.location.href + "#comment");
		}
	});

	// Toggle Search Form on Footer
	$(".search-footer").on("click", function(){

		if ($("#search-footer-bar").is(":hidden")) {
			$(this).toggleClass('active');
			$("#search-footer-bar").slideDown("slow");
			$("#search-footer-bar .search-field").focus();
		}
    else{
      if ($("#search-field").val()==''){
			$("#search-footer-bar").slideUp();
      }
      else{
        $("#search-form").submit();
      }
    }
	});

	/* For Scroll to top button */
	jQuery("#scroll-up").hide();

	$(window).scroll( function() {
		if ($(this).scrollTop() > 1000) {
			$('#scroll-up').fadeIn();
		} else {
			$('#scroll-up').fadeOut();
		}
	});

	$('a#scroll-up').click( function() {
		 $('body,html').animate({
				scrollTop: 0
		 }, 800);
		 return false;
	});
	
});
	