<?php

namespace Buepro\Fromes\Service;

class SessionService
{
    public const SESSION_ACCESS_TOKEN = 'fromes-access-token';

    public function getAccessToken(): string
    {
        if (session_id() === '') {
            session_start();
        }
        if (!isset($_SESSION[self::SESSION_ACCESS_TOKEN]) || $_SESSION[self::SESSION_ACCESS_TOKEN] === '') {
            $_SESSION[self::SESSION_ACCESS_TOKEN] =
                substr(str_shuffle(
                    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$'),
                    0,
                    20
                );
        }
        return $_SESSION[self::SESSION_ACCESS_TOKEN];
    }
}
