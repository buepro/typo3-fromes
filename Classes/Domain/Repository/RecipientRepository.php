<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Domain\Repository;

use Buepro\Fromes\Domain\Model\Filter;
use Doctrine\DBAL\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The repository for Registrations
 */
class RecipientRepository
{
    public function getForFilter(Filter $filter): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users')
            ->select('uid', 'first_name', 'last_name', 'name', 'email')
            ->from('fe_users');
        $rows = $this->getFromQueryBuilder($filter->modifyQueryBuilder($queryBuilder));
        $result = [];
        foreach ($rows as $row) {
            $label = $row['name'] !== '' ? $row['name'] : trim($row['first_name'] . ' ' . $row['last_name']);
            $label = $label === '' ? $row['email'] : $label;
            if (GeneralUtility::validEmail($row['email'])) {
                $result[] = [
                    'id' => $row['uid'],
                    'label' => $label,
                ];
            }
        }
        return $result;
    }

    public function getForUidList(array $uidList): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');
        $queryBuilder
            ->select('uid', 'first_name', 'last_name', 'name', 'email')
            ->from('fe_users')
            ->where($queryBuilder->expr()->in(
                'uid',
                $queryBuilder->createNamedParameter($uidList, Connection::PARAM_INT_ARRAY)
            )
        );
        return $this->getFromQueryBuilder($queryBuilder);
    }

    private function getFromQueryBuilder(QueryBuilder $queryBuilder): array
    {
        $queryResult = $queryBuilder->execute();
        $rows = [];
        if ($queryResult instanceof \Doctrine\DBAL\ForwardCompatibility\Result) {
            $rows = $queryResult->fetchAllAssociative();
        }
        if ($rows === [] && $queryResult instanceof \Doctrine\DBAL\Driver\Statement) {
            // @phpstan-ignore-next-line
            $rows = $queryResult->fetchAll();
        }
        return $rows;
    }
}
