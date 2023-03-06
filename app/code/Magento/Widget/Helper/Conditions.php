<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Widget\Helper;

use Magento\Framework\Data\Wysiwyg\Normalizer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Widget Conditions helper.
 */
class Conditions
{
    /**
     * @param Json|null $serializer
     * @param Normalizer|null $normalizer
     */
    public function __construct(
        private ?Json $serializer = null,
        private ?Normalizer $normalizer = null
    ) {
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->normalizer = $normalizer ?: ObjectManager::getInstance()->get(Normalizer::class);
    }

    /**
     * Encode widget conditions to be used with WYSIWIG.
     *
     * @param array $value
     * @return string
     */
    public function encode(array $value)
    {
        return $this->normalizer->replaceReservedCharacters($this->serializer->serialize($value));
    }

    /**
     * Decode previously encoded widget conditions.
     *
     * @param string $value
     * @return array
     */
    public function decode($value)
    {
        return $this->serializer->unserialize(
            $this->normalizer->restoreReservedCharacters($value)
        );
    }
}
