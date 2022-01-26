<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Controller;

use Buepro\Fromes\Domain\Model\Filter;
use Buepro\Fromes\Domain\Repository\RecipientRepository;
use Buepro\Fromes\Service\FilterConfigurationService;
use Buepro\Fromes\Service\SessionService;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * EventController
 */
class MessengerController extends ActionController
{
    /** @var RecipientRepository */
    protected $recipientRepository;

    public function injectRecipientRepository(RecipientRepository $recipientRepository): void
    {
        $this->recipientRepository = $recipientRepository;
    }

    public function panelAction(): void
    {
        $this->view->assignMultiple([
            'config' => json_encode([
                'accessToken' => (new SessionService())->getAccessToken(),
                'resultElementId' => $this->settings['filter']['resultElementId'] ?? 'undefined',
                'jsonFilter' => (new FilterConfigurationService($this->settings))->getJsonFilter(),
            ], JSON_THROW_ON_ERROR),
        ]);
    }

    public function filterAction(string $filterStatus = ''): string
    {
        if (
            GeneralUtility::makeInstance(Context::class)
                ->getPropertyFromAspect('frontend.user', 'isLoggedIn') !== true
        ) {
            return json_encode([], JSON_THROW_ON_ERROR);
        }
        $filter = new Filter($this->settings, $filterStatus);
        $recipients = $this->recipientRepository->getForFilter($filter);
        return json_encode($recipients, JSON_THROW_ON_ERROR);
    }
}
