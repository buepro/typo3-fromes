<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Service;

use TYPO3\CMS\Core\TypoScript\TypoScriptService;
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

    public function getSubfilterConfig(): array
    {
        if (
            isset($this->settings['items'], $this->settings['componentId']) && $this->settings['componentId'] !== '' &&
            ($method = $this->getFilterItemsMethodName($this->settings['items'])) !== null
        ) {
            return [
                'id' => $this->settings['componentId'],
                'items' => $this->$method($this->settings['items']),
            ];
        }
        return [];
    }

    protected function getFilterItemsMethodName(array $conf): ?string
    {
        $methodName = 'getFilterItemsFrom' . $conf['_typoScriptNodeValue'] ?? '';
        return method_exists($this, $methodName) ? $methodName : null;
    }

    protected function getFilterItemsFromTypoScript(array $conf): array
    {
        $cObj = new ContentObjectRenderer();
        $tsService = GeneralUtility::makeInstance(TypoScriptService::class);
        $tsConf = $tsService->convertPlainArrayToTypoScriptArray($conf);
        $items = $cObj->getRecords($conf['table'], $tsConf['select.']);
        return array_map(static function ($item) use ($cObj, $conf, $tsConf): array {
            $filtered = [];
            $cObj->data = $item;
            foreach ($conf['fieldMap'] as $key => $field) {
                $value = is_array($field) ? '' : (string)$item[$field];
                $keyDot = $key . '.';
                if (isset($tsConf['fieldMap.'][$keyDot])) {
                    $value = $cObj->stdWrap($tsConf['fieldMap.'][$key] ?? '', $tsConf['fieldMap.'][$keyDot]);
                }
                $filtered[$key] = $value;
            }
            return $filtered;
        }, $items);
    }

    protected function getFilterItemsFromTCAConfigItems(array $conf): array
    {
        if (
            ($table = $conf['table'] ?? false) !== false &&
            ($column = $conf['column'] ?? false) !== false &&
            ($items = $GLOBALS['TCA'][$table]['columns'][$column]['config']['items'] ?? false) !== false
        ) {
            $result = [];
            foreach ($items as $item) {
                if (isset($item[0], $item[1])) {
                    $result[] = [
                        'id' => $item[1],
                        'label' => $GLOBALS['TSFE']->sL($item[0])
                    ];
                }
            }
            return $result;
        }
        return [];
    }
}
