<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Css\PreProcessor;

use Magento\Framework\View\Asset\PreProcessorInterface;

class Scss implements PreProcessorInterface
{
    /**
     * @var \Magento\Framework\Css\PreProcessor\FileGenerator
     */
    protected $fileGenerator;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @param \Magento\Framework\Css\PreProcessor\FileGenerator $fileGenerator
     * @param AdapterInterface $adapter
     */
    public function __construct(
        \Magento\Framework\Css\PreProcessor\FileGenerator $fileGenerator,
        AdapterInterface $adapter
    ) {
        $this->fileGenerator = $fileGenerator;
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function process(\Magento\Framework\View\Asset\PreProcessor\Chain $chain)
    {
        $chain->setContentType('scss');
        $tmpFile = $this->fileGenerator->generateFileTree($chain);
        $cssContent = $this->adapter->process($tmpFile);
        $cssTrimmedContent = trim($cssContent);
        if (!empty($cssTrimmedContent)) {
            $chain->setContent($cssContent);
        }
        $chain->setContentType('css');
    }
}
