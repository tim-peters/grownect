
channel.bind('events', function(data) {
	switch(data.name) {
		case "setUser":
			window.location.replace("./?change_user="+data.id);
		break;

		case "leaveUser":
			window.location.replace("./?change_user=-1");
		break;
	}
});