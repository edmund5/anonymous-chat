<?php
/**
 * Anonymous Chat - https://www.anonymouschat.cc
 * Created by Edmund Cinco - https://www.edmundcinco.com
 */

require "incoming-message.php";

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
$peer_id                = $_POST['peer_id'];
$message                = $_POST['message'];

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

} else if (empty($peer_id)) {

    echo '{

                "messages":[
                    {
                        "text":"peer id is required"
                    }
                ]

            }';

    exit;

} else if (empty($message)) {

    echo '{

                "messages":[
                    {
                        "text":"message is required"
                    }
                ]

            }';

    exit;

}

$datetime = date('Y-m-d H:i:s');

if (strpos($message, '39178562_1505197616293642_5411344281094848512_n.png') OR strpos($message, '851587_369239346556147_162929011_n.png')) {
    
    $result = mysqli_query($con, "SELECT * FROM `anonymous_chat` WHERE bot_id = '$bot_id' AND peer_id = '$peer_id' LIMIT 2") or die(mysqli_error());
    
    while ($row = mysqli_fetch_array($result)) {
               
        $messenger_user_id = $row['messenger_user_id'];
        
        mysqli_query($con, "UPDATE `anonymous_chat` SET peer_id = 'not set', last_status = 'available', updated_at = '$datetime' WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id' AND peer_id = '$peer_id'");
        
        ChatEnded($bot_id, $broadcasting_api_token, $messenger_user_id);
        
    }
    
    exit;
    
}

$result = mysqli_query($con, "SELECT * FROM `anonymous_chat` WHERE bot_id = '$bot_id' AND peer_id = '$peer_id' LIMIT 2") or die(mysqli_error());

while ($row = mysqli_fetch_array($result)) {
    
    if ($row['messenger_user_id'] != $messenger_user_id) {
        
        mysqli_query($con, "UPDATE `anonymous_chat` SET updated_at = '$datetime' WHERE bot_id = '$bot_id' AND peer_id = '$peer_id'");

        Receiver($bot_id, $broadcasting_api_token, $row['messenger_user_id'], $peer_id, $message);
        
    }
    
}

function ChatEnded($bot_id, $broadcasting_api_token, $psid)
{
    
    $bot_id     = $bot_id;
    $block_name = 'Chat Ended';
    $token      = $broadcasting_api_token;
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.chatfuel.com/bots/' . $bot_id . '/users/' . $psid . '/send?chatfuel_token=' . $token . '&chatfuel_block_name=' . urlencode($block_name),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        )
    ));
    
    $response = curl_exec($curl);
    
    $query = json_decode($response, true);
    
    if (!$query['success']) {

        echo '{

                    "messages":[
                        {
                            "text":"Invalid bot id or broadcasting api token or messenger user id"
                        },
                        {
                            "text":"Please email contact@chatbot.so if you get stuck or have questions."
                        }
                    ]

                }';
        
    }
    
    curl_close($curl);
    
}
