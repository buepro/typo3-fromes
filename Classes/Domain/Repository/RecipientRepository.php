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
use TYPO3\CMS\Core\Database\ConnectionPool;
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
            ->select('first_name', 'last_name', 'name', 'email')
            ->from('fe_users');
        // @phpstan-ignore-next-line
        $rows = $filter->modifyQueryBuilder($queryBuilder)->execute()->fetchAllAssociative();
        $result = [];
        foreach ($rows as $row) {
            if ($row['email'] !== '') {
                $result[] = [
                    'name' => $row['name'] !== '' ? $row['name'] : trim($row['first_name'] . ' ' . $row['last_name']),
                    'email' => $row['email'],
                ];
            }
        }
        return $result;
    }
}
