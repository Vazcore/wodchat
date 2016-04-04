<?php
	//$api_host = "http://chat.framelocker.com:8082/";
	//$api_host = "http://chat.framelocker.com:8081/";
	$api_host = "http://localhost:8081/";
?>
<!DOCTYPE html>
<html>
<head>
	<title>Test 1</title>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=$api_host?>socket.io/socket.io.js"></script>
</head>
<body>
	<script type="text/javascript">
	$(function(){
		var socket = io('<?=$api_host?>api?token=16f64abc8315c6962e0170767c1f8c19a1d01abc');
		

		socket.on('connect', function(e) {
            console.log('connect');
       	});

       	socket.on('disconnect', function() {
            console.log("socket disconnect");
        });

        socket.on('notifications', function(data){
        	console.log(data);
        });        
	});
	
	</script>
</body>
</html>