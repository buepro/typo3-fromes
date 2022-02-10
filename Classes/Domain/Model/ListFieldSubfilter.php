<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Domain\Model;

class ListFieldSubfilter extends SubfilterBase
{
    public function modifyQueryBuilders(): void
    {
        if (
            !isset($this->settings['table'], $this->settings['field']) || count($this->status) === 0 ||
            ($queryBuilder = $this->filter->getQueryBuilder('default')) === null
        ) {
            return;
        }
        $constraints = [];
        $fieldName = $this->settings['table'] . '.' . $this->settings['field'];
        foreach ($this->status as $uid) {
            $constraints[] = $queryBuilder->expr()
                ->inSet($fieldName, $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT));
        }
        // @phpstan-ignore-next-line
        $queryBuilder->andWhere($queryBuilder->expr()->orX(...$constraints));
    }
}
