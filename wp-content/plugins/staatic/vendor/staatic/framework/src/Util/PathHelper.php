<?php

namespace Staatic\Framework\Util;

final class PathHelper
{
    public static function determineFilePath(string $uriPath, bool $treatHtmlSpecial = \true) : string
    {
        $filePath = \rawurldecode($uriPath);
        $filePath = \preg_replace('~/+~', '/', $filePath);
        $filePath = '/' . \ltrim($filePath, '/');
        if (substr_compare($filePath, '/', -strlen('/')) !== 0 && ($pos = \strrpos($filePath, '.')) !== \false) {
            if (!$treatHtmlSpecial) {
                return $filePath;
            }
            $extension = \substr($filePath, $pos + 1);
            if (!\in_array($extension, ['htm', 'html'])) {
                return $filePath;
            }
        }
        if (substr_compare($filePath, '/', -strlen('/')) !== 0) {
            $filePath .= '/';
        }
        $filePath .= 'index.html';
        return $filePath;
    }
}
