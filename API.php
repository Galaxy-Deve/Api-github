<?php
$text = $_GET['text'];
function makeRandomString($length){
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $out = "";
    for ($i = 1; $i <= $length; $i++) {
        $out .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $out;
}
function downloadRepository($username, $name, $custom = []){
    $url = "https://github.com/{$username}/{$name}";
    $user_agent = [
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.7 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.7',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11) AppleWebKit/601.1.56 (KHTML, like Gecko) Version/9.0 Safari/601.1.56',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.71 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
        'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
        'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
        'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)',
    ];
    
    $options = [
        CURLOPT_RETURNTRANSFER => true, 
        CURLOPT_HEADER         => true, 
        CURLOPT_FOLLOWLOCATION => true,     
        CURLOPT_ENCODING       => "",   
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 120,        
        CURLOPT_TIMEOUT        => 120,       
        CURLOPT_MAXREDIRS      => 10,        
        CURLINFO_HEADER_OUT    => true,
        CURLOPT_SSL_VERIFYPEER => false,     
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_COOKIE         => (array_key_exists('cookies', $custom) ? $custom['cookies'] : null),
        CURLOPT_USERAGENT      => (array_key_exists('user_agent', $custom) ? $custom['user_agent'] : $user_agent[array_rand($user_agent)]),
    ];

    if (array_key_exists('headers', $custom) and is_array($custom['headers'])) {
        $options[CURLOPT_HTTPHEADER] = $custom['headers'];
    }

    $handle = curl_init($url);
    curl_setopt_array($handle, $options);
    $response = curl_exec($handle);
    if ($response === false) {
        $errno = curl_errno($handle);
        $error = curl_error($handle);
        error_log("Curl returned error $errno: $error\n");
        curl_close($handle);
        return false;
    }
    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);
    if ($http_code >= 500) {
        sleep(10);
        return false;
    } elseif ($http_code != 200) {
        return false;
    } else {
        if (preg_match('/(\/' . $username . '\/' . $name . '\/archive\/refs\/heads\/(\w+)\.zip)/i', $response, $matches)) {
            $repo_file_addr = "https://github.com" . $matches[1];
            $fname = makeRandomString(rand(15, 25));

        preg_match('/(\<strong\>\s*(([+-]?([0-9]*[.])?[0-9]+)[kmbt]?)\s*\<\/strong\>\s*stars)/i', $response, $stars);
        preg_match('/(\<strong\>\s*(([+-]?([0-9]*[.])?[0-9]+)[kmbt]?)\s*\<\/strong\>\s*watching)/i', $response, $watchs);
        preg_match('/(\<strong\>\s*(([+-]?([0-9]*[.])?[0-9]+)[kmbt]?)\s*\<\/strong\>\s*forks)/i', $response, $forks);
        preg_match('/(\s*\<title\>.+\/.+\:\s*(.+)\s*\<\/title\>)/i', $response, $title);
        preg_match('/(\s*\<Topics\>.+\/.+\:\s*(.+)\s*\<\/Topics\>)/i', $response,$Topics);
        return [
                "user" => "https://github.com/{$username}/",
                "link" => $url,
                "file_name" => "{$username}[{$name}] - {$matches[2]}.zip",
                "title" => $title[2],
                "stars"  => (int) $stars[2],
                "watchs" => (int) $watchs[2],
                "forks"  => (int) $forks[2],
                "Topics" =>$Topics[2],
            ];
        }
    }
    return false;
}


    if(preg_match('/^(?:http(?:s)?\:\/\/)?github\.com\/([\w-]{2,40})\/([\w-]{2,40})(?:(?:\.git)?\/?)?$/i', $text, $matches)) {
        
    $repo = downloadRepository($matches[1], $matches[2]);
    $lisk = str_replace("https://", "", $repo['link']);

$msg = "<b>✨ A new repository was sent 🎉</b>
<b>🏷 Title:</b> <i>{$repo['title']}</i>

<b>👤 From:</b> <a href=\"tg://user?id={$from_id}\">" . htmlspecialchars($first_name) . "</a> (<a href=\"{$repo['user']}\">Github</a>)
<b>🔗 Link:</b> $lisk
<b>⭐️ Star(s):</b> <code>{$repo['stars']}</code>
<b>👁‍🗨 Watch(s):</b> <code>{$repo['watchs']}</code>
<b>🌐 Fork(s):</b> <code>{$repo['forks']}</code>
";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{#}/sendMessage");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('chat_id' => '-1001598459642', 'text' => $msg,"parse_mode" =>'html','disable_web_page_preview' => true,)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

}
?> 
