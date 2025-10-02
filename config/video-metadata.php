<?php

declare(strict_types=1);

return [
    'youtube' => [
        'oembed_endpoint' => 'https://www.youtube.com/oembed',
    ],

    'http' => [
        'user_agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'accept_language' => 'en-US,en;q=0.8',
        'connect_timeout' => 10,
        'timeout' => 15,
        'max_redirects' => 5,
    ],
];
