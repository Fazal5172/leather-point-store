<?php

/**
 * Application Configuration
 * Change APP_URL when deploying to another server.
 */

$host = $_SERVER['HTTP_HOST'];

if (
    $host === 'localhost' ||
    $host === '127.0.0.1'
) {

    define(
        'APP_URL',
        'http://localhost/leather-store-final'
    );

} else {

    define(
        'APP_URL',
        'https://leatherstore.lovestoblog.com/'
    );

}