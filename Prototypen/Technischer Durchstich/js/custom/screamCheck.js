channel.bind('events', function(data) {
	console.log("event detected: "+data);
	console.log("id: "+data.id+", name: "+data.name);
	if(data.id == techID)
	{
		switch(data.name) {
			case "startScream":
				$(".message").text("Scream as loud as you can into your bracelet now!")
							.after("<div id=\"loudnessIndikator\"></div>");
				console.log("recognized: startScream");
			break;
			case "endScream":
				$(".message").html("Congratulations! Screaming out your anger can help to calm down. <a href=\""+redirectURI+"\">Continue</a>");
				console.log("recognized: endScream");
			break;
			case "skipScream":
				$(".message").html("Congratulations! It seems like you are calm enough to solve this conflict. <a href=\""+redirectURI+"\">Continue</a>");
				console.log("recognized: skipScream");
			break;
			default:
				console.log("Received event could not be identified");
				console.dir(data);
		}
	}
});

var lastLoudness = 0;
channel.bind('answers', function(data) {
	console.log("answer detected: "+data);
	console.log("id: "+data.id+", name: "+data.name);
	if(data.id == techID && data.name == "setLoudness")
	{
		console.log("setLoudness to "+data.value);
		$("#loudnessIndikator").animate({width:data.value+"px"}, 200);
		
		/*
		$({ value: lastLoudness }).animate({ value: data.value }, 
		{
			step: function() {
				$("#loudnessIndikator").attr("value", this.value);
			},
			complete: function() {
				console.log("complete animation");
				lastLoudness = this.value;
			}
		});*/
	} 
	else
	{
		console.error("Received answer could not be identified");
		console.dir(data);
	}
});

function sendLoudness(volume) {
	$.ajax({
		type: "POST",
		url: "./api/api_braceletAnswer.php",
		data: dataObject
	});
}