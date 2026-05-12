<?php
// سنستخدم موقعاً بديلاً أسهل في القشط حالياً للتجربة
$target_url = 'https://ww.asia2tv.top/category/korean-drama/';

$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ],
    "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
];

$context = stream_context_create($options);
$content = file_get_contents($target_url, false, $context);

if (!$content) {
    die("❌ فشل الاتصال بالموقع البديل");
}

// Regex جديد يتناسب مع بنية المواقع المشهورة
preg_match_all('/<a href="([^"]+)"[^>]*title="([^"]+)"[^>]*>.*?<img[^>]*src="([^"]+)"/is', $content, $matches);

$movies = [];
// سنأخذ أول 12 مسلسل فقط للتجربة
for ($i = 0; $i < min(12, count($matches[0])); $i++) {
    $movies[] = [
        'title'   => trim($matches[2][$i]),
        'poster'  => $matches[3][$i],
        'url'     => $matches[1][$i],
        'quality' => "WEB-DL"
    ];
}

// إذا فشل الـ Regex الأول، سنجرب واحداً أبسط
if (empty($movies)) {
    preg_match_all('/<img[^>]*src="([^"]+)"[^>]*alt="([^"]+)"/is', $content, $img_matches);
    preg_match_all('/<a[^>]*href="([^"]+)"[^>]*class="post-link"/is', $content, $link_matches);
    
    for ($i = 0; $i < min(10, count($img_matches[1])); $i++) {
        $movies[] = [
            'title'   => $img_matches[2][$i] ?? "مسلسل كوري",
            'poster'  => $img_matches[1][$i],
            'url'     => $link_matches[1][$i] ?? "#",
            'quality' => "HD"
        ];
    }
}

file_put_contents('movies.json', json_encode($movies, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "✅ تم العثور على: " . count($movies) . " عمل درامي.";
