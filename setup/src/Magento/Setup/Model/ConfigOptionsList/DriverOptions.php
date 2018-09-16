<?php declare(strict_types=1);
namespace Magento\Setup\Model\ConfigOptionsList;

use Magento\Framework\Config\ConfigOptionsListConstants;

class DriverOptions
{
    /**
     * @params array $options
     * @return array
     */
    public function getDriverOptions($options)
    {
        $driverOptionKeys = [
            ConfigOptionsListConstants::KEY_MYSQL_SSL_KEY => ConfigOptionsListConstants::INPUT_KEY_DB_SSL_KEY,
            ConfigOptionsListConstants::KEY_MYSQL_SSL_CERT => ConfigOptionsListConstants::INPUT_KEY_DB_SSL_CERT,
            ConfigOptionsListConstants::KEY_MYSQL_SSL_CA => ConfigOptionsListConstants::INPUT_KEY_DB_SSL_CA,
            ConfigOptionsListConstants::KEY_MYSQL_SSL_VERIFY => ConfigOptionsListConstants::INPUT_KEY_DB_SSL_VERIFY
        ];
        $driverOptions = [];
        foreach ($driverOptionKeys as $configKey => $driverOptionKey) {
            if ($this->optionExists($options, $driverOptionKey)) {
                $driverOptions[$configKey] = $options[$driverOptionKey];
            }
        }
        return $driverOptions;
    }

    /**
     * @param array $options
     * @param string $driverOptionKey
     * @return bool
     */
    private function optionExists($options, $driverOptionKey)
    {
        return $options[$driverOptionKey] === false || !empty($options[$driverOptionKey]);
    }
}
