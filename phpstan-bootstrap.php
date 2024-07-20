<?php

if (!defined('PHPSTAN_RUNNING')) {
    define('PHPSTAN_RUNNING', true);
}

function replaceAppPath(string $file): string {
    return str_replace('/app/', 'var/www/', $file);
}