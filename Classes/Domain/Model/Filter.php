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

class Filter implements SubfilterInterface
{
    /** @var array */
    protected $status = [];
    /** @var array  */
    protected $settings = [];

    /**
     * @param array $settings Contains the keys `filter` and `subfilters`
     * @param array $status Status from the filter with subfilters
     */
    public function __construct(array $settings, array $status)
    {
        $this->settings = $settings;
        $this->status = $status;
    }

    public function modifyQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        foreach ($this->status as $subfilterStatus) {
            $settings = $this->getSubfilterSettingsFromId($subfilterStatus->id);
            if (
                class_exists($subfilterClass = $settings['class'] ?? '') &&
                ($subfilter = new $subfilterClass($settings, $subfilterStatus->value)) instanceof SubfilterInterface
            ) {
                $subfilter->modifyQueryBuilder($queryBuilder);
            }
        }
        if ($queryBuilder->getQueryPart('where') === null) {
            // We prevent showing all users when no filter criteria is set
            $queryBuilder->andWhere($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter(0)));
        }
        return $queryBuilder;
    }

    protected function getSubfilterSettingsFromId(string $id): array
    {
        $result = [];
        foreach ($this->settings['subfilters'] as $subfilterSetting) {
            if ($subfilterSetting['id'] == $id) {
                return $subfilterSetting;
            }
        }
        return $result;
    }
}
