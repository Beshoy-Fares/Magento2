<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Analytics\ReportXml;

use Magento\Framework\Config\DataInterface;

/**
<<<<<<< HEAD
 * Class Config
 *
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 * Config of ReportXml
 */
class Config implements ConfigInterface
{
    /**
     * @var DataInterface
     */
    private $data;

    /**
     * Config constructor.
     *
     * @param DataInterface $data
     */
    public function __construct(
        DataInterface $data
    ) {
        $this->data = $data;
    }

    /**
     * Returns config value by name
     *
     * @param string $queryName
     * @return array
     */
    public function get($queryName)
    {
        return $this->data->get($queryName);
    }
}
