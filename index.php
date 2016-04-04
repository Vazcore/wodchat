<?php



$deviceToken = "FE66489F304DC75B8D6E8200DFF8A456E8DAEACEC428B427E9518741C92C6660";

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '/cert.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', "awedxzs123");

// My alert message here:
$message = 'New Push Notification from Misnk Developers Wod Chat! Write to Paul if we recieved this message';

//badge
$badge = 1;

$fp = stream_socket_client(
'ssl://gateway.sandbox.push.apple.com:2195', $err,
$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
  exit("Failed to connect: $err $errstr" . PHP_EOL);

 echo 'Connected to APNS' . PHP_EOL;

  // Create the payload body
$body['aps'] = array(
    'alert' => $message,
    'badge' => $badge,
    'sound' => 'newMessage.wav'
);

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload))  . $payload;

$result = fwrite($fp, $msg, strlen($msg));

if (!$result) echo 'Error, notification not sent' . PHP_EOL; else echo 'notification sent!' . PHP_EOL;

 // Close the connection to the server
 fclose($fp);


?>