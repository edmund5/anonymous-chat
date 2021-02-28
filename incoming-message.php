<?php
/**
 * Anonymous Chat - https://www.anonymouschat.cc
 * Created by Edmund Cinco
 */

function Receiver($bot_id, $broadcasting_api_token, $psid, $peer_id, $message)
{
    
    $bot_id     = $bot_id;
    $block_name = 'Incoming Message';
    $token      = $broadcasting_api_token;
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.chatfuel.com/bots/' . $bot_id . '/users/' . $psid . '/send?chatfuel_token=' . $token . '&chatfuel_block_name=' . urlencode($block_name) . '&peer_id=' . $peer_id . '&incoming_message=' . urlencode($message),
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
