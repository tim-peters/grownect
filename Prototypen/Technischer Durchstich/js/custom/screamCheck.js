var loudnessInput = $("<input type=\"text\" value=\"0\" class=\"dial\">");

channel.bind('events', function(data) {
	console.log("event detected: "+data);
	console.log("id: "+data.id+", name: "+data.name);
	if(data.id == techID)
	{
		switch(data.name) {
			case "startScream":
				$("h2").html("Let out your rage by screaming into your<br> bracelet as loud as you can.")
							.after("<div class=\"scream\"></div>").next().append(loudnessInput);
				loudnessInput.knob({
			        'min':0,
			        'max':.3,
			        'step':0.01,
			        'angleArc':180,
			        'angleOffset':-90,
			        'readOnly':true,
			        'displayPrevious':true,
			        'thickness':0.1,
			        'fgColor':"#fff",
			    	'bgColor':'#333',
			    	'inputColor':"#ffffff"

			    });
				console.log("recognized: startScream");
			break;
			case "endScream":
				$(".scream").hide();
				$("h2").html("Feeling better now, right?<br>Screaming out your anger can help to calm down.").append("<a href=\""+redirectURI+"\">Continue</a>");
				console.log("recognized: endScream");
			break;
			case "skipScream":
				window.location.href = redirectURI;
				//$("h2").html("Congratulations! It seems like you are calm enough to solve this conflict. <a href=\""+redirectURI+"\">Continue</a>");
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
		loudnessInput.val(data.value).trigger("change");
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