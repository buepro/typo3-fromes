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

/**
 * Provides a filter web component with configuration and a repository with filter constraints.
 * The filter constraints are defined in query builders.
 */
interface FilterInterface extends SubfilterInterface
{
    /**
     * Mainly defines the join clauses. If required clones the provided query builder.
     *
     * @param QueryBuilder $queryBuilder Provided by the ReceiverRepository
     * @return QueryBuilder[]
     */
    public function setupQueryBuilders(QueryBuilder $queryBuilder): array;
}
