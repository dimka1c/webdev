<?php

function generatePassword($length = 16)
{
    $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
    $numChars = strlen($chars);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($chars, rand(1, $numChars) - 1, 1);
    }
    return $string;
}

function createCsrf()
{
    $csrf = generatePassword(32);
    $_SESSION['_csrf'] =  $csrf;
    return $csrf;
}