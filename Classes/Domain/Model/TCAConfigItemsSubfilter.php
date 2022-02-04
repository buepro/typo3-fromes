<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Domain\Model;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class TCAConfigItemsSubfilter extends SubfilterBase
{
    public function modifyQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        if (count($this->status) === 0) {
            return $queryBuilder;
        }
        $constraints = [];
        $fieldName = $this->settings['items']['table'] . '.' . $this->settings['items']['column'];
        foreach ($this->status as $fieldValue) {
            $constraints[] = $queryBuilder->expr()->eq(
                $fieldName,
                $queryBuilder->createNamedParameter($fieldValue, \PDO::PARAM_INT)
            );
        }
        $queryBuilder->andWhere($queryBuilder->expr()->orX(...$constraints));
        return $queryBuilder;
    }
}
