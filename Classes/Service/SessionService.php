<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

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
                substr(
                    str_shuffle(
                    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$'
                ),
                    0,
                    20
                );
        }
        return $_SESSION[self::SESSION_ACCESS_TOKEN];
    }
}
