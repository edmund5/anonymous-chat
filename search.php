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
$channel_name           = $_POST['channel_name'];

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

} else if (empty($channel_name)) {

    echo '{

                "messages":[
                    {
                        "text":"Channel Name is required"
                    }
                ]

            }';

    exit;

}

$datetime = date('Y-m-d H:i:s');

$query  = mysqli_query($con, "SELECT * FROM `anonymous_chat` WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id'");
$result = mysqli_fetch_array($query);

if ($result) {
    
    mysqli_query($con, "UPDATE `anonymous_chat` SET channel_name = '$channel_name', last_status = 'available', updated_at = '$datetime' WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id'");
    
}

$result = mysqli_query($con, "SELECT * FROM `anonymous_chat` WHERE bot_id = '$bot_id' AND channel_name = '$channel_name' AND last_status = 'available' ORDER BY RAND() LIMIT 2");

while ($row = mysqli_fetch_array($result)) {
    
    if ($row['messenger_user_id'] != $messenger_user_id) {
        
        $psid1 = $messenger_user_id;
        $psid2 = $row['messenger_user_id'];
        
        // create peer id based on 'psid'
        $peer_id = md5($psid1 . $psid2);
        
        mysqli_query($con, "UPDATE `anonymous_chat` SET peer_id = '$peer_id', last_status = 'matched', updated_at = '$datetime' WHERE bot_id = '$bot_id' AND channel_name = '$channel_name' AND messenger_user_id = '$psid1'");
        
        mysqli_query($con, "UPDATE `anonymous_chat` SET peer_id = '$peer_id', last_status = 'matched', updated_at = '$datetime' WHERE bot_id = '$bot_id' AND channel_name = '$channel_name' AND messenger_user_id = '$psid2'");
             
        // redirect receiver to 'Chat Started'
        ChatStarted($bot_id, $broadcasting_api_token, $psid2, $peer_id, $channel_name);

        // redirect sender to 'Check Peer Id'
        echo '{

                    "redirect_to_blocks":[
                        "Check Peer Id"
                    ]

                }';
        
        exit;
        
    }
    
}

$query  = mysqli_query($con, "SELECT * FROM `anonymous_chat` WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id'");
$result = mysqli_fetch_array($query);

if ($result) {
    
    mysqli_query($con, "UPDATE `anonymous_chat` SET updated_at = '$datetime' WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id'");
    
    NoMatchFound($bot_id, $broadcasting_api_token, $messenger_user_id, $channel_name);
    
}

function ChatStarted($bot_id, $broadcasting_api_token, $psid, $peer_id, $channel_name)
{
    
    $bot_id     = $bot_id;
    $block_name = 'Chat Started';
    $token      = $broadcasting_api_token;
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.chatfuel.com/bots/' . $bot_id . '/users/' . $psid . '/send?chatfuel_token=' . $token . '&chatfuel_block_name=' . urlencode($block_name) . '&peer_id=' . $peer_id . '&channel_name=' . urlencode($channel_name),
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

function NoMatchFound($bot_id, $broadcasting_api_token, $psid, $channel_name)
{
    
    $bot_id     = $bot_id;
    $block_name = 'No Match Found';
    $token      = $broadcasting_api_token;
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.chatfuel.com/bots/' . $bot_id . '/users/' . $psid . '/send?chatfuel_token=' . $token . '&chatfuel_block_name=' . urlencode($block_name) . '&channel_name=' . urlencode($channel_name),
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
