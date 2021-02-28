<?php
/**
 * Anonymous Chat - https://www.anonymouschat.cc
 * Created by Edmund Cinco
 */

include "connectdb.php";

$con = connectdb();

if (empty($_POST)) {

    echo '{

			    "messages":[
			        {
			            "text":"Invalid HTTP Method Usage"
			        },
			        {
			            "text":"Please email contact@chatbot.so if you get stuck or have questions."
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

			    "messages":[
			        {
			            "text":"bot id is required"
			        }
			    ]

			}';

    exit;

} else if (strlen($broadcasting_api_token) !== 64) {

    echo '{

			    "messages":[
			        {
			            "text":"broadcasting api token is required"
			        }
			    ]

			}';

    exit;

} else if (empty($messenger_user_id)) {

    echo '{

			    "messages":[
			        {
			            "text":"messenger user id is required"
			        }
			    ]

			}';

    exit;

}

$datetime = date('Y-m-d H:i:s');

$query  = mysqli_query($con, "SELECT * FROM `anonymous_chat` WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id'");
$result = mysqli_fetch_array($query);

if ($result) {
       
    mysqli_query($con, "UPDATE `anonymous_chat` SET broadcasting_api_token = '$broadcasting_api_token', updated_at = '$datetime' WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id'");
    
} else {
      
    // default channel name 'general'
    mysqli_query($con, "INSERT INTO `anonymous_chat` (`bot_id`,`broadcasting_api_token`,`messenger_user_id`,`peer_id`,`channel_name`,`last_status`,`created_at`) VALUES('$bot_id','$broadcasting_api_token','$messenger_user_id','not set','general','available','$datetime')");
    
}
