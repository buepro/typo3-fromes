<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Controller;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * EventController
 */
class MessengerController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * action panel
     */
    public function panelAction(): void
    {
        $this->view->assignMultiple(['jsonFilters' => $this->getJsonFiltersFromSettings()]);
    }

    private function getJsonFiltersFromSettings(): string
    {
        $filters = [];
        $languageService = GeneralUtility::makeInstance(LanguageService::class);
        foreach($this->settings['filters'] as $id => $conf) {
            if (($method = $this->getFilterItemsMethodName($conf['items'])) !== null) {
                $filters[$id] = $conf;
                $filters[$id]['title'] = $languageService->sL($filters[$id]['title']);
                $filters[$id]['items'] = $this->$method($conf['items']);
            }
        }
        return json_encode($filters);
    }

    private function getFilterItemsMethodName(array $conf): ?string
    {
        $methodName = 'getFilterItemsFrom' . ucfirst(strtolower($conf['source']));
        return method_exists($this, $methodName) ? $methodName : null;
    }

    private function getFilterItemsFromTs(array $conf): array
    {
        $conf = $conf['ts'];
        $cObjRenderer = new ContentObjectRenderer();
        $items = $cObjRenderer->getRecords($conf['table'], $conf['select']);
        return array_map(function ($item) use ($conf) {
            $filtered = [];
            foreach($conf['fieldMap'] as $key => $field) {
                if (isset($item[$field])) {
                    $filtered[$key] = $item[$field];
                }
            }
            return $filtered;
        }, $items);
    }
}
