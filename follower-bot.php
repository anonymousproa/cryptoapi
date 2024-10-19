<?php
$botToken = "7428265837:AAEvf7dNPZICchFGOloKodM3MNkokQeB9vk";
$apiURL = "https://api.telegram.org/bot$botToken/";
$jsonFile = 'refferdatamega.json';

function getBotUsername($apiURL) {
    $response = file_get_contents($apiURL . "getMe");
    $response = json_decode($response, true);
    
    return $response['result']['username'] ?? null;
}

$BotUsername = getBotUsername($apiURL);

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

$chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
$firstName = $update['message']['chat']['first_name'] ?? $update['callback_query']['from']['first_name'];
$fromUsername = $update['message']['chat']['username'] ?? $update['callback_query']['from']['username'];

function loadUsers($file) {
    if (!file_exists($file)) {
        return [];
    }
    return json_decode(file_get_contents($file), true);
}

function saveUsers($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

$users = loadUsers($jsonFile);

if (isset($update['message']['text']) && strpos($update['message']['text'], "/start") === 0) {
    $referrerId = null;
    if (preg_match("/\/start (\d+)/", $update['message']['text'], $matches)) {
        $referrerId = $matches[1];
    }

    // If the user is new
    if (!array_key_exists($chatId, $users)) {
        $users[$chatId] = [
            'coins' => 0,
            'referredBy' => $referrerId ? [$referrerId] : null,
            'directStart' => $referrerId ? false : true,
            'referpending' => $referrerId ? true : false // Set pending if referred
        ];
        saveUsers($jsonFile, $users);
    }

    $coins = $users[$chatId]['coins'];

    $photo = "https://i.ibb.co/Pjz0gtG/IMG-20241013-082741.jpg"; 

    $caption = "🚀 𝗛𝗲𝘆 $firstname\n\n⭐️ 𝗪𝗘𝗟𝗖𝗢𝗠𝗘 𝗧𝗢 𝗠𝗘𝗚𝗔 𝗙𝗢𝗟𝗟𝗢𝗪𝗘𝗥𝗦 𝗕𝗢𝗧\n\n⚡️ 𝗚𝗘𝗧 𝗜𝗡𝗦𝗧𝗔𝗚𝗥𝗔𝗠 𝗙𝗢𝗟𝗟𝗢𝗪𝗘𝗥𝗦 𝗙𝗥𝗘𝗘 𝗢𝗙 𝗖𝗢𝗦𝗧 💫\n\n🔰 𝗣𝗟𝗘𝗔𝗦𝗘 𝗝𝗢𝗜𝗡 𝗔𝗟𝗟 𝗖𝗛𝗔𝗡𝗡𝗘𝗟𝗦";
    
    $inlineKeyboard = [
        'inline_keyboard' => [
            [
                ['text' => '𝗝𝗢𝗜𝗡', 'url' => 'https://t.me/+8D5R11wfLP1kYmQ1'],
                ['text' => '𝗝𝗢𝗜𝗡', 'url' => 'https://t.me/+crusTm9Vo9w0ZjU1'],
            ],
            [
                ['text' => '𝗙𝗢𝗟𝗟𝗢𝗪', 'url' => 'https://www.instagram.com/performerxd?igsh=aW5heXMzMnRpc2pi'],
                ['text' => '𝗦𝗨𝗕𝗦𝗖𝗥𝗜𝗕𝗘', 'url' => 'https://youtube.com/@performerxd?si=g6oU59jYETZvYgN4'],
            ],
            [
                ['text' => '𝗝𝗢𝗜𝗡', 'url' => 'https://t.me/dark_performer_chat'],
                ['text' => '𝗝𝗢𝗜𝗡', 'url' => 'https://t.me/darkperformer'],
            ]
            
        ]
    ];

    $postFields = [
        'chat_id' => $chatId,
        'photo' => $photo,
        'caption' => $caption,
        'reply_markup' => json_encode($inlineKeyboard),
        'parse_mode' => 'HTML'
    ];

    file_get_contents($apiURL . "sendPhoto?" . http_build_query($postFields));

    $replyKeyboard = [
        'keyboard' => [
            [['text' => '𝗩𝗘𝗥𝗜𝗙𝗬']]
        ],
        'resize_keyboard' => true,
        'one_time_keyboard' => false
    ];

    $postFields = [
        'chat_id' => $chatId,
        'text' => "⚠️ Aꜰᴛᴇʀ Jᴏɪɴɪɴɢ Aʟʟ Cʜᴀɴɴᴇʟꜱ Cʟɪᴄᴋ Oɴ Vᴇʀɪғʏ Bᴜᴛᴛᴏɴ.",
        'reply_markup' => json_encode($replyKeyboard),
        'parse_mode' => 'HTML'
    ];

    file_get_contents($apiURL . "sendMessage?" . http_build_query($postFields));
}

$requiredChannels = ['@DARK_PERFORMER', '@dark_performer_chat', '@giftcodekiduniyaaaaa'];

$notJoinedChannels = [];

if (isset($update['message']['text']) && $update['message']['text'] == "𝗩𝗘𝗥𝗜𝗙𝗬") {
    foreach ($requiredChannels as $channel) {
        // Check if the user has joined each required channel
        $chatMemberStatus = json_decode(file_get_contents($apiURL . "getChatMember?chat_id=$channel&user_id=$chatId"), true);

        if ($chatMemberStatus['result']['status'] != 'member' && $chatMemberStatus['result']['status'] != 'administrator' && $chatMemberStatus['result']['status'] != 'creator') {
            // User hasn't joined this particular channel, add to the list
            $notJoinedChannels[] = $channel;
        }
    }

    if (count($notJoinedChannels) > 0) {
        // User hasn't joined all the required channels
        $message = "⚠️ 𝗠𝘂𝘀𝘁 𝗝𝗼𝗶𝗻 𝗮𝗹𝗹 𝗖𝗵𝗮𝗻𝗻𝗲𝗹𝘀";

        // Keep showing the '𝗩𝗘𝗥𝗜𝗙𝗬' button
        $replyKeyboard = [
            'keyboard' => [
                [['text' => '𝗩𝗘𝗥𝗜𝗙𝗬']]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];

        $postFields = [
            'chat_id' => $chatId,
            'text' => $message,
            'reply_markup' => json_encode($replyKeyboard),
            'parse_mode' => 'HTML'
        ];

        file_get_contents($apiURL . "sendMessage?" . http_build_query($postFields));
    } else {
        // User has joined all the required channels
        if (isset($users[$chatId]['referpending']) && $users[$chatId]['referpending'] == true) {
            $referrerId = $users[$chatId]['referredBy'][0]; // Get the referrer ID

            // Award 1 coin to the referrer
            $users[$referrerId]['coins'] += 1;
            $users[$chatId]['referpending'] = false; // Set the pending status to false
            saveUsers($jsonFile, $users);

            // Notify the referrer
            $referrerNotification = "𝗡𝗘𝗪 𝗨𝗦𝗘𝗥 𝗥𝗘𝗚𝗜𝗦𝗧𝗘𝗥𝗘𝗗 𝗕𝗬 𝗬𝗢𝗨𝗥 𝗥𝗘𝗙𝗘𝗥 𝗟𝗜𝗡𝗞 🥳\n\n𝟭 𝗖𝗢𝗜𝗡 𝗔𝗗𝗗𝗘𝗗 𝗜𝗡 𝗬𝗢𝗨𝗥 𝗔𝗖𝗖𝗢𝗨𝗡𝗧";
            $postFields = [
                'chat_id' => $referrerId,
                'text' => $referrerNotification,
                'parse_mode' => 'HTML'
            ];

            file_get_contents($apiURL . "sendMessage?" . http_build_query($postFields));
        }

        // Show the main menu to the referred user
        $coins = $users[$chatId]['coins'];

        $message = "𝗛𝗘𝗬 $firstname 👾\n\n𝗪𝗘𝗟𝗖𝗢𝗠𝗘 𝗧𝗢 𝗧𝗛𝗘 𝗠𝗘𝗚𝗔 𝗙𝗢𝗟𝗟𝗢𝗪𝗘𝗥𝗦 𝗕𝗢𝗧 🚀\n\n𝗚𝗘𝗧 𝗙𝗥𝗘𝗘 𝗢𝗙 𝗖𝗢𝗦𝗧 𝗜𝗡𝗦𝗧𝗔𝗚𝗥𝗔𝗠 𝗙𝗢𝗟𝗟𝗢𝗪𝗘𝗥𝗦 𝗜𝗡𝗦𝗧𝗔𝗡𝗧𝗟𝗬 🔰";

        $replyKeyboard = [
            'keyboard' => [
                [['text' => 'Gᴇᴛ Fᴏʟʟᴏᴡᴇʀs']],
                [['text' => 'Bᴀʟᴀɴᴄᴇ 🎯'], ['text' => 'Rᴇғᴇʀᴀʟ 🚀']]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];

        $postFields = [
            'chat_id' => $chatId,
            'text' => $message,
            'reply_markup' => json_encode($replyKeyboard),
            'parse_mode' => 'HTML'
        ];

        file_get_contents($apiURL . "sendMessage?" . http_build_query($postFields));
    }
}

if (isset($update['message']['text']) && $update['message']['text'] == 'Bᴀʟᴀɴᴄᴇ 🎯') {
    $coins = $users[$chatId]['coins'];
    
    $postFields = [
        'chat_id' => $chatId,
        'text' => "✨ 𝗪𝗘𝗟𝗖𝗢𝗠𝗘\n\n👤 𝗨𝘀𝗲𝗿: $firstName\n💼 𝗕𝗮𝗹𝗮𝗻𝗰𝗲: $coins\n❄️ 𝗖𝗛𝗔𝗧 𝗜𝗗 : $chatId\n\n💎 𝗥𝗲𝗳𝗲𝗿 𝗮𝗻𝗱 𝗚𝗲𝘁 𝗥𝗲𝘄𝗮𝗿𝗱𝗲𝗱!",
        'parse_mode' => 'HTML'
    ];
    file_get_contents($apiURL . "sendMessage?" . http_build_query($postFields));
}

if (isset($update['message']['text']) && $update['message']['text'] == 'Rᴇғᴇʀᴀʟ 🚀') {
    $referralLink = "https://t.me/$BotUsername?start=$chatId";

    $postFields = [
        'chat_id' => $chatId,
        'text' => "𝗛𝗲𝘆 $firstName\n👾 𝗪𝗲𝗹𝗰𝗼𝗺𝗲 𝗧𝗼 𝗧𝗵𝗲 𝗥𝗲𝗳𝗲𝗿𝗿𝗮𝗹 𝗭𝗼𝗻𝗲!\n\n🌐 𝗬𝗼𝘂𝗿 𝗨𝗻𝗶𝗾𝘂𝗲 𝗟𝗶𝗻𝗸: $referralLink\n\n💰 𝗘𝗮𝗿𝗻 𝟭 𝗖𝗼𝗶𝗻 𝗳𝗼𝗿 𝗘𝗮𝗰𝗵 𝗥𝗲𝗳𝗲𝗿!",
        'parse_mode' => 'HTML'
    ];
    file_get_contents($apiURL . "sendMessage?" . http_build_query($postFields));
}

// Check if the user sent the "Get Followers" command
if (isset($update['message']['text']) && $update['message']['text'] == 'Gᴇᴛ Fᴏʟʟᴏᴡᴇʀs') {
    $firstname = $update['message']['from']['first_name'];
    $chatId = $update['message']['chat']['id'];

    // Send message with follower options
    $responseMessage = "𝗛𝗘𝗬 $firstname\n\n𝗛𝗢𝗪 𝗠𝗨𝗖𝗛 𝗙𝗢𝗟𝗟𝗢𝗪𝗘𝗥𝗦 𝗬𝗢𝗨 𝗪𝗔𝗡𝗧?";
    $inlineKeyboard = [
        'inline_keyboard' => [
            [['text' => '100', 'callback_data' => 'followers:100'], ['text' => '200', 'callback_data' => 'followers:200'], ['text' => '300', 'callback_data' => 'followers:300']],
            [['text' => '500', 'callback_data' => 'followers:500'], ['text' => '2000', 'callback_data' => 'followers:2000'], ['text' => '5000', 'callback_data' => 'followers:5000']]
        ]
    ];

    $postFields = [
        'chat_id' => $chatId,
        'text' => $responseMessage,
        'reply_markup' => json_encode($inlineKeyboard),
        'parse_mode' => 'HTML'
    ];
    file_get_contents($apiURL . "sendMessage?" . http_build_query($postFields));
}

// Handle inline button clicks for follower selection
if (isset($update['callback_query'])) {
    $callbackData = $update['callback_query']['data'];
    $chatId = $update['callback_query']['from']['id'];
    $firstname = $update['callback_query']['from']['first_name'];

    // Extract follower amount from callback data
    if (strpos($callbackData, 'followers:') !== false) {
        $followers = explode(':', $callbackData)[1];

        // Define coin prices for each follower option
        $prices = [
            '100' => 5,
            '200' => 10,
            '300' => 15,
            '500' => 20,
            '2000' => 50,
            '5000' => 70
        ];

        // Get the coin price based on the selected followers
        $howmuchcoin = $prices[$followers];
        
        // Send billing details message
        $responseMessage = "𝗛𝗲𝘆 $firstname\n\n🈴 𝗕𝗶𝗹𝗹𝗶𝗻𝗴 𝗗𝗲𝘁𝗮𝗶𝗹𝘀\n\n🔰 𝗦𝗼 $followers 𝗙𝗼𝗹𝗹𝗼𝘄𝗲𝗿 𝗬𝗼𝘂 𝗪𝗮𝗻𝘁\n\n🏧 𝗧𝗼𝘁𝗮𝗹 𝗣𝗿𝗶𝗰𝗲 : $howmuchcoin 𝗖𝗼𝗶𝗻𝘀\n\n🌐 𝗖𝗹𝗶𝗰𝗸 𝗣𝗿𝗼𝗰𝗲𝗲𝗱 𝗕𝘂𝘁𝘁𝗼𝗻 𝗶𝗳 𝗬𝗼𝘂 𝗪𝗮𝗻𝘁 𝘁𝗵𝗶𝘀.";
        $inlineKeyboard = [
            'inline_keyboard' => [
                [['text' => '𝗣𝗿𝗼𝗰𝗲𝗲𝗱', 'callback_data' => 'proceed:' . $followers]]
            ]
        ];

        $postFields = [
            'chat_id' => $chatId,
            'text' => $responseMessage,
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ];
        file_get_contents($apiURL . "sendMessage?" . http_build_query($postFields));
    }
}

// Handle "Proceed" button click
if (isset($callbackData) && strpos($callbackData, 'proceed:') !== false) {
    $followers = explode(':', $callbackData)[1];
    $howmuchcoin = $prices[$followers];

    // Load user data from refferdatamega.json
    $refferdatamegaFile = 'refferdatamega.json'; // Ensure this path is correct
    $refferdatamega = json_decode(file_get_contents($refferdatamegaFile), true);
    
    // Check if user exists in the data
    if (isset($refferdatamega[$chatId])) {
        $coins = $refferdatamega[$chatId]['coins'];

        if ($coins < $howmuchcoin) {
            // Insufficient coins message
            $responseMessage = "⚠️ 𝗬𝗼𝘂𝗿 𝗕𝗮𝗹𝗮𝗻𝗰𝗲 𝗶𝘀 𝗟𝗼𝘄 𝗸𝗶𝗻𝗱𝗹𝘆 𝗥𝗲𝗳𝗲𝗿 𝗔𝗻𝗱 𝗘𝗮𝗿𝗻 𝗖𝗼𝗶𝗻𝘀";
        } else {
            // Deduct coins and send success message
            $refferdatamega[$chatId]['coins'] -= $howmuchcoin;

            // Save updated data back to JSON file
            if (file_put_contents($refferdatamegaFile, json_encode($refferdatamega, JSON_PRETTY_PRINT))) {
                $responseMessage = "☑️ 𝗙𝗼𝗹𝗹𝗼𝘄𝗲𝗿𝘀 𝗣𝘂𝗿𝗰𝗵𝗮𝘀𝗲𝗱 𝗦𝘂𝗰𝗰𝗲𝘀𝗳𝘂𝗹𝗹𝘆\n\n♻️ 𝗣𝗹𝗲𝗮𝘀𝗲 𝗦𝗲𝗻𝗱 𝗬𝗼𝘂𝗿 𝗜𝗻𝘀𝘁𝗮𝗴𝗿𝗮𝗺 𝗨𝘀𝗲𝗿𝗻𝗮𝗺𝗲\n\n⚠️ 𝗶𝗳 𝗨𝘀𝗲𝗿𝗻𝗮𝗺𝗲 𝗶𝘀 𝗪𝗿𝗼𝗻𝗴 𝗪𝗲 𝗔𝗿𝗲 𝗡𝗼𝘁 𝗥𝗲𝘀𝗽𝗼𝗻𝘀𝗶𝗯𝗹𝗲";
            } else {
                $responseMessage = "⚠️ 𝗘𝗿𝗿𝗼𝗿: 𝗖𝗼𝘂𝗹𝗱 𝗡𝗼𝘁 𝗨𝗽𝗱𝗮𝘁𝗲 𝗖𝗼𝗶𝗻𝘀.";
            }
        }
    } else {
        // User not found in the data
        $responseMessage = "⚠️ 𝗘𝗿𝗿𝗼𝗿: 𝗨𝘀𝗲𝗿 𝗗𝗮𝘁𝗮 𝗡𝗼𝘁 𝗙𝗼𝘂𝗻𝗱.";
    }

    // Send the response message back to the user
    $postFields = [
        'chat_id' => $chatId,
        'text' => $responseMessage,
        'parse_mode' => 'HTML'
    ];
    file_get_contents($apiURL . "sendMessage?" . http_build_query($postFields));
}

?>