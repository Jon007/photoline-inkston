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
			//#comment not encoded by encodeURI so replace with %23
			//window.location.href = window.loginurl + "?redirect=" + encodeURI(window.location.href) + "%23comment";
			window.location.href = window.loginurl + "%23comment";
		}
	});

	function expandCommentForm(){
		if (jQuery('body').hasClass('logged-in')){
			if ($(".comment-form").is(":hidden")) {
				$(this).toggleClass('active');
				$(".comment-form").slideDown();
				$(".comment-form #comment").focus();
				$('html, body').animate({ scrollTop: $(document).height() }, 1200);
			}
		}
	}
	thishref=window.location.href;
//if (window.location.href.substring(window.location.href.length - 8, window.location.href.length)  )
	if (thishref.substring(thishref.length - 8, thishref.length) == '#comment' ){
		expandCommentForm();
	}
	
	//Handle search Form on Header
	$("#topsearchanchor").on("click", function(){
			if ($("#topsearch-input").is(":hidden")) {
				$("#topsearch-input").slideDown("slow");
				$("#topsearch-input").focus();
		} else{
				if ($("#topsearch-input").val()==''){
				if ($(window).width()<1000){
					$("#topsearch-input").slideUp();
			}
		}else{
				$("#topsearch").submit();
			}
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
	