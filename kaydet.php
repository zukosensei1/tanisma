<?php
// Hata raporlamayı açalım (Sorunu görmek için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Telegram Bot Bilgileri
$botToken = '8761753927:AAFrVMhziZNflfozhQA6d1V1INQn7_iBi7A';
$chatId   = '6671499665';

function telegramMesaj($text) {
    global $botToken, $chatId;
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text'    => $text,
        'parse_mode' => 'HTML'
    ];

    // Önce cURL ile deneyelim
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL hatasını önlemek için
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $cevap = curl_exec($ch);
        $hata = curl_error($ch);
        curl_close($ch);
        
        if ($cevap !== false) {
            return $cevap;
        }
    }

    // cURL çalışmazsa file_get_contents ile deneyelim
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            'timeout' => 15
        ]
    ];
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        return "Telegram API'sine bağlanılamadı. Sunucunuz dış bağlantıları engelliyor olabilir.";
    }
    return $result;
}

$tip = $_POST['tip'] ?? '';

if ($tip === 'evet') {
    $mesaj = "💖 <b>Evet dedi!</b>\nKullanıcı tanışmayı kabul etti.";
    $sonuc = telegramMesaj($mesaj);
    echo "ok";
    exit;
}

if ($tip === 'form') {
    $isim     = htmlspecialchars($_POST['isim'] ?? '');
    $hobiler  = htmlspecialchars($_POST['hobiler'] ?? '');
    $renk     = htmlspecialchars($_POST['renkler'] ?? '');
    $kitap    = htmlspecialchars($_POST['kitaplar'] ?? '');
    $film     = htmlspecialchars($_POST['filmler'] ?? '');

    $mesaj = "📋 <b>Yeni Form Bilgileri</b>\n\n";
    $mesaj .= "👤 <b>Ad:</b> {$isim}\n";
    $mesaj .= "🎨 <b>Hobiler:</b> {$hobiler}\n";
    if ($renk)  $mesaj .= "🌈 <b>Sevdiği renk:</b> {$renk}\n";
    if ($kitap) $mesaj .= "📚 <b>Sevdiği kitap:</b> {$kitap}\n";
    if ($film)  $mesaj .= "🎬 <b>Sevdiği film:</b> {$film}\n";

    $sonuc = telegramMesaj($mesaj);
    
    // Telegram'dan gelen cevabı kontrol et
    $json = json_decode($sonuc, true);
    if (isset($json['ok']) && $json['ok'] === true) {
        echo "ok";
    } else {
        // Hata varsa ekrana bas
        echo "Telegram Hatası: " . $sonuc;
    }
    exit;
}

echo "Geçersiz istek.";