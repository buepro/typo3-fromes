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

class UserGroupSubfilter extends SubfilterBase
{
    public function modifyQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        if (count($this->status) === 0) {
            return $queryBuilder;
        }
        $constraints = [];
        foreach ($this->status as $uid) {
            $constraints[] = $queryBuilder->expr()
                ->inSet('usergroup', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT));
        }
        // @phpstan-ignore-next-line
        $queryBuilder->andWhere($queryBuilder->expr()->orX(...$constraints));
        return $queryBuilder;
    }
}
