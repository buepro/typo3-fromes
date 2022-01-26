<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class FilterConfigurationService
{
    /** @var array  */
    protected $settings = [];

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function getJsonFilter(): array
    {
        $filters = [];
        $subfilters = GeneralUtility::trimExplode(',', $this->settings['filter']['includedSubfilters']);
        foreach ($subfilters as $subfilter) {
            if (
                ($config = $this->settings['subfilters'][$subfilter] ?? false) !== false &&
                ($method = $this->getFilterItemsMethodName($config['items'])) !== null
            ) {
                $filters[] = [
                    'id' => $config['id'],
                    'items' => $this->$method($config['items']),
                ];
            }
        }
        return $filters;
    }

    protected function getFilterItemsMethodName(array $conf): ?string
    {
        $methodName = 'getFilterItemsFrom' . $conf['_typoScriptNodeValue'] ?? '';
        return method_exists($this, $methodName) ? $methodName : null;
    }

    protected function getFilterItemsFromTypoScript(array $conf): array
    {
        $cObjRenderer = new ContentObjectRenderer();
        $items = $cObjRenderer->getRecords($conf['table'], $conf['select']);
        return array_map(function ($item) use ($conf): array {
            $filtered = [];
            foreach ($conf['fieldMap'] as $key => $field) {
                if (isset($item[$field])) {
                    $filtered[$key] = $item[$field];
                }
            }
            return $filtered;
        }, $items);
    }
}
