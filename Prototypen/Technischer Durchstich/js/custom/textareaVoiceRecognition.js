$(document).ready(function() {
		$("textarea").each(function() {
			var that = $(this);
			var hash = $(this).attr("id");
			$(this).parent().find(".input img:first-of-type")
			.click(function() {
				var text = that.val();
				var dataObject = {
					id: techID,
					name: 'startVoiceRec',
					'hash': hash,
					value: text
				};
				//console.dir(dataObject);
				$.ajax({
					type: "POST",
					url: "./api/api_braceletAnswer.php",
					data: dataObject,
					success: listenForText
				});
			});
		});
	});


	function listenForText() {
		channel.bind('answers', function(data) {
			console.log("event registered");
			if(data.id == techID)
			{
				console.log("event accepted: "+data.name);
				switch(data.name) {
					case "setText":
						$("#"+data.hash).val(data.value);
					break;
					default:
						console.error("Received event could not be identified");
				}
			}
		});
	}