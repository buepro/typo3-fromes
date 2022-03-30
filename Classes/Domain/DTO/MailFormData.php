<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Domain\DTO;

use Buepro\Fromes\Utility\FileUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\File\BasicFileUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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

    /**
     * @var string[] $files
     */
    protected $files;

    public function __construct(string $subject, string $message, string $receivers, array $files)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->receivers = $receivers;
        $this->setFiles($files);
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

    protected function setFiles(array $files): self
    {
        $this->files = [];
        $basicFileUtility = GeneralUtility::makeInstance(BasicFileUtility::class);
        $allowedFileTypes = GeneralUtility::trimExplode(',', GeneralUtility::makeInstance(
            ExtensionConfiguration::class
        )->get('fromes', 'allowedFileTypes'));
        $allowedMimeTypes = FileUtility::getMimeTypes($allowedFileTypes);

        foreach ($files as $file) {
            if (
                $file['tmp_name'] !== '' && $file['name'] !== '' && $files['type'] !== '' &&
                FileUtility::fileAllowed($file['tmp_name'], $allowedMimeTypes) &&
                ($tempFileName = GeneralUtility::upload_to_tempfile($file['tmp_name'])) !== ''
            ) {
                $newFileName = $basicFileUtility->getUniqueName($file['name'], GeneralUtility::dirname($tempFileName) . '/');
                if (
                    $newFileName !== null &&
                    GeneralUtility::upload_copy_move($tempFileName, $newFileName) === false &&
                    file_exists($newFileName)
                ) {
                    $this->files[] = $newFileName;
                }
                GeneralUtility::unlink_tempfile($tempFileName);
            }
        }
        return $this;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function deleteFiles(): self
    {
        foreach ($this->files as $file) {
            GeneralUtility::unlink_tempfile($file);
        }
        return $this;
    }
}
