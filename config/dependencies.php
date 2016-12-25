<?php

$c = [];
$c['morphy'] = function() {
    // set some options
    $opts = [
        'storage' => PHPMORPHY_STORAGE_FILE,
        'predict_by_suffix' => true,
        'predict_by_db' => true,
    ];

    $dir = __DIR__ . '/../var/dicts/ru_RU';
    $lang = 'ru_RU';

    return new phpMorphy($dir, $lang, $opts);
};

return $c;
