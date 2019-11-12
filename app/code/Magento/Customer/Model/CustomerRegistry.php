<?php
declare(strict_types=1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model;

use Magento\Customer\Model\Data\CustomerSecure;
use Magento\Customer\Model\Data\CustomerSecureFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerResourceFactory;

/**
 * Registry for \Magento\Customer\Model\Customer
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CustomerRegistry
{
    const REGISTRY_SEPARATOR = ':';

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var CustomerSecureFactory
     */
    private $customerSecureFactory;

    /**
     * @var array
     */
    private $customerRegistryById = [];

    /**
     * @var array
     */
    private $customerRegistryByEmail = [];

    /**
     * @var array
     */
    private $customerSecureRegistryById = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerResourceFactory
     */
    private $customerResourceFactory;

    /**
     * Constructor
     *
     * @param CustomerFactory $customerFactory
     * @param CustomerSecureFactory $customerSecureFactory
     * @param StoreManagerInterface $storeManager
     * @param CustomerResourceFactory $customerResourceFactory
     */
    public function __construct(
        CustomerFactory $customerFactory,
        CustomerSecureFactory $customerSecureFactory,
        StoreManagerInterface $storeManager,
        CustomerResourceFactory $customerResourceFactory = null
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerSecureFactory = $customerSecureFactory;
        $this->storeManager = $storeManager;
        $this->customerResourceFactory = $customerResourceFactory
            ?? ObjectManager::getInstance()->get(CustomerResourceFactory::class);
    }

    /**
     * Retrieve Customer Model from registry given an id
     *
     * @param string $customerId
     * @return Customer
     * @throws NoSuchEntityException
     */
    public function retrieve($customerId)
    {
        if (isset($this->customerRegistryById[$customerId])) {
            return $this->customerRegistryById[$customerId];
        }
        $customer = $this->customerFactory->create();
        $this->customerResourceFactory->create()
            ->load($customer, $customerId);
        if (!$customer->getId()) {
            // customer does not exist
            throw NoSuchEntityException::singleField('customerId', $customerId);
        } else {
            $emailKey = $this->getEmailKey($customer->getEmail(), $customer->getWebsiteId());
            $this->customerRegistryById[$customerId] = $customer;
            $this->customerRegistryByEmail[$emailKey] = $customer;
            return $customer;
        }
    }

    /**
     * Retrieve Customer Model from registry given an email
     *
     * @param string $customerEmail Customers email address
     * @param string|null $websiteId Optional website ID, if not set, will use the current websiteId
     * @return Customer
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function retrieveByEmail($customerEmail, $websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
        }
        $emailKey = $this->getEmailKey($customerEmail, $websiteId);
        if (isset($this->customerRegistryByEmail[$emailKey])) {
            return $this->customerRegistryByEmail[$emailKey];
        }

        $customer = $this->customerFactory->create();

        if (isset($websiteId)) {
            $customer->setWebsiteId($websiteId);
        }

        $customer->loadByEmail($customerEmail);
        if (!$customer->getEmail()) {
            // customer does not exist
            throw new NoSuchEntityException(
                __(
                    'No such entity with %fieldName = %fieldValue, %field2Name = %field2Value',
                    [
                        'fieldName' => 'email',
                        'fieldValue' => $customerEmail,
                        'field2Name' => 'websiteId',
                        'field2Value' => $websiteId
                    ]
                )
            );
        } else {
            $this->customerRegistryById[$customer->getId()] = $customer;
            $this->customerRegistryByEmail[$emailKey] = $customer;
            return $customer;
        }
    }

    /**
     * Retrieve CustomerSecure Model from registry given an id
     *
     * @param int $customerId
     * @return CustomerSecure
     * @throws NoSuchEntityException
     */
    public function retrieveSecureData($customerId)
    {
        if (isset($this->customerSecureRegistryById[$customerId])) {
            return $this->customerSecureRegistryById[$customerId];
        }
        /** @var CustomerResourceFactory $customer */
        $customer = $this->retrieve($customerId);
        /** @var $customerSecure CustomerSecure */
        $customerSecure = $this->customerSecureFactory->create();
        $customerSecure->setPasswordHash($customer->getPasswordHash());
        $customerSecure->setRpToken($customer->getRpToken());
        $customerSecure->setRpTokenCreatedAt($customer->getRpTokenCreatedAt());
        $customerSecure->setDeleteable($customer->isDeleteable());
        $customerSecure->setFailuresNum($customer->getFailuresNum());
        $customerSecure->setFirstFailure($customer->getFirstFailure());
        $customerSecure->setLockExpires($customer->getLockExpires());
        $this->customerSecureRegistryById[$customer->getId()] = $customerSecure;

        return $customerSecure;
    }

    /**
     * Remove instance of the Customer Model from registry given an id
     *
     * @param int $customerId
     * @return void
     */
    public function remove($customerId)
    {
        if (isset($this->customerRegistryById[$customerId])) {
            /** @var Customer $customer */
            $customer = $this->customerRegistryById[$customerId];
            $emailKey = $this->getEmailKey($customer->getEmail(), $customer->getWebsiteId());
            unset($this->customerRegistryByEmail[$emailKey]);
            unset($this->customerRegistryById[$customerId]);
            unset($this->customerSecureRegistryById[$customerId]);
        }
    }

    /**
     * Remove instance of the Customer Model from registry given an email
     *
     * @param string $customerEmail Customers email address
     * @param string|null $websiteId Optional website ID, if not set, will use the current websiteId
     * @return void
     * @throws NoSuchEntityException
     */
    public function removeByEmail($customerEmail, $websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
        }
        $emailKey = $this->getEmailKey($customerEmail, $websiteId);
        if ($emailKey) {
            /** @var Customer $customer */
            $customer = $this->customerRegistryByEmail[$emailKey];
            unset($this->customerRegistryByEmail[$emailKey]);
            unset($this->customerRegistryById[$customer->getId()]);
            unset($this->customerSecureRegistryById[$customer->getId()]);
        }
    }

    /**
     * Create registry key
     *
     * @param string $customerEmail
     * @param string $websiteId
     * @return string
     */
    protected function getEmailKey($customerEmail, $websiteId)
    {
        return $customerEmail . self::REGISTRY_SEPARATOR . $websiteId;
    }

    /**
     * Replace existing customer model with a new one.
     *
     * @param Customer $customer
     * @return $this
     */
    public function push(Customer $customer)
    {
        $this->customerRegistryById[$customer->getId()] = $customer;
        $emailKey = $this->getEmailKey($customer->getEmail(), $customer->getWebsiteId());
        $this->customerRegistryByEmail[$emailKey] = $customer;
        return $this;
    }
}
