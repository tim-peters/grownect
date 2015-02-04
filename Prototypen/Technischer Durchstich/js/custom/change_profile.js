/* Forces .css-hooks to return color as hex (instead of rgb)
 * Source: http://stackoverflow.com/questions/6177454/can-i-force-jquery-cssbackgroundcolor-returns-on-hexadecimal-format */
$.cssHooks.backgroundColor = {
    get: function(elem) {
        if (elem.currentStyle)
            var bg = elem.currentStyle["backgroundColor"];
        else if (window.getComputedStyle)
            var bg = document.defaultView.getComputedStyle(elem,
                null).getPropertyValue("background-color");
        if (bg.search("rgb") == -1)
            return bg;
        else {
            bg = bg.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            function hex(x) {
                return ("0" + parseInt(x).toString(16)).slice(-2);
            }
            return "#" + hex(bg[1]) + hex(bg[2]) + hex(bg[3]);
        }
    }
}

function isItUploaded()
{
	console.log("run: isItUploaded()");
	$.ajax({
		type: "POST",
		url: apiurl,
		success: function( msg ) {
			if(msg != 0)
			{
				$(".qr").remove();
				$(".userlarge").css("background-image", "url('"+msg+"'");
				$("textarea[name='description']").after("<input type='hidden' name='img' value='"+msg+"' />");
				$(".editprofile img").hide();
			}
			else
				setTimeout(function() { isItUploaded() },1000);
		}
	});
}

$(document).ready(function() {
	$('.editprofile').click(function() {	
		$('.qr').show();
		setTimeout(function() { isItUploaded(); },5000);
	});
});

