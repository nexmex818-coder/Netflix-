<?php
$target_url = 'https://ar.drama-queen.live/category/%d9%85%d8%b3%d9%84%d8%b3%d9%84%d8%a7%d8%aa-%d9%83%d9%88%d8%b1%d9%8a%d8%a9/';

// إعداد لتجاوز أي مشاكل اتصال في السيرفر
$options = [
    "http" => ["header" => "User-Agent: Mozilla/5.0\r\n"],
    "ssl"  => ["verify_peer" => false, "verify_peer_name" => false]
];
$context = stream_context_create($options);
$content = file_get_contents($target_url, false, $context);

preg_match_all('/<article[^>]*>.*?<img[^>]*src="([^"]+)"[^>]*>.*?<h2[^>]*>(.*?)<\/h2>.*?<a[^>]*href="([^"]+)"/is', $content, $matches);

$movies = [];
for ($i = 0; $i < count($matches[0]); $i++) {
    $movies[] = [
        'title'   => trim(strip_tags($matches[2][$i])),
        'poster'  => $matches[1][$i],
        'url'     => $matches[3][$i],
        'quality' => "FHD"
    ];
}

file_put_contents('movies.json', json_encode($movies, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Done! File movies.json updated.";
