<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3') or die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Fromes',
    'Messenger',
    'LLL:EXT:fromes/Resources/Private/Language/locallang_db.xlf:tx_fromes_messenger.name',
    'fromes-plugin-messenger'
);
