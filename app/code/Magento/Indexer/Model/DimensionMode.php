<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Indexer\Model;

/**
 * DTO to work with dimension mode
 */
class DimensionMode
{
    /**
     * @var array
     */
    private $name;

    /**
     * @var array
     */
    private $dimensions;

    /**
     * @param string $name
<<<<<<< HEAD
     * @param array  $dimensions
=======
     * @param array $dimensions
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     */
    public function __construct(string $name, array $dimensions)
    {
        $this->dimensions = (function (string ...$dimensions) {
            return $dimensions;
        })(...$dimensions);
        $this->name = $name;
    }

    /**
     * Returns dimension name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns dimension modes
     *
     * @return string[]
     */
    public function getDimensions(): array
    {
        return $this->dimensions;
    }
}
