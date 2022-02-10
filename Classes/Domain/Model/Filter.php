<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Domain\Model;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Filter implements FilterInterface
{
    /** @var array */
    protected $status = [];
    /** @var array  */
    protected $settings = [];
    /** @var QueryBuilder[] */
    protected $queryBuilders = [];

    /**
     * @param array $settings Contains the keys `filter` and `subfilters`
     * @param array $status Status from the filter with subfilters
     */
    public function __construct(array $settings, array $status = [])
    {
        $this->settings = $settings;
        $this->status = $status;
    }

    /**
     * @inheritDoc
     */
    public function getConfigForWebComponent(): array
    {
        $subfilterConfig = [];
        $subfilters = GeneralUtility::trimExplode(',', $this->settings['filter']['includedSubfilters']);
        foreach ($subfilters as $subfilter) {
            if (
                ($config = $this->settings['subfilters'][$subfilter] ?? false) !== false &&
                class_exists($className = $config['class'])
            ) {
                $subfilterConfig[] = (new $className($this, $config))->getConfigForWebComponent();
            }
        }
        return [
            'subfilters' => $subfilterConfig,
        ];
    }

    /**
     * @inheritDoc
     */
    public function setupQueryBuilders(QueryBuilder $queryBuilder): FilterInterface
    {
        $this->queryBuilders['default'] = $queryBuilder;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQueryBuilder(string $name): ?QueryBuilder
    {
        return $this->queryBuilders[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function modifyQueryBuilders(): array
    {
        foreach ($this->status as $subfilterStatus) {
            $settings = $this->getSubfilterSettingsFromId($subfilterStatus->id);
            if (
                class_exists($subfilterClass = $settings['class'] ?? '') &&
                ($subfilter = new $subfilterClass($this, $settings, $subfilterStatus->value)) instanceof SubfilterInterface
            ) {
                $subfilter->modifyQueryBuilders();
            }
        }
        // Prevent showing all users when no filter criteria is set
        foreach ($this->queryBuilders as $queryBuilder) {
            if ($queryBuilder->getQueryPart('where') !== null) {
                return array_values($this->queryBuilders);
            }
        }
        [$queryBuilder] = $queryBuilders = array_values($this->queryBuilders);
        $queryBuilder->andWhere($queryBuilder->expr()->eq(
            'fe_users.uid',
            $queryBuilder->createNamedParameter(0)
        ));
        return $queryBuilders;
    }

    protected function getSubfilterSettingsFromId(string $id): array
    {
        $result = [];
        foreach ($this->settings['subfilters'] as $subfilterSetting) {
            if ($subfilterSetting['componentId'] == $id) {
                return $subfilterSetting;
            }
        }
        return $result;
    }
}
