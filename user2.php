<?php
	//$api_host = "http://chat.framelocker.com:8082/";
	//$api_host = "http://chat.framelocker.com:8081/";
	$api_host = "http://localhost:8081/";
	$token = "bd32bb2c6dbf8697e89d0f9b6b6e1757f79984b8";
	$invite_ids = "5,6";
?>
<!DOCTYPE html>
<html>
<head>
	<title>Chat</title>
	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=$api_host?>socket.io/socket.io.js"></script>	
	<script type="text/javascript" src="js/core.js"></script>
</head>
<body>
<div class="container">	
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="input-group">
	           <input type="text" class="form-control" id="chat_bar" placeholder="Enter Message">
	            <span class="input-group-btn">
	                <button class="btn btn-info" id="send" type="button">SEND</button>
	            </span>
	        </div>
        </div>
        <div class="panel-body">
        	<div class="col-md-12" id="rooms_list">
        	Rooms:<br>
        	<ul></ul>
        </div>
        <div class="panel-body"><button class="btn btn-success" id="undone">Undone things</button></div>
        	<div class="media-list">
        		
        	</div>
        </div>
	</div>	
</div>
<li class="media hide">
	<div class="media-body">
		<div class="media">
            <a class="pull-left" href="#"><img width="50px" class="media-object img-circle " src=""></a>
            <div class="media-body"><span class="chat_msg">[MSG]</span><br><small class="text-muted"><span class="chat_name">[MSG]</span> | 23rd June at 5:00pm</small><hr></div>
        </div>
    </div>
</li>
<script type="text/javascript">
	(function() {
		var socket = io('<?=$api_host?>api?token=<?=$token?>');
	
		var el = $(".media.hide");
		if(socket === undefined)
			return false;	
		

		socket.on('notifications', function(data){
			console.log(data);
		    if(data.request_method == 'connection' && data.status == 1){		    	
		    	$(document).trigger("success_login");		    	
		    	$(document).trigger("buildRooms", [socket, data.user_data]);					    	
		    }
		    if(data.request_type == 'invite'){
		        var reponse = confirm('You have recieved an invitation from  '+data.name + ". Accept? Room "+data.room);
		        if(reponse){
		            // Accept invitation
		            socket.emit('accept_invitaion', {room: data.room, invite_id:data.invite_id}); // 
		        }
		        if(reponse === false){
		        	socket.emit('reject_invitation', {room: data.room, invite_id:data.invite_id});	
		        }
		    }
		});

		$(document).on("success_login", function() {
			alert("done");
		});

		$("#undone").click(function(){
			socket.emit('triggerUndoneRequests', {});
		});

		$("#send").click(function() {
			var msg = $("#chat_bar").val();
			socket.emit('send_message', {room:"105->52->51", msg:msg});
		});

		$("#chat_bar").keypress(function(e){
			if(e.which == 13){
				var msg = $("#chat_bar").val();
				socket.emit('send_message', {room:"105->52->51", msg:msg});	
			}
		});

		socket.on('get_messages', function(data){
			console.log(data);
		    var message_display = $(".media-list");
		    var it = 0;
		    $.each(data, function(i, val){
		    	it++;
		    	if(it == 5)
		    		return false;
		        var chat = el.clone();
		        chat.removeClass("hide");
		        chat.find(".chat_msg").text(val.msg);
		        chat.find("img").attr("src", val.avatar);
		        chat.find(".chat_name").text(val.name);
		        message_display.append(chat);            
		    });`
		});
	})();
	
</script>
</body>
</html>