<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Image\Adapter;

<<<<<<< HEAD
=======
/**
 * Image config provider.
 */
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
class Config implements ConfigInterface, UploadConfigInterface
{
    const XML_PATH_IMAGE_ADAPTER = 'dev/image/default_adapter';

    const XML_PATH_IMAGE_ADAPTERS = 'dev/image/adapters';

    const XML_PATH_MAX_WIDTH_IMAGE = 'system/upload_configuration/max_width';

    const XML_PATH_MAX_HEIGHT_IMAGE = 'system/upload_configuration/max_height';

    /**
     * Config path for the maximal image width value
     * @deprecated
     */
    const XML_PATH_MAX_WIDTH_IMAGE = 'system/upload_configuration/max_width';

    /**
     * Config path for the maximal image height value
     * @deprecated
     */
    const XML_PATH_MAX_HEIGHT_IMAGE = 'system/upload_configuration/max_height';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inherit}
     *
     * @return string
     */
    public function getAdapterAlias()
    {
        return (string)$this->config->getValue(self::XML_PATH_IMAGE_ADAPTER);
    }

    /**
     * {@inherit}
     *
     * @return mixed
     */
    public function getAdapters()
    {
        return $this->config->getValue(self::XML_PATH_IMAGE_ADAPTERS);
    }

    /**
<<<<<<< HEAD
     * Get Maximum Image Width resolution in pixels. For image resizing on client side
     *
     * @return int
     */
    public function getMaxWidth()
    {
        return $this->config->getValue(self::XML_PATH_MAX_WIDTH_IMAGE);
    }

    /**
     * Get Maximum Image Height resolution in pixels. For image resizing on client side
     *
     * @return int
     */
    public function getMaxHeight()
    {
        return $this->config->getValue(self::XML_PATH_MAX_HEIGHT_IMAGE);
=======
     * Get Maximum Image Width resolution in pixels. For image resizing on client side.
     *
     * @return int
     * @deprecated
     * @see \Magento\Backend\Model\Image\UploadResizeConfigInterface::getMaxHeight()
     */
    public function getMaxWidth(): int
    {
        return (int)$this->config->getValue(self::XML_PATH_MAX_WIDTH_IMAGE);
    }

    /**
     * Get Maximum Image Height resolution in pixels. For image resizing on client side.
     *
     * @return int
     * @deprecated
     * @see \Magento\Backend\Model\Image\UploadResizeConfigInterface::getMaxHeight()
     */
    public function getMaxHeight(): int
    {
        return (int)$this->config->getValue(self::XML_PATH_MAX_HEIGHT_IMAGE);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    }
}
