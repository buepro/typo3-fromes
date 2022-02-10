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
interface FilterInterface
{

    /**
     * @param array $settings Contains the keys `filter` and `subfilters`
     * @param array $status Status from the filter with subfilters
     */
    public function __construct(array $settings, array $status = []);

    /**
     * @return array Contains the keys accessToken and subfilters
     */
    public function getConfigForWebComponent(): array;

    /**
     * Creates a local reference to the required query builders and mainly defines their join clauses.
     * If required clones the provided query builder.
     *
     * @param QueryBuilder $queryBuilder Provided by the ReceiverRepository
     * @return FilterInterface
     */
    public function setupQueryBuilders(QueryBuilder $queryBuilder): self;

    /**
     * @return QueryBuilder|null The named query builder
     * @see setupQueryBuilders
     */
    public function getQueryBuilder(string $name): ?QueryBuilder;

    /**
     * @return QueryBuilder[]
     */
    public function modifyQueryBuilders(): array;
}
