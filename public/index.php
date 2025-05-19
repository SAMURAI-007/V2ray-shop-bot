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
        $new->updateUser($name, $chat_id, 0, null);
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
        array($telegram->buildInlineKeyBoardButton('🚀 کانفیگ های من', '', '/user_subs')),
        //Second row
        array($telegram->buildInlineKeyBoardButton("💵 کیف پول", '', '/user_wallet')),
        //Third row
        array($telegram->buildInlineKeyBoardButton("💰 کسب درآمد", '', '/refferal')),
        //Fourth row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/home'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => '👤 نام کاربری : ' . $user->username . "\n" . '▫️ ایدی کاربری : ' . $user->chat_id . "\n" . '✳️ تعداد رفرال : ' . '0'
        . "\n\n" . 'گزینه های بیشتر 👇', 'message_id' => $result['callback_query']['message']['message_id']);
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

if ($text == "/deposit") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 20000 تومان', '', '/deposited')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('💵 50000 تومان', '', '/deposited')),
        //Third row
        array($telegram->buildInlineKeyBoardButton('💵 100000 تومان', '', '/deposited')),
        //Fourth row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/user_wallet'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => 'افزایش موجودی کیف پول 💰' . "\n\n" . 'لطفا مبلغ مورد نظر را انتخاب کنید.' . "\n\n" . 'گزینه های بیشتر 👇', 'message_id' => $result['callback_query']['message']['message_id']);
    $telegram->editMessageText($content);
}

if ($text == "/deposited") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💠 پشتیبانی', 'https://t.me//NayaVPN_Support', '')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/user_wallet'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => 'افزایش موجودی کیف پول 💰' . "\n\n" . 'برای افزایش موجودی , مبلغ را به شماره کارت زیر واریز کرده و رسید پرداختی را برای پشتیبانی ارسال کنید.' . "\n\n" . '6219 8619 3263 9601', 'message_id' => $result['callback_query']['message']['message_id']);
    $telegram->editMessageText($content);
}


if ($text == "/refferal") {
    $validCommand = true;

    $new = new DB($db);
    $user = $new->getUser($chat_id);

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/home')),
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => 'طرح کسب درآمد نایا وی پی ان 🤑' . "\n\n" . 'شما در نایا وی پی ان میتوانید با دعوت از دوستان خود , از خرید آنها به اعتبار حساب خود بیافزایید. لینک رفرال شما و تعداد رفرال های شما به صورت زیر است:'
        . "\n\n" . '👥 رفرال های شما :' . $user->refs . "\n" . '📡 لینک رفرال شما : ' . "https://t.me/nayavpn_shopbot?start=$user->ref_id" . "\n\n", 'message_id' => $result['callback_query']['message']['message_id']);
    $telegram->editMessageText($content);
}

if ($text == "/user_subs") {
    $validCommand = true;

    $new = new DB($db);
    $user = $new->getUser($chat_id);

    $subs = $new->getSub($user->id);

    if (isset($subs)) {
        foreach ($subs as $sub) {
            $content .= '💳 پلن شما : ' . $sub->sub_url . "\n\n" . '📅 تاریخ انقضا : ' . date('Y-m-d H:i:s', $sub->exp_date) . "\n" . 'حجم :' . $sub->data_limit . "\n\n" . "";
        }
    } else {
        $content = 'شما هیچ کانفیگی ندارید.';
    }

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/home'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);

    $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => 'کانفیگ های من 📡' . "\n\n" . "$content", 'message_id' => $result['callback_query']['message']['message_id']);
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


if ($text == "/5G1M") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 خرید', '', '/buy_confirm_5g1m')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => 'خرید پلن 5 گیگابایت 1 ماهه' . "\n\n" . 'قیمت : 10000 تومان' . "\n\n" . 'هزینه خرید از اعتبار شما کسر خواهد شد. در صورت کمبود اعتبار , از منوی کیف پول اقدام به افزایش اعتبار کنید.',
        'message_id' => $result['callback_query']['message']['message_id']
    );
    $telegram->editMessageText($content);
}
if ($text == "/10G1M") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 خرید', '', '/buy_confirm_10g1m')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => 'خرید پلن 10 گیگابایت 1 ماهه' . "\n\n" . 'قیمت : 20000 تومان' . "\n\n" . 'هزینه خرید از اعتبار شما کسر خواهد شد. در صورت کمبود اعتبار , از منوی کیف پول اقدام به افزایش اعتبار کنید.',
        'message_id' => $result['callback_query']['message']['message_id']
    );
    $telegram->editMessageText($content);
}
if ($text == "/25G1M") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 خرید', '', '/buy_confirm_25g1m')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => 'خرید پلن 25 گیگابایت 1 ماهه' . "\n\n" . 'قیمت : 50000 تومان' . "\n\n" . 'هزینه خرید از اعتبار شما کسر خواهد شد. در صورت کمبود اعتبار , از منوی کیف پول اقدام به افزایش اعتبار کنید.',
        'message_id' => $result['callback_query']['message']['message_id']
    );
    $telegram->editMessageText($content);
}
if ($text == "/50G1M") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 خرید', '', '/buy_confirm_50g1m')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => 'خرید پلن 50 گیگابایت 1 ماهه' . "\n\n" . 'قیمت : 80000 تومان' . "\n\n" . 'هزینه خرید از اعتبار شما کسر خواهد شد. در صورت کمبود اعتبار , از منوی کیف پول اقدام به افزایش اعتبار کنید.',
        'message_id' => $result['callback_query']['message']['message_id']
    );
    $telegram->editMessageText($content);
}
if ($text == "/60G2M") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 خرید', '', '/buy_confirm_60g2m')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => 'خرید پلن 60 گیگابایت 2 ماهه' . "\n\n" . 'قیمت : 110000 تومان' . "\n\n" . 'هزینه خرید از اعتبار شما کسر خواهد شد. در صورت کمبود اعتبار , از منوی کیف پول اقدام به افزایش اعتبار کنید.',
        'message_id' => $result['callback_query']['message']['message_id']
    );
    $telegram->editMessageText($content);
}
if ($text == "/100G2M") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 خرید', '', '/buy_confirm_100g2m')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => 'خرید پلن 100 گیگابایت 2 ماهه' . "\n\n" . 'قیمت : 150000 تومان' . "\n\n" . 'هزینه خرید از اعتبار شما کسر خواهد شد. در صورت کمبود اعتبار , از منوی کیف پول اقدام به افزایش اعتبار کنید.',
        'message_id' => $result['callback_query']['message']['message_id']
    );
    $telegram->editMessageText($content);
}
if ($text == "/120G3M") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 خرید', '', '/buy_confirm_120g3m')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => 'خرید پلن 120 گیگابایت 3 ماهه' . "\n\n" . 'قیمت : 180000 تومان' . "\n\n" . 'هزینه خرید از اعتبار شما کسر خواهد شد. در صورت کمبود اعتبار , از منوی کیف پول اقدام به افزایش اعتبار کنید.',
        'message_id' => $result['callback_query']['message']['message_id']
    );
    $telegram->editMessageText($content);
}
if ($text == "/200G3M") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 خرید', '', '/buy_confirm_200g3m')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => 'خرید پلن 200 گیگابایت 3 ماهه' . "\n\n" . 'قیمت : 220000 تومان' . "\n\n" . 'هزینه خرید از اعتبار شما کسر خواهد شد. در صورت کمبود اعتبار , از منوی کیف پول اقدام به افزایش اعتبار کنید.',
        'message_id' => $result['callback_query']['message']['message_id']
    );
    $telegram->editMessageText($content);
}


