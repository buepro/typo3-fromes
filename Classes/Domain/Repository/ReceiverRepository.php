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
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * The repository for Receivers
 */
class ReceiverRepository
{
    public function getForFilter(Filter $filter, array $conf = []): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users')
            ->select(
                'fe_users.uid',
                'fe_users.username',
                'fe_users.first_name',
                'fe_users.last_name',
                'fe_users.name',
                'fe_users.email'
            )
            ->from('fe_users')
            ->groupBy('fe_users.uid', 'fe_users.first_name')
            ->orderBy('fe_users.first_name');
        $rows = $this->getFromQueryBuilder($filter->modifyQueryBuilder($queryBuilder));
        $result = [];
        if ($applyStdWrap = (isset($conf['label']) && is_array($conf['label']))) {
            $cObj = new ContentObjectRenderer();
            $tsConf = GeneralUtility::makeInstance(TypoScriptService::class)
                ->convertPlainArrayToTypoScriptArray($conf);
        }
        foreach ($rows as $row) {
            if ($applyStdWrap) {
                $cObj->data = $row;
                $label = $cObj->stdWrap('', $tsConf['label.']);
            }
            if (!isset($label) || $label === '') {
                $label = trim($row['first_name'] . ' ' . $row['last_name']);
                $label = $label !== '' ? $label : $row['name'];
                $label = $label !== '' ? $label : $row['email'];
            }
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
            ->where(
                $queryBuilder->expr()->in(
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
