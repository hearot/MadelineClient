<?php
if (!function_exists('readline')) {
    function readline($prompt = null)
    {
        if ($prompt) {
            echo $prompt;
        }
        $fp = fopen('php://stdin', 'r');
        $line = rtrim(fgets($fp, 1024));
        return $line;
    }
}
function latinreadline($message)
{
    do {
        if (isset($res)) {
            echo 'Only latin letters, numbers, dashes (-) and underscores (_) can be used!' . PHP_EOL;
        }
        $res = readline($message);
    } while (!preg_match('/^[A-Za-z0-9_.-]+$/', $res));
    return $res;
}
function platinreadline($message)
{
    do {
        if (isset($res)) {
            echo 'A project with this name already exists!' . PHP_EOL;
        }
        $res = latinreadline($message);
    } while (file_exists($res));
    return $res;
}

function emailreadline($message)
{
    do {
        if (isset($res)) {
            echo 'This is not a valid email!' . PHP_EOL;
        }
        $res = readline($message);
    } while (!filter_var($res, FILTER_VALIDATE_EMAIL));

    return $res;
}
