<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Controller;

use Buepro\Fromes\Domain\DTO\MailFormData;
use Buepro\Fromes\Domain\Model\Filter;
use Buepro\Fromes\Domain\Repository\ReceiverRepository;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * EventController
 */
class MessengerController extends ActionController
{
    /** @var ReceiverRepository */
    protected $receiverRepository;

    public function injectReceiverRepository(ReceiverRepository $receiverRepository): void
    {
        $this->receiverRepository = $receiverRepository;
    }

    public function panelAction(): void
    {
        $this->view->assignMultiple([
            'filterConfig' => (new Filter($this->settings))->getConfigForWebComponent(),
            'subfilterConfigById' => $this->getSubfilterConfigById(),
        ]);
    }

    public function filterAction(string $filterStatus = ''): string
    {
        $filterStatus = (json_decode($filterStatus, false, 512, JSON_THROW_ON_ERROR));
        if (
            $filterStatus instanceof \stdClass && property_exists($filterStatus, 'data') &&
            class_exists($filterClass = $this->settings['filter']['class'])
        ) {
            $filter = new $filterClass($this->settings, $filterStatus->data);
            $receivers = $this->receiverRepository->getForFilter($filter, $this->settings['filter']['result']);
            return json_encode($receivers, JSON_THROW_ON_ERROR);
        }
        return json_encode([], JSON_THROW_ON_ERROR);
    }

    /**
     * @param MailFormData $mailFormData
     * @return string
     * @throws \JsonException
     */
    public function mailAction(MailFormData $mailFormData): string
    {
        $loginUser = $GLOBALS['TSFE']->fe_user->user;
        if (
            !GeneralUtility::validEmail($systemEmail = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']) ||
            !GeneralUtility::validEmail($senderEmail = $loginUser['email'])
        ) {
            throw new \LogicException('Mail delivery failed. The email address from the system user or the '
                . 'registered user is not correct.', 1643390458);
        }
        $receivers = $this->receiverRepository->getForUidList($mailFormData->getReceivers());
        if (count($receivers) === 0) {
            throw new \LogicException('Mail delivery failed. The requested receivers are not available.', 1643384193);
        }
        $receivers = array_map(static function ($receiver): Address {
            $receiverName = $receiver['name'] !== '' ? $receiver['name'] : trim($receiver['first_name'] . ' ' . $receiver['last_name']);
            return new Address($receiver['email'], $receiverName);
        }, $receivers);
        $senderName = $loginUser['name'] !== '' ? $loginUser['name'] : trim($loginUser['first_name'] . ' ' . $loginUser['last_name']);
        $mail = GeneralUtility::makeInstance(MailMessage::class);
        $mail
            ->from(new Address($systemEmail, $senderName))
            ->replyTo(new Address($senderEmail, $senderName))
            ->to(new Address($senderEmail, $senderName))
            ->bcc(...$receivers)
            ->subject($mailFormData->getSubject())
            ->text($mailFormData->getMessage());
        foreach ($mailFormData->getFiles() as $file) {
            $mail->attachFromPath($file);
        }
        $mail->send();
        $mailFormData->deleteFiles();
        return json_encode(['Mail sent successfully'], JSON_THROW_ON_ERROR);
    }

    private function getSubfilterConfigById(): array
    {
        $result = [];
        $subfilters = GeneralUtility::trimExplode(',', $this->settings['filter']['includedSubfilters']);
        foreach ($subfilters as $tsName => $subfilter) {
            if (
                ($config = $this->settings['subfilters'][$subfilter] ?? false) !== false &&
                isset($config['componentId']) &&
                class_exists($config['class'])
            ) {
                $config['tsName'] = $tsName;
                $result[$config['componentId']] = $config;
            }
        }
        return $result;
    }
}
