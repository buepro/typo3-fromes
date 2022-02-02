<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3') || die('Access denied.');

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Fromes',
        'Messenger',
        [
            \Buepro\Fromes\Controller\MessengerController::class => 'panel,filter,mail',
        ],
        [
            \Buepro\Fromes\Controller\MessengerController::class => 'filter,mail',
        ]
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    messenger {
                        iconIdentifier = fromes-plugin-messenger
                        title = LLL:EXT:fromes/Resources/Private/Language/locallang_db.xlf:tx_fromes_messenger.name
                        description = LLL:EXT:fromes/Resources/Private/Language/locallang_db.xlf:tx_fromes_messenger.description
                        tt_content_defValues {
                            CType = list
                            list_type = fromes_messenger
                        }
                    }
                }
                show = *
            }
       }'
    );

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $icons = [
        'fromes-plugin-messenger',
    ];
    foreach ($icons as $icon) {
        $iconRegistry->registerIcon(
            $icon,
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:fromes/Resources/Public/Icons/' . $icon . '.svg']
        );
    }
})();
