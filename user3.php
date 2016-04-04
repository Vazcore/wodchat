<?php
	//$api_host = "http://chat.framelocker.com:8082/";
	$api_host = "http://chat.framelocker.com:8081/";
	//$api_host = "http://localhost:8081/";
	//$token = "bd32bb2c6dbf8697e89d0f9b6b6e1757f79984b8";
	//$token = "c62de1f39358e4896f297eeea85586316532567b";
	//$token = "26d33d014abe714efedf90459ad3c3b021147a71";
	$token = "4b4d5701982f1c03609e7c43145134f3233fa86a";
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
        <div class="col-md-12" id="rooms_list">
        	<button class="btn btn-success" id="getUndoneThings">Trigger undone things</button>
        	<br><br>
        	<button class="btn btn-success" id="inv">Invite</button><br>
        	Rooms:<br>
        	<ul></ul>
			
			<button class="btn btn-success" id="reorg_chat">Change chat name</button><br>
        </div>
        <div class="panel-body">        
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
		    if(data.request_type == "user_login"){		    	
				var room = data.params.room.name;				
				socket.emit("triggerRoomHistory", {room:room, offset:0,limit:10});
		    }
		});

		$(document).on("success_login", function() {
			socket.emit('join_room', {room:"test_room_777"});
		});

		$("#send").click(function() {
			var msg = $("#chat_bar").val();
			socket.emit('send_message', {room:$(".media-list").attr("room"), msg:msg, cacheKey:"huawey_111"});
		});		

		$("#chat_bar").keypress(function(e){
			if(e.which == 13){
				var msg = $("#chat_bar").val();
				socket.emit('send_message', {room:$(".media-list").attr("room"), msg:msg});	
			}
		});
		
		$("#reorg_chat").click(function(){
			//var data = prompt("Enter ids separated by comma");
			//var ids = data.split(",");
			socket.emit("reorganized_current_chat", {current_room:"815->810", new_room:"815->810->800"});
		});
		
		$("#inv").click(function() {			
			socket.emit('invite_to_chat', {uid:800});
			//socket.emit('trigger_get_boxes');
		});	

		socket.on('get_messages', function(data){
			console.log(data);
		    var message_display = $(".media-list");
		    var it = 0;
		    $.each(data, function(i, val){
		    	it++;
		    	
		        var chat = el.clone();
		        chat.removeClass("hide");
		        chat.find(".chat_msg").text(val.msg);
		        chat.find("img").attr("src", val.avatar);
		        chat.find(".chat_name").text(val.name);
		        message_display.append(chat);            
		    });
		});
	})()
	
</script>
</body>
</html>