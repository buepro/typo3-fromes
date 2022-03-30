<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Utility;

use TYPO3\CMS\Core\Resource\MimeTypeDetector;
use TYPO3\CMS\Core\Type\File\FileInfo;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileUtility
{
    public static function getMimeTypes(array $extensions): array
    {
        $result = [];
        $detector = GeneralUtility::makeInstance(MimeTypeDetector::class);
        foreach ($extensions as $extension) {
            $result = array_merge($result, $detector->getMimeTypesForFileExtension($extension));
        }
        return array_unique($result);
    }

    public static function fileAllowed(string $file, array $allowedMimeTypes): bool
    {
        $mimeType = GeneralUtility::makeInstance(FileInfo::class, $file)->getMimeType();
        return $mimeType !== false && count(array_intersect($allowedMimeTypes, [$mimeType])) > 0;
    }
}
