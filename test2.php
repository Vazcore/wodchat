<?php
	//$api_host = "http://chat.framelocker.com:8082/";
	//$api_host = "http://chat.framelocker.com:8081/";
	$api_host = "http://localhost:8081/";
	$token = "0256ab22b4fd25d78f205f5d83023cd83cd9a597";//"f87de2a3e49ad2fa7aac0be148c9a37c9de22b6b";
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
	<div class="panel panel-info" id="panelGeneral">
		<div class="panel-heading">
			<div class="input-group">
	           <input type="text" id="chat_bar" class="form-control" placeholder="Enter Message">
	            <span class="input-group-btn">
	                <button class="btn btn-info" id="send" type="button">SEND</button>
	            </span>
	        </div>
        </div>     
        <div class="col-md-12" id="rooms_list">
        	Rooms:<br>
        	<ul></ul>
        </div>           
        <div class="panel-body"><button class="btn btn-success" id="inv">Invite</button></div>
        <div class="panel-body"><button class="btn btn-success" id="undone">Undone things</button></div>
        <div class="panel-body"><button class="btn btn-success" id="more">History</button></div>
        <div class="panel-body">        	
        	<div class="media-list">
        		
        	</div>
        </div>
	</div>	
</div>

<div class="container">
	<div class="row">
		<button class="btn btn-warning" id="checkUsers">Check users</button>
	</div>
    <div class="row">
        <div class="col-md-5">
            <div class="panel panel-primary hide" id="panelPM">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-comment"></span> Chat
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-chevron-down"></span>
                        </button>
                        
                    </div>
                </div>
                <div class="panel-body">
                    <ul class="chat" style="list-style:none;">
                        <li class="left clearfix chat_el hide"><span class="chat-img pull-left">
                            <img src="#" alt="User Avatar" class="img-circle">
                        </span>
                            <div class="chat-body clearfix">
                                <div class="header">
                                    <strong class="primary-font pm_name"></strong> 
                                    <small class="pull-right text-muted">
                                        <span class="glyphicon glyphicon-time"></span>12 mins ago
                                    </small>
                                </div>
                                <p class="pm_msg">
                                    
                                </p>
                            </div>
                        </li>                      
                        
                    </ul>
                </div>
                <div class="panel-footer">
                    <div class="input-group">
                        <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here...">
                        <span class="input-group-btn">
                            <button class="btn btn-warning btn-sm" id="btn-chat">
                                Send</button>
                        </span>
                    </div>
                </div>
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
		var pm_el = $(".chat_el");
		if(socket === undefined)
			return false;	
		
		$("#more").click(function() {
			socket.emit('triggerRoomHistory', {room: "Baltazor", offset:0, limit: 100});						
		});			

		$("#inv").click(function() {			
			socket.emit('invite_to_chat', {uid:[51,52]});
			//socket.emit('trigger_get_boxes');
		});	

		$("#undone").click(function(){
			socket.emit('triggerUndoneRequests', {});
		});

		socket.on('getUndoneRequests', function(data){
			console.log(data);
		});


		socket.on('get_boxes', function(boxes){
			console.log(boxes);
		});

		

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
		            socket.emit('accept_invitation', {room: data.room, invite_id:data.invite_id}); // 
		        }
		        if(reponse === false){
		        	socket.emit('reject_invitation', {room: data.room, invite_id:data.invite_id});	
		        }
		    }

		    if(data.type == "accepted_invitation"){
		    	$("#panelGeneral").addClass("hide");
		    	$("#panelPM").removeClass("hide");
		    	var udata = data.params.userData;

		    	$("#btn-chat").click(function() {
					var val = $(this).closest(".input-group").find("input").val();
					console.log(data);					
					socket.emit("send_message", {room:data.params.room, msg:val});
				});

				$("#checkUsers").click(function() {
					socket.emit('get_room_users', {room: data.params.room}); 
				});			    	

		    }	
		});

		$(document).on("success_login", function() {
			//socket.emit('join_room', {"room":"820->105"});					
			//socket.emit('join_room', {"room":"772->84"});					
		});

		$("#send").click(function() {
			var msg = $("#chat_bar").val();
			socket.emit('send_message', {room:"public", msg:msg, cacheKey: hashCode(makeid()+makeid())});
		});

		$("#chat_bar").keypress(function(e){
			if(e.which == 13){
				var msg = $("#chat_bar").val();
				socket.emit('send_message', {room:"public", msg:msg});	
			}
		});

		hashCode = function(str){
	        var hash = 0,
	            len = str.length;
	    
	        for (var i = 0; i < len; i++) {
	            hash = hash * 31 + str.charCodeAt(i);
	        }
	        return hash;
	    }

	    function makeid()
		{
		    var text = "";
		    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

		    for( var i=0; i < 5; i++ )
		        text += possible.charAt(Math.floor(Math.random() * possible.length));

		    return text;
		}

		socket.on('get_messages', function(data){			
		    var message_display = $(".media-list");
		    console.log(data);
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
		    });
		});

	})()
	
</script>
</body>
</html>