<?php

namespace Kaliop\eZMigrationBundle\Core\ComplexField;

use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use Kaliop\eZMigrationBundle\API\ComplexFieldInterface;

class EzImage extends AbstractComplexField implements ComplexFieldInterface
{
    /**
     * Creates a value object to use as the field value when setting an image field type.
     *
     * @param array|string $fieldValue The path to the file or an array with 'path' and 'alt_text' keys
     * @param array $context The context for execution of the current migrations. Contains f.e. the path to the migration
     * @return ImageValue
     */
    public function createValue($fieldValue, array $context = array())
    {
        $altText = '';
        $fileName = '';

        if ($fieldValue === null) {
            return new ImageValue();
        } else if (is_string($fieldValue)) {
            $filePath = $fieldValue;
        } else {
            $filePath = $fieldValue['path'];
            if (isset($fieldValue['alt_text'])) {
                $altText = $fieldValue['alt_text'];
            }
            if (isset($fieldValue['filename'])) {
                $fileName = $fieldValue['filename'];
            }
        }

        // default format: path is relative to the 'images' dir
        $realFilePath = dirname($context['path']) . '/images/' . $filePath;

        // but in the past, when using a string, this worked as well as an absolute path, so we have to support it as well
        if (!is_file($realFilePath) && is_file($filePath)) {
            $realFilePath = $filePath;
        }

        return new ImageValue(
            array(
                'path' => $realFilePath,
                'fileSize' => filesize($realFilePath),
                'fileName' => $fileName != '' ? $fileName : basename($realFilePath),
                'alternativeText' => $altText
            )
        );
    }
}
