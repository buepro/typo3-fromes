<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Frontend messenger',
    'description' => 'Provides a plugin to send emails to frontend users. Recipients can be compiled by help of a flexible filter.',
    'category' => 'plugin',
    'author' => 'Roman Büchler',
    'author_email' => 'rb@buechler.pro',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'php'   => '>=7.3.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Buepro\\Fromes\\' => 'Classes'
        ],
    ],
];
