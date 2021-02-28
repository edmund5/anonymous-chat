<?php
/**
 * Anonymous Chat - https://www.anonymouschat.cc
 * Created by Edmund Cinco
 */

include "connectdb.php";

$con = connectdb();

if (empty($_GET)) {

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

$bot_id            = $_GET['bot_id'];
$messenger_user_id = $_GET['messenger_user_id'];

if (strlen($bot_id) !== 24) {

    echo '{

			    "messages":[
			        {
			            "text":"bot id is required"
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

$query  = mysqli_query($con, "SELECT * FROM `anonymous_chat` WHERE bot_id = '$bot_id' AND messenger_user_id = '$messenger_user_id' LIMIT 1");
$result = mysqli_fetch_array($query);

if ($result) {
    
    $peer_id = $result['peer_id'];
    
    echo '{
              "set_attributes":
                {
                  "peer_id": "'.$peer_id.'"
                }
            }';
    
} else {

    echo '{

                "messages":[
                    {
                        "text":"Invalid bot id or messenger user id or peer id is missing"
                    },
                    {
                        "text":"Please email contact@chatbot.so if you get stuck or have questions."
                    }
                ]

            }';
	
}
