
channel.bind('events', function(data) {
	switch(data.name) {
		case "setUser":
			window.location.replace("./?change_user="+data.id);
		break;

		case "leaveUser":
			window.location.replace("./?change_user=-1");
		break;

		case "reloadMirror":
			window.location.replace("./");
		break;

		case "blurMirror":
			window.location.replace("./?state=blurred&conflict_id="+data.conflict_id);
		break;
	}
});

$(".usercontainer:not(.small) .user").click(function(){ // trigger
	if(!$(".user.open").not(this).length)
		$(this).parent(".usercontainer").toggleClass("clicked");
	$(".user").not(this).removeClass("open");
	$(this).toggleClass("open");
});
