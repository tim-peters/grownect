<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once("./classes/class_Log.php");
require_once("./classes/class_Conflict.php");
require_once("./classes/class_Pusher.php");
include_once("./db_connect.inc");
include_once("./classes/class_User.php");

if(!isset($_GET['id']) || !$user_object = User::FromDb($_GET['id']))
	die("No User specified");

?>
<!DOCTYPE html>
<html>
<head>
	<title>Bracelet</title>
	<script type="text/javascript" src="./js/core/pusher.min.js"> </script>
	<script type="text/javascript" src="./js/core/jquery.js"> </script>
	<script type="text/javascript">
	var techID = "<?php echo $user_object->tech_id; ?>";

	var timeBetweenLoudnessPosts =  250;

	var clearID;
	var audioContext = null;
	var audioSource = null;
	var audioAnalyser = null;
	var audioGain = null;
	var audioData = null;
	var volume = null;
	window.stopRecording = false;

	/**
	 * Source: http://www.standardabweichung.de/code/javascript/html5-microphone-access
	 */
	var audioStart = function (){
	    navigator.getUserMedia = ( navigator.getUserMedia ||
	                       navigator.webkitGetUserMedia ||
	                       navigator.mozGetUserMedia ||
	                       navigator.msGetUserMedia);
	    
	    navigator.getUserMedia({
	        audio : true,
	        video : false
	    }, audioSuccess, audioError);
	};

	var audioStop = function () {
		window.stopRecording = true;
	}

	var audioSuccess = function(stream) {
	    window.AudioContext = ( window.AudioContext ||
	    				    window.webkitAudioContext ||
	    				    window.mozAudioContext ||
	    				    window.msAudioContext);
	    
	    var fftSize = 256;
	    
	    audioContext = new AudioContext();
	    audioSource = audioContext.createMediaStreamSource(stream);
	    
	    audioGain = audioContext.createGain();
	    audioGain.gain.value = 0; // Output volume
	    audioGain.connect(audioContext.destination);
	    
	    audioAnalyser = audioContext.createAnalyser();
	    audioAnalyser.fftSize = fftSize;
	    audioAnalyser.connect(audioGain);
	    
	    audioData = new Uint8Array(fftSize * 0.5);
	    audioSource.connect(audioGain);
	    audioSource.connect(audioAnalyser);
	    
	    var timeSinceReset = 0;
	    var sampleAudioStream = function() {
		    if(window.stopRecording)
		    {
		    	console.log("audio stop");
		    	clearInterval(clearID); // stop sending
		    	stream.stop(); // free microphone
		    	window.stopRecording = false;
		    }

	        audioAnalyser.getByteFrequencyData(audioData);
	        
	        for (var i = 0, length = audioData.length, sum = 0; i < length; i++) {
	            sum += audioData[i];
	        }
	        
	        volume = sum / (length * 256);
	        document.getElementById("h1").innerHTML = volume;
	        timeSinceReset += 20;
	        if(timeSinceReset >= timeBetweenLoudnessPosts) 
	        { 
	        	sendLoudness(volume);
	        	timeSinceReset = 0;
	        }
	    };
	    clearID = setInterval(sampleAudioStream, 20);
	    
	    volume = 0;
	};

	var audioError = function(event) {
	  console.error("audioError");
	};

	function sendLoudness(volume) {
		console.log("working");
		var dataObject = {
			id: techID,
			name: 'setLoudness',
			value: volume
		};

		$.ajax({
			type: "POST",
			url: "./api/api_braceletAnswer.php",
			data: dataObject
		});
	}

	var sendBlocker;
	function initializeSendText(_val, _hash) {
		clearTimeout(sendBlocker);
		sendBlocker = setTimeout(function(val, hash) { 
			sendText(val, hash) 
		},200, _val, _hash);
	}

	function sendText(text, hash) {
		console.log("working");
		var dataObject = {
			id: techID,
			hash: hash,
			name: 'setText',
			value: text
		};

		$.ajax({
			type: "POST",
			url: "./api/api_braceletAnswer.php",
			data: dataObject
		});
	}

	function initializeConflict(data) {
		$("h1").text("blinkt").css("color", data.value); // FIXME
		clearID = setTimeout(function() { read(data.text); }, 5000);
		
		$(".buttons").append(function() {
			return $("<button name='nope'>Hauen (nein)</button>").click(function() {$(this).hide(); $("button[name='jep']").hide(); knowsProblem(false, data.conflict)});
		});
		$(".buttons").append(function() {
			return $("<button name='jep'>Wischen (ja)</button>").click(function() {$(this).hide(); $("button[name='nope']").hide(); knowsProblem(true, data.conflict)});
		});
	}

	function getNameFromConflict(id) {
		var eventName = "getNameFromConflict";
		var dataObject = {
			id: techID,
			name: eventName,
			value: id
		};

		var result="";
		$.ajax({
			type: "POST",
			url: "./api/api_braceletAnswer.php",
			data: dataObject,
			async:false,
			success:function(data) {
				result = data;
			}
		});
		return result;
	}

	function blurMirror(id) {
		var dataObject = {
			id: techID,
			name: "blurMirror",
			value: id
		};

		$.ajax({
			type: "POST",
			url: "./api/api_braceletAnswer.php",
			data: dataObject
		});
	}

	function solve(id) {
		var dataObject = {
			id: techID,
			name: "solveConflict",
			value: id
		};

		$.ajax({
			type: "POST",
			url: "./api/api_braceletAnswer.php",
			data: dataObject,
			success: function(data) {
				if(data == '1')
					read("conflict solved");
			}
		});
	}

	function askIfSolved(id) {
		$(".buttons").append(function() {
			return $("<button name='nope'>Hauen (nein)</button>").click(function() {$(this).hide(); $("button[name='jep']").hide(); blurMirror(id);});
		});
		$(".buttons").append(function() {
			return $("<button name='jep'>Wischen (ja)</button>").click(function() {$(this).hide(); $("button[name='nope']").hide(); solve(id);});
		});
	}

	function deliverExplanation(explan, id) {
		var dataObject = {
			id: techID,
			name: "setExplanation",
			value: id,
			text: explan
		};

		var result="";
		$.ajax({
			type: "POST",
			url: "./api/api_braceletAnswer.php",
			data: dataObject,
			async:false
		});
	}

	function getExplanation(id) {
		$(".buttons").append(function() {
			return $("<button name='nope'>Hauen (nein)</button>").click(function() {$(this).hide(); $("button[name='jep']").hide(); $("textarea[name='explanation']").hide(); blurMirror(id)});
		});
		$(".buttons").append(function() {
			return $("<textarea name='explanation'></textarea>").focus();
		});
		$(".buttons").append(function() {
			return $("<button name='jep'>Wischen (ja)</button>").click(function() {$(this).hide(); $("button[name='nope']").hide(); $("textarea[name='explanation']").hide(); deliverExplanation($("textarea[name='explanation']").val(), id) });
		});
	}

	function demandExplanation(id) {
		var name = getNameFromConflict(id);
		read("You should let "+name+" know why you acted that way, to let him understand the situation better. If you don't want to help solving the conflict, it's time to hit the bracelet now. Otherwise tab and hold the bracelet and start talking.", getExplanation(id));
	}

	function read(text, callback) {
		console.log("reading: "+text);

	    var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', 'http://tts-api.com/tts.mp3?q='+encodeURIComponent(text));
        audioElement.setAttribute('autoplay', 'autoplay');
        //audioElement.load()
        $.get();
        audioElement.addEventListener("load", function() {
        	audioElement.play();
        }, true);
    
    	if(callback != undefined)
			audioElement.addEventListener("ended", function() {
				callback();
			}, false);
	}

	function knowsProblem(jep, id) {
		clearTimeout(clearID);
		if(jep) // User knows the problem
		{
			// Tells User to explain his situation
			demandExplanation(id);
		}
		else // User does not know what's the problem
		{
			// Demand explanation
			var eventName = "getProblemDescription";
			var dataObject = {
				id: techID,
				name: eventName,
				value: id
			};

			function processAnswer(answer) {
				read(answer, function() { demandExplanation(id) });
			};

			$.ajax({
				type: "POST",
				url: "./api/api_braceletAnswer.php",
				data: dataObject,
				success: processAnswer
			});

		}
	}

	channel.bind('events', function(data) {
		console.log("event registered");
		if(data.id == techID)
		{
			console.log("event accepted: "+data.name);
			switch(data.name) {
				case "checkPulse":
					getPulse(function (pulse) {
						console.log("working");
						var dataObject = {
							id: techID,
							name: 'setPulse',
							value: pulse
						};
						
						$.ajax({
							type: "POST",
							url: "./api/api_braceletAnswer.php",
							data: dataObject,
							success: handlePulseAnswer
						});
					});
				break;

				case "startScream":
					audioStart();
				break;

				case "endScream":
					audioStop();
				break;

				case "getText":

					$("h1").after("<textarea>"+data.value+"</textarea>")
							.next().delay(0).focus()
							.bind('keyup input change', function(){
								initializeSendText($(this).val(), data.hash);
							})
							.focusout(function() { 
								$(this).remove()
							});
				break;

				case "conflictCreated":
					console.log("conflictCreated");
					initializeConflict(data);
				break;

				case "setExplanation":
					read(data.text,askIfSolved(data.conflict));
				break;

				default:
					console.error("Received event could not be identified");
					console.dir(data);
			}
		}
	});

	$(document).ready(function() {
		var isAtMirror = false;
		$("input[name='mirror']").click(function(){
			var that = this;
			var user_id = getParameterByName('id');
			if(!isAtMirror)
			{
				console.log("working");
				var dataObject = {
					id: techID,
					name: 'setUser',
					user: user_id
				};
				
				$.ajax({
					type: "POST",
					url: "./api/api_braceletAnswer.php",
					data: dataObject,
					user: user_id
				}).done(function() {
					$(that).val("I'm away from mirror");
					isAtMirror = true;
				});
			}
			else
			{
				console.log("working");
				var dataObject = {
					id: techID,
					name: 'leaveUser',
					user: user_id
				};
				
				$.ajax({
					type: "POST",
					url: "./api/api_braceletAnswer.php",
					data: dataObject
				}).done(function() {
					$(that).val("I'm in front of the mirror");
					isAtMirror = false;
				});
			}
		});
	});

	function handlePulseAnswer(msg) {
		console.log("Answer: "+msg);
	}

	function getPulse(callback) {
		setTimeout(function() {
			console.log("Pulse ermittelt");
			callback(Math.floor(Math.random() * (170 - 55 + 1)) + 55); // create random pulse value
			//callback(129);
		}, 1000);
	}

	/**
	 * SOURCE: http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
	 */
	function getParameterByName(name) {
	    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
	        results = regex.exec(location.search);
	    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	</script>
</head>
<body>
<h1 id="h1"><?php echo $user_object->name; ?>'s Bracelet</h1>
<div class="buttons">
	<input type="button" name="mirror" value="I'm in front of the mirror">
</div>
</body>
</html>