if ($text == "/custom_sub") {
    $validCommand = true;

    $option = array(
        //First row
        array($telegram->buildInlineKeyBoardButton('💵 خرید', '', '/buy_custom')),
        //Second row
        array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
    );
    $keyb = $telegram->buildInlineKeyBoard($option);
    $content = array(
        'chat_id' => $chat_id,
        'reply_markup' => $keyb,
        'text' => 'خرید پلن سفارشی' . "\n\n" . 'شما میتوانید پلن دلخواه خود را با توجه به نیاز خود بسازید. برای این کار , از دکمه خرید استفاده کنید.' . "\n\n" . 'انتخاب پلن 👇',
        'message_id' => $result['callback_query']['message']['message_id']
    );
    $telegram->editMessageText($content);
}

if (!$validCommand) {

    if (str_contains($text, "/buy_confirm_")) {
        $validCommand = true;
        preg_match('/\/buy_confirm_(\d+)g(\d+)m/', $text, $matches);
        $g = intval($matches[1]);
        $m = intval($matches[2]);

        $e = $m * time() + 60 * 60 * 24 * 30;
        $new = new DB($db);
        $user = $new->getUser($chat_id);
        $wallet = $new->getWallet($user->id);
        $price = 2000 * $g + $m * 2000; // Assuming 10000 Toman for 5GB
        if ($wallet->balance >= $price) {

            $new->updateWallet($user->id, $wallet->balance - $price, time(), $price);

            $new->createSub($user->id, $user->username, $g, $e);


            $option = array(
                //First row
                array($telegram->buildInlineKeyBoardButton('🚀 کانفیگ های من ', '', '/user_subs')),
                //Second row
                array($telegram->buildInlineKeyBoardButton('🔙 بازگشت', '', '/buy_sub'))
            );
            $keyb = $telegram->buildInlineKeyBoard($option);
            $content = array(
                'chat_id' => $chat_id,
                'reply_markup' => $keyb,
                'text' => 'خرید شما با موفقیت انجام شد.' . "\n" . 'مبلغ : ' . $price . ' تومان' . "\n" . 'پلن : ' . "$g گیگابایت , " . "$m ماهه" . "\n" . 'تاریخ انقضا : ' . date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30 * $m),
                'message_id' => $result['callback_query']['message']['message_id']
            );
            $telegram->editMessageText($content);
        } else {
            $content = array('chat_id' => $chat_id, 'text' => 'موجودی کیف پول شما کافی نیست.' . "\n" . 'لطفا از منوی کیف پول اقدام به افزایش اعتبار کنید.', 'message_id' => $result['callback_query']['message']['message_id']);
            $telegram->editMessageText($content);
        }
    }

    if (str_contains($text, "/start")) {
        if (preg_match('/^\/start\s+(\w+)/', $text, $matches)) {
            $validCommand = true;
            $ref_id = $matches[1];

            $new = new DB($db);
            $ref_user = $new->getUserByRefId($ref_id);
            $user = $new->getUser($chat_id);
            if ($ref_user) {
                if ($user->$is_ref == null) {
                    $new->updateUser($user->username, $chat_id,0,$ref_user->id);
                }
                $content = array(
                    'chat_id' => $chat_id,
                    'text' => "کاربر معرف پیدا شد",
                    'message_id' => $result['callback_query']['message']['message_id']
                );
                $telegram->sendMessage($content);
            } else {
                $content = array(
                    'chat_id' => $chat_id,
                    'text' => "کاربر معرف یافت نشد.",
                    'message_id' => $result['callback_query']['message']['message_id']
                );
                $telegram->sendMessage($content);
            }
        }
    } else {
        $content = array('chat_id' => $chat_id, 'text' => 'Invalid Command');
        $telegram->sendMessage($content);
    }
}
