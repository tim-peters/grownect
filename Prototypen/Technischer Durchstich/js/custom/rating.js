$(document).ready(function() {
	$(".rating span").click(function() {
		var amount = 4-$(".rating span").index(this);
		$("input[name='rating']").val(amount*25);
		$(".rating .active").removeClass("active");
		$(".rating span").each(function() {
			var dc;
			if(dc = $(this).attr("data-class"))
				$(this).removeAttr("data-class");
		});
		$(this).addClass("active");
	});
	$(".rating").hover(function() {
		$("span.active", this).attr("data-class", "active").removeClass("active");
	},
	function() {
		$("span", this).each(function() {
			var dc;
			if(dc = $(this).attr("data-class"))
				$(this).attr("class", dc);
		});
	});
});