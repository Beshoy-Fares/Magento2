<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Protocol\CurlTransport;

use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;

/**
 * Curl transport on webapi.
 */
class WebApiDecorator implements CurlInterface
{
    /**
     * Curl transport protocol.
     *
     * @var CurlTransport
     */
    protected $transport;

    /**
     * System config.
     *
     * @var DataInterface
     */
    protected $configuration;

    /**
     * Api headers.
     *
     * @var array
     */
    protected $headers = [
        'Accept: application/json',
        'Content-Type:application/json',
    ];

    /**
     * Response data.
     *
     * @var string
     */
    protected $response;

    /**
     * @construct
     * @param CurlTransport $transport
     * @param DataInterface $configuration
     */
    public function __construct(CurlTransport $transport, DataInterface $configuration)
    {
        $this->transport = $transport;
        $this->configuration = $configuration;
    }

    /**
     * Send request to the remote server.
     *
     * @param string $url
     * @param array $params
     * @param string $method
     * @param array $headers
     * @return void
     */
    public function write($url, $params = [], $method = CurlInterface::POST, $headers = [])
    {
        $headers = array_merge(
            ['Authorization: Bearer ' . $this->configuration->get('handler/0/api/0/token/0/value')],
            $this->headers,
            $headers
        );

        $this->transport->write($url, json_encode($params), $method, $headers);
    }

    /**
     * Read response from server.
     *
     * @return string
     */
    public function read()
    {
        $this->response = $this->transport->read();
        return $this->response;
    }

    /**
     * Add additional option to cURL.
     *
     * @param  int $option the CURLOPT_* constants
     * @param  mixed $value
     * @return void
     */
    public function addOption($option, $value)
    {
        $this->transport->addOption($option, $value);
    }

    /**
     * Close the connection to the server.
     *
     * @return void
     */
    public function close()
    {
        $this->transport->close();
    }

    /**
     * Update index for all entities via webapi.
     *
     * @return string
     */
    public function reindexAll()
    {
        $this->write(
            $_ENV['app_frontend_url'] . 'rest/V1/indexer/processor/all',
            [],
            CurlInterface::GET
        );
        $response = $this->read();

        return $response;
    }
}
