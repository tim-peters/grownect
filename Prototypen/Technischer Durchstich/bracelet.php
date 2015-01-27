<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once("./classes/class_Log.php");

include_once("./classes/class_User.php");

if(!isset($_GET['id']) || !$user_object = User::FromDb($_GET['id']))
	die("No User specified");

?>
<!DOCTYPE html>
<html>
<head>
	<title>Bracelet</title>
	<link rel="stylesheet" href="./style/all.css" type="text/css" />
	<script type="text/javascript" src="./js/core/pusher.min.js"> </script>
	<script type="text/javascript" src="./js/core/jquery.js"> </script>
	<script type="text/javascript">
	var techID = "<?php echo $user_object->tech_id; ?>";

	var timeBetweenLoudnessPosts =  250;

	var audioClearID;
	var audioContext = null;
	var audioSource = null;
	var audioAnalyser = null;
	var audioGain = null;
	var audioData = null;
	var volume = null;

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
		console.log("audio stop")
		clearInterval(audioClearID);
		audioContext = null;
		audioSource = null;
		audioAnalyser = null;
		audioGain = null;
		audioData = null;
		volume = null;
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
	    
	    audioClearID = setInterval(sampleAudioStream, 20);
	    
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

					$("h1").after("<textarea></textarea>")
							.next().delay(0).focus()
							.bind('keyup input change', function(){
								initializeSendText($(this).val(), data.hash);
							})
							.focusout(function() { 
								$(this).remove()
							});
				break;

				default:
					console.error("Received event could not be identified");
			}
		}
	});

	/*channel.bind('answers', function(data) {
		console.log("answer registered");
		if(data.id == techID)
		{
			console.log("answer accepted: "+data.name);
			switch(data.name) {
				case "setLoudness":
				default:
					console.error("Received event could not be identified");
			}
		}
	});*/

	function handlePulseAnswer(msg) {
		console.log("Answer: "+msg);
	}

	function getPulse(callback) {
		setTimeout(function() {
			console.log("Pulse ermittelt");
			callback(Math.floor(Math.random() * (170 - 55 + 1)) + 55);
			//callback(129);
		}, 1000);
	}

	/*
	function getLoudness(callback) {
		setTimeout(function() {
			console.log("Loudness ermittelt");
			callback(Math.random());
		}, 50);
	}
	*/

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
</body>
</html>