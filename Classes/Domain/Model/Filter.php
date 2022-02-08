<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Domain\Model;

use Buepro\Fromes\Service\SessionService;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Filter implements FilterInterface
{
    /** @var array */
    protected $status = [];
    /** @var array  */
    protected $settings = [];

    /**
     * @param array $settings Contains the keys `filter` and `subfilters`
     * @param array $status Status from the filter with subfilters
     */
    public function __construct(array $settings, array $status = [])
    {
        $this->settings = $settings;
        $this->status = $status;
    }

    /**
     * @return array Contains the keys accessToken, resultComponentId and jsonFilter
     */
    public function getConfigForWebComponent(): array
    {
        $subfilterConfig = [];
        $subfilters = GeneralUtility::trimExplode(',', $this->settings['filter']['includedSubfilters']);
        foreach ($subfilters as $subfilter) {
            if (
                ($config = $this->settings['subfilters'][$subfilter] ?? false) !== false &&
                class_exists($className = $config['class'])
            ) {
                $subfilterConfig[] = (new $className($config))->getConfigForWebComponent();
            }
        }
        return [
            'accessToken' => (new SessionService())->getAccessToken(),
            'subfilters' => $subfilterConfig,
        ];
    }

    /**
     * @inheritDoc
     */
    public function setupQueryBuilders(QueryBuilder $queryBuilder): array
    {
        return [$queryBuilder];
    }

    /**
     * @param QueryBuilder ...$queryBuilders
     * @return QueryBuilder[]
     */
    public function modifyQueryBuilders(QueryBuilder ...$queryBuilders): array
    {
        foreach ($this->status as $subfilterStatus) {
            $settings = $this->getSubfilterSettingsFromId($subfilterStatus->id);
            if (
                class_exists($subfilterClass = $settings['class'] ?? '') &&
                ($subfilter = new $subfilterClass($settings, $subfilterStatus->value)) instanceof SubfilterInterface
            ) {
                $subfilter->modifyQueryBuilders(...$queryBuilders);
            }
        }
        // Prevent showing all users when no filter criteria is set
        foreach ($queryBuilders as $queryBuilder) {
            if ($queryBuilder->getQueryPart('where') !== null) {
                return $queryBuilders;
            }
        }
        $queryBuilders[0]->andWhere($queryBuilders[0]->expr()->eq(
            'fe_users.uid',
            $queryBuilders[0]->createNamedParameter(0)
        ));
        return $queryBuilders;
    }

    protected function getSubfilterSettingsFromId(string $id): array
    {
        $result = [];
        foreach ($this->settings['subfilters'] as $subfilterSetting) {
            if ($subfilterSetting['componentId'] == $id) {
                return $subfilterSetting;
            }
        }
        return $result;
    }
}
