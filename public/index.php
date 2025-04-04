<?php
include 'db.php';
include 'Telegram.php';

$telegram = new Telegram("7740557476:AAF_6aMOlEOzMFYJequvkxjM7MVLi0Dkmqc");

$chat_id = $telegram->ChatID();
$name = $telegram->FirstName();
$text = $telegram->Text();
$result = $telegram->getData();

$databasePath = __DIR__ . '/../data.sqlite';

$db;

try {
    // Connect to the SQLite database
    $db = new PDO("sqlite:$databasePath");

    // Set PDO to throw exceptions on errors
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$validCommand = false;

if ($text == "/start") {
    $validCommand = true;

    $new = new DB($db);

    $exists = $new->getUser($chat_id);
    if (!isset($exists->chat_id)) {
        $new->createUser($name, $chat_id);
        $new->createWallet($new->getUser($chat_id)->id, time());
    } else {
        $new->updateUser($name, $chat_id);
    }

    // $new->deleteUser($chat_id);
    // $new->deleteWallet();

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton("Start", '', '/home')),
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'text' => 'Welcome ! Lets start the bot', 'reply_markup' => $keyb);
    $telegram->sendMessage($content);
}

if ($text == "/home") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton("🌐 اکانت من", '', '/user_ac'), $telegram->buildInlineKeyBoardButton("💵 کیف پول", '', '/user_wallet')),
        //Second row
        array($telegram->buildInlineKeyBoardButton("💲 خرید وی پی ان", '', '/buy_sub'), $telegram->buildInlineKeyBoardButton("💰 کسب درآمد", '', '/refferal')),
        //Third row
        array($telegram->buildInlineKeyBoardButton('💠 پشتیبانی', 'https://t.me//NayaVPN_Support', ''))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => 'سلام , به بات فروش نایا وی پی ان خوش اومدی ! چه کاری میتونم برات انجام بدم؟', 'message_id' => $result['callback_query']['message']['message_id']);
    $telegram->editMessageText($content);
}

if ($text == "/user_ac") {
    $validCommand = true;

    $new = new DB($db);
    $user = $new->getUser($chat_id);

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton("💵 کیف پول", '', '/user_wallet')),
        //Second row
        array($telegram->buildInlineKeyBoardButton("💰 کسب درآمد", '', '/refferal')),
        //Third row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/home'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => '👤 نام کاربری : ' . $user->username . "\n" . '▫️ ایدی کاربری : ' . $user->chat_id . "\n" . '✳️ تعداد رفرال : ' . '0'
        . "\n\n" . '0' . 'گزینه های بیشتر 👇', 'message_id' => $result['callback_query']['message']['message_id']);
    $telegram->editMessageText($content);
}

if ($text == "/user_wallet") {
    $validCommand = true;

    $new = new DB($db);
    $user = $new->getUser($chat_id);

    $wallet = $new->getWallet($user->id);

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💰 افزایش موجودی', '', '/deposit')),
        //second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/home'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => 'کیف پول شما 💸' . "\n\n" . '💳 موجودی فعلی : ' . $wallet->balance . "\n" . '📥 اخرین واریز شما : ' . $wallet->last_depo
        . "\n\n" . 'گزینه های بیشتر 👇', 'message_id' => $result['callback_query']['message']['message_id']);
    $telegram->editMessageText($content);
}

if ($text == "/refferal") {
    $validCommand = true;

    $new = new DB($db);
    $user = $new->getUser($chat_id);

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/home'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => 'طرح کسب درآمد نایا وی پی ان 🤑' . "\n\n" . 'شما در نایا وی پی ان میتوانید با دعوت از دوستان خود , از خرید آنها به اعتبار حساب خود بیافزایید. لینک رفرال شما و تعداد رفرال های شما به صورت زیر است:'
        . "\n\n" . '👥 رفرال های شما :'. $user->refs . "\n" . '📡 لینک رفرال شما : ' . "https://t.me/nayavpn_shopbot?start=$user->ref_id" . "\n\n", 'message_id' => $result['callback_query']['message']['message_id']);
    $telegram->editMessageText($content);
}

if ($text == "/buy_sub") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('5GB , 1 MONTH', '', '/5G1M'), $telegram->buildInlineKeyBoardButton('10GB , 1 MONTH', '', '/10G1M')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('25GB , 1 MONTH', '', '/25G1M'), $telegram->buildInlineKeyBoardButton('50GB , 1 MONTH', '', '/50G1M')),
        //Third row
        array($telegram->buildInlineKeyBoardButton('60GB , 2 MONTH', '', '/60G2M'), $telegram->buildInlineKeyBoardButton('100GB , 2 MONTH', '', '/100G2M')),
        //Fourth row
        array($telegram->buildInlineKeyBoardButton('120GB , 3 MONTH', '', '/120G3M'), $telegram->buildInlineKeyBoardButton('200GB , 3 MONTH', '', '/200G3M')),
        //Fifth row
        array($telegram->buildInlineKeyBoardButton('🚀 پلن سفارشی', '', '/custom_sub')),
        //Sixth row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/home'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => 'خرید کانفیگ جدید 💵' . "\n\n" . 'پلن های زیر , پلن های پیشنهادی نایا وی پی ان هستن. شما همچنین میتونید پلن دلخواه خودتون رو از طریق دکمه پلن سفارشی بسازید!'
        . "\n\n" . 'انتخاب پلن 👇', 'message_id' => $result['callback_query']['message']['message_id']);
    $telegram->editMessageText($content);
}

if (!$validCommand) {
    $content = array('chat_id' => $chat_id, 'text' => 'Invalid Command');
    $telegram->sendMessage($content);
}
