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
                  "text": "Invalid request method."
                },
                {
                  "text": "Please email contact@somethingtechie.co if you get stuck or have questions."
                }
              ]
          }';
    
    exit;

}

$bot_id                 = $_POST['bot_id'];
$broadcasting_api_token = $_POST['broadcasting_api_token'];
$messenger_user_id      = $_POST['messenger_user_id'];

if (strlen($bot_id) !== 24) {

    echo '{
             "messages": [
               {"text": "Invalid bot_id: ' . $bot_id . '"}
             ]
            }';

    exit;

} else if (strlen($broadcasting_api_token) !== 64) {

    echo '{
             "messages": [
               {"text": "Invalid broadcasting_api_token: ' . $broadcasting_api_token . '"}
             ]
            }';

    exit;

} else if (strlen($messenger_user_id) !== 16) {

    echo '{
             "messages": [
               {"text": "Invalid messenger_user_id: ' . $messenger_user_id . '"}
             ]
            }';

    exit;

}

// Timezone
date_default_timezone_set('UTC');

$createdAt = date('Y-m-d H:i:s');
$updatedAt = $createdAt;

$query  = mysqli_query($con, "SELECT * FROM `anonymous_chat` WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id'") or die(mysqli_error());
$result = mysqli_fetch_array($query);

if ($result) {

	mysqli_query($con, "UPDATE `anonymous_chat` SET broadcasting_api_token = '$broadcasting_api_token', updatedAt = '$updatedAt' WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id'");

} else {

	mysqli_query($con, "INSERT INTO `anonymous_chat` (`bot_id`,`broadcasting_api_token`,`messenger_user_id`,`pairId`,`channelName`,`lastStatus`,`createdAt`) VALUES('$bot_id','$broadcasting_api_token','$messenger_user_id','--','--','--','$createdAt')");

}
