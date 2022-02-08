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
 * Provides a subfilter web component with configuration and modify the filter query builders.
 */
interface SubfilterInterface
{
    /**
     * @param array $settings See TS setup `plugin.tx_fromes_messenger.settings.subfilters`
     * @param array $status The data structure depends on the class implementing this interface
     */
    public function __construct(FilterInterface $filter, array $settings, array $status = []);

    /**
     * @return array The data structure depends on the class implementing this interface
     */
    public function getConfigForWebComponent(): array;

    /**
     * Gets the required query builders from the filter and modifies them.
     */
    public function modifyQueryBuilders(): void;
}
