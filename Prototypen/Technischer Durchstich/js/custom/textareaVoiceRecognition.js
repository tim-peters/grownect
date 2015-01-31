$(document).ready(function() {
		$("textarea").each(function() {
			var hash = $(this).attr("id");
			var that = $(this);
			$(this).after("<a href=\"#\" class=\"startVoiceRecog\" data-id=\""+hash+"\">start Voice Recognition</a>\n")
			.next().click(function() {
				var text = that.val();
				var dataObject = {
					id: techID,
					name: 'startVoiceRec',
					'hash': hash,
					value: text
				};
				
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