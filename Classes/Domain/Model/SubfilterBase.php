<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Domain\Model;

use Buepro\Fromes\Service\FilterConfigurationService;

abstract class SubfilterBase implements SubfilterInterface
{
    /** @var FilterInterface */
    protected $filter;
    /** @var array */
    protected $settings =  [];
    /** @var array */
    protected $status =  [];

    /**
     * @param FilterInterface $filter
     * @param array $settings
     * @param array $status
     */
    public function __construct(FilterInterface $filter, array $settings, array $status = [])
    {
        $this->filter = $filter;
        $this->settings = $settings;
        $this->status = $status;
    }

    public function getConfigForWebComponent(): array
    {
        return (new FilterConfigurationService($this->settings))->getSubfilterConfig();
    }
}
