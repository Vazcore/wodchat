<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Socket requests</title>
	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="http://chat.framelocker.com:8082/socket.io/socket.io.js"></script>
</head>
<body>
<script type="text/javascript">
	$(function() {
		var socket = io("http://chat.framelocker.com:8082/general");
		socket.emit('stream_event', {id:1});
	});
</script>
</body>
</html>