<?php
/**
 * Anonymous Chat
 * Created by Edmund Cinco
 * Website: https://www.edmundcinco.com
 */

function ReceiveMessage($bot_id, $broadcasting_api_token, $psid, $peer_id, $message)
{

    $bot_id     = $bot_id;
    $block_name = 'Receive Message';
    $token      = $broadcasting_api_token;

    $ch = curl_init('https://api.chatfuel.com/bots/' . $bot_id . '/users/' . $psid . '/send?chatfuel_token=' . $token . '&chatfuel_block_name=' . urlencode($block_name) . '&peer_id=' . $peer_id . '&chatMessage=' . urlencode($message));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($ch);
    curl_close($ch);

}
