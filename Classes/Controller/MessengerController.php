<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Controller;

use Buepro\Fromes\Service\SessionService;
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
        $this->view->assignMultiple([
            'config' => json_encode([
                'accessToken' => (new SessionService())->getAccessToken(),
                'jsonFilter' => $this->getJsonFilterFromSettings(),
            ], JSON_THROW_ON_ERROR),
        ]);
    }

    private function getJsonFilterFromSettings(): array
    {
        $filters = [];
        $includedSubfilters = GeneralUtility::trimExplode(',', $this->settings['filter']['includedSubfilters']);
        foreach ($includedSubfilters as $subfilter) {
            if (
                ($config = $this->settings['subfilters'][$subfilter] ?? false) !== false &&
                ($method = $this->getFilterItemsMethodName($config['items'])) !== null
            ) {
                $filter = $config;
                $filter['items'] = $this->$method($config['items']);
                $filters[] = $filter;
            }
        }
        return $filters;
    }

    private function getFilterItemsMethodName(array $conf): ?string
    {
        $methodName = 'getFilterItemsFrom' . $conf['_typoScriptNodeValue'] ?? '';
        return method_exists($this, $methodName) ? $methodName : null;
    }

    private function getFilterItemsFromTypoScript(array $conf): array
    {
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
