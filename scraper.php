<?php
$target_url = 'https://ar.drama-queen.live/category/%d9%85%d8%b3%d9%84%d8%b3%d9%84%d8%a7%d8%aa-%d9%83%d9%88%d8%b1%d9%8ي%d8%a9/';

$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36\r\n" .
                    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8\r\n" .
                    "Accept-Language: en-US,en;q=0.5\r\n"
    ],
    "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
];

$context = stream_context_create($options);
$content = file_get_contents($target_url, false, $context);

if (!$content) {
    echo "❌ فشل جلب المحتوى من الموقع.";
    exit;
}

// Regex شامل جداً لجلب الرابط، الصورة، والعنوان
preg_match_all('/<a href="([^"]+)"[^>]*>.*?<img[^>]*src="([^"]+)"[^>]*alt="([^"]+)"/is', $content, $matches);

$movies = [];
for ($i = 0; $i < count($matches[0]); $i++) {
    // تصفية النتائج لضمان أنها مسلسلات وليست روابط جانبية
    if (strpos($matches[1][$i], '/drama/') !== false || strpos($matches[1][$i], '/series/') !== false) {
        $movies[] = [
            'title'   => trim($matches[3][$i]),
            'poster'  => $matches[2][$i],
            'url'     => $matches[1][$i],
            'quality' => "FHD"
        ];
    }
}

file_put_contents('movies.json', json_encode($movies, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "✅ تم العثور على: " . count($movies) . " مسلسل.";
