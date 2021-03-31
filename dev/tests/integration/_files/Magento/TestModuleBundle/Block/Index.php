<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TestModuleBundle\Block;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Index extends Template
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepositoryInterface;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepositoryInterface,
        array $data = []
    ) {
        $this->productRepositoryInterface = $productRepositoryInterface;
        parent::__construct($context, $data);
    }

    /**
     * @param $id
     * @return object
     * @throws NoSuchEntityException
     */
    public function GetBundleById($id): object
    {
        return $this->productRepositoryInterface->getById($id);
    }
}
