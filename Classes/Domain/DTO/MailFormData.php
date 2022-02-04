<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Domain\DTO;

use TYPO3\CMS\Extbase\Annotation as Extbase;

class MailFormData
{
    /**
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 200})
     * @Extbase\Validate("Text")
     * @var string
     */
    protected $subject;

    /**
     * @Extbase\Validate("StringLength", options={"minimum": 10, "maximum": 3000})
     * @Extbase\Validate("Text")
     * @var string
     */
    protected $message;

    /**
     * Json encoded array containing uid's from receivers
     *
     * @var string
     */
    protected $receivers;

    public function __construct(string $subject, string $message, string $receivers)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->receivers = $receivers;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getReceivers(): array
    {
        $receivers = json_decode($this->receivers, true, 512, JSON_THROW_ON_ERROR);
        return array_map(static function ($receiver): int {
            return (int)$receiver;
        }, $receivers);
    }
}
