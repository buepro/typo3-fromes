<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Domain\Model;

use Buepro\Fromes\Service\FilterConfigurationService;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class UserGroupSubfilter implements SubfilterInterface
{
    /** @var array */
    protected $settings =  [];
    /** @var array */
    protected $status =  [];

    /**
     * @param array $settings
     * @param array $status
     */
    public function __construct(array $settings, array $status = [])
    {
        $this->settings = $settings;
        $this->status = $status;
    }

    public function getConfigForWebComponent(): array
    {
        return (new FilterConfigurationService($this->settings))->getSubfilterConfig();
    }

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
