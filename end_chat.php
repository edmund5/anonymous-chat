<?php
/**
 * Anonymous Chat
 * Created by Edmund Cinco
 * Website: https://www.edmundcinco.com
 */

include "connectdb.php";

$con = connectdb();

if (empty($_POST)) {

    echo '{
              "messages": [
                {
                  "text": "Invalid HTTP Method"
                },
                {
                  "text": "Please email edmundcinco@me.com if you get stuck or have questions."
                }
              ]
          }';
    
    exit;

}

$bot_id                 = $_POST['bot_id'];
$broadcasting_api_token = $_POST['broadcasting_api_token'];
$messenger_user_id      = $_POST['messenger_user_id'];
$peer_id                = $_POST['peer_id'];

if (strlen($bot_id) !== 24) {

    echo '{
             "messages": [
               {"text": "Invalid Bot Id: ' . $bot_id . '"}
             ]
            }';

    exit;

} else if (strlen($broadcasting_api_token) !== 64) {

    echo '{
             "messages": [
               {"text": "Invalid Broadcasting API Token: ' . $broadcasting_api_token . '"}
             ]
            }';

    exit;

} else if (strlen($messenger_user_id) !== 16) {

    echo '{
             "messages": [
               {"text": "Invalid Messenger User Id: ' . $messenger_user_id . '"}
             ]
            }';

    exit;

}

$updatedAt = date('Y-m-d H:i:s');

$result = mysqli_query($con, "SELECT * FROM `anonymous_chat` WHERE bot_id = '$bot_id' AND peer_id = '$peer_id' LIMIT 2") or die(mysqli_error());

while ($row = mysqli_fetch_array($result)) {

	if ($row['messenger_user_id'] != $messenger_user_id) {

	   ChatEnded($bot_id, $broadcasting_api_token, $row['messenger_user_id']);

     // peer_id = 'not set' and lastStatus = 'available', 
	   mysqli_query($con, "UPDATE `anonymous_chat` SET peer_id = 'not set', lastStatus = 'available', updatedAt = '$updatedAt' WHERE bot_id = '$bot_id' AND peer_id = '$peer_id'");

	}

}

function ChatEnded($bot_id, $broadcasting_api_token, $psid)
{

    $bot_id     = $bot_id;
    $block_name = 'Chat Ended';
    $token      = $broadcasting_api_token;

    $ch = curl_init('https://api.chatfuel.com/bots/' . $bot_id . '/users/' . $psid . '/send?chatfuel_token=' . $token . '&chatfuel_block_name=' . urlencode($block_name));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($ch);
    curl_close($ch);

}
