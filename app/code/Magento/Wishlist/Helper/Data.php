<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Wishlist\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Escaper;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;

/**
 * Wishlist Data Helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 *
 * @api
 * @since 100.0.2
 */
class Data extends AbstractHelper
{
    /**
     * Config key 'Display Wishlist Summary'
     */
    public const XML_PATH_WISHLIST_LINK_USE_QTY = 'wishlist/wishlist_link/use_qty';

    /**
     * Config key 'Display Out of Stock Products'
     */
    public const XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK = 'cataloginventory/options/show_out_of_stock';

    /**
     * Currently logged in customer
     *
     * @var CustomerInterface
     */
    protected $_currentCustomer;

    /**
     * Customer Wishlist instance
     *
     * @var Wishlist
     */
    protected $_wishlist;

    /**
     * Wishlist Product Items Collection
     *
     * @var Collection
     */
    protected $_productCollection;

    /**
     * Wishlist Items Collection
     *
     * @var Collection
     */
    protected $_wishlistItemCollection;

    /**
     * Magento framework Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var View
     */
    protected $_customerViewHelper;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Session $customerSession
     * @param WishlistFactory $wishlistFactory
     * @param StoreManagerInterface $storeManager
     * @param PostHelper $postDataHelper
     * @param View $customerViewHelper
     * @param WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Escaper $escaper
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Session $customerSession,
        WishlistFactory $wishlistFactory,
        StoreManagerInterface $storeManager,
        PostHelper $postDataHelper,
        View $customerViewHelper,
        protected readonly WishlistProviderInterface $wishlistProvider,
        protected readonly ProductRepositoryInterface $productRepository,
        private ?Escaper $escaper = null
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_storeManager = $storeManager;
        $this->_postDataHelper = $postDataHelper;
        $this->_customerViewHelper = $customerViewHelper;
        $this->escaper = $escaper ?? ObjectManager::getInstance()->get(Escaper::class);
        parent::__construct($context);
    }

    /**
     * Retrieve customer login status
     *
     * @return bool
     */
    protected function _isCustomerLogIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * Retrieve logged in customer
     *
     * @return CustomerInterface
     */
    protected function _getCurrentCustomer()
    {
        return $this->getCustomer();
    }

    /**
     * Set current customer
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->_currentCustomer = $customer;
    }

    /**
     * Retrieve current customer
     *
     * @return CustomerInterface|null
     */
    public function getCustomer()
    {
        if (!$this->_currentCustomer && $this->_customerSession->isLoggedIn()) {
            $this->_currentCustomer = $this->_customerSession->getCustomerData();
        }
        return $this->_currentCustomer;
    }

    /**
     * Retrieve wishlist by logged in customer
     *
     * @return Wishlist
     */
    public function getWishlist()
    {
        if ($this->_wishlist === null) {
            if ($this->_coreRegistry->registry('shared_wishlist')) {
                $this->_wishlist = $this->_coreRegistry->registry('shared_wishlist');
            } else {
                $this->_wishlist = $this->wishlistProvider->getWishlist();
            }
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve wishlist item count (include config settings)
     *
     * Used in top link menu only
     *
     * @return int
     */
    public function getItemCount()
    {
        $storedDisplayType = $this->_customerSession->getWishlistDisplayType();
        $currentDisplayType = $this->scopeConfig->getValue(
            self::XML_PATH_WISHLIST_LINK_USE_QTY,
            ScopeInterface::SCOPE_STORE
        );

        $storedDisplayOutOfStockProducts = $this->_customerSession->getDisplayOutOfStockProducts();
        $currentDisplayOutOfStockProducts = $this->scopeConfig->getValue(
            self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK,
            ScopeInterface::SCOPE_STORE
        );
        if (!$this->_customerSession->hasWishlistItemCount() ||
            $currentDisplayType != $storedDisplayType ||
            $this->_customerSession->hasDisplayOutOfStockProducts() ||
            $currentDisplayOutOfStockProducts != $storedDisplayOutOfStockProducts
        ) {
            $this->calculate();
        }

        return $this->_customerSession->getWishlistItemCount();
    }

    /**
     * Create wishlist item collection
     *
     * @return Collection
     */
    protected function _createWishlistItemCollection()
    {
        return $this->getWishlist()->getItemCollection();
    }

    /**
     * Retrieve wishlist items collection
     *
     * @return Collection
     */
    public function getWishlistItemCollection()
    {
        if ($this->_wishlistItemCollection === null) {
            $this->_wishlistItemCollection = $this->_createWishlistItemCollection();
        }
        return $this->_wishlistItemCollection;
    }

    /**
     * Retrieve Item Store for URL
     *
     * @param Product|Item $item
     * @return Store
     */
    protected function _getUrlStore($item)
    {
        $storeId = null;
        $product = null;
        if ($item instanceof Item) {
            $product = $item->getProduct();
        } elseif ($item instanceof Product) {
            $product = $item;
        }
        if ($product) {
            if ($product->isVisibleInSiteVisibility()) {
                $storeId = $product->getStoreId();
            } else {
                if ($product->hasUrlDataObject()) {
                    $storeId = $product->getUrlDataObject()->getStoreId();
                }
            }
        }
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Retrieve params for removing item from wishlist
     *
     * @param Product|Item $item
     * @param bool $addReferer
     * @return string
     */
    public function getRemoveParams($item, $addReferer = false)
    {
        $url = $this->_getUrl('wishlist/index/remove');
        $params = ['item' => $item->getWishlistItemId()];
        $params[ActionInterface::PARAM_NAME_URL_ENCODED] = '';

        if ($addReferer) {
            $params = $this->addRefererToParams($params);
        }

        return $this->_postDataHelper->getPostData($url, $params);
    }

    /**
     * Retrieve URL for configuring item from wishlist
     *
     * @param Product|Item $item
     * @return string
     */
    public function getConfigureUrl($item)
    {
        return $this->_getUrl(
            'wishlist/index/configure',
            [
                'id' => $item->getWishlistItemId(),
                'product_id' => $item->getProductId(),
                'qty' => (int)$item->getQty()
            ]
        );
    }

    /**
     * Retrieve params for adding product to wishlist
     *
     * @param Product|Item $item
     * @param array $params
     * @return string
     */
    public function getAddParams($item, array $params = [])
    {
        $productId = null;
        if ($item instanceof Product) {
            $productId = (int) $item->getEntityId();
        }
        if ($item instanceof Item) {
            $productId = (int) $item->getProductId();
        }

        $url = $this->_getUrlStore($item)->getUrl('wishlist/index/add');
        if ($productId) {
            $params['product'] = $productId;
        }

        return $this->_postDataHelper->getPostData(
            $this->escaper->escapeUrl($url),
            $params
        );
    }

    /**
     * Retrieve params for adding product to wishlist
     *
     * @param int $itemId
     * @return string
     */
    public function getMoveFromCartParams($itemId)
    {
        $url = $this->_getUrl('wishlist/index/fromcart');
        $params = ['item' => $itemId];
        return $this->_postDataHelper->getPostData($url, $params);
    }

    /**
     * Retrieve params for updating product in wishlist
     *
     * @param Product|Item $item
     * @return string|false
     */
    public function getUpdateParams($item)
    {
        $itemId = null;
        if ($item instanceof Product) {
            $itemId = $item->getWishlistItemId();
            $productId = $item->getId();
        }
        if ($item instanceof Item) {
            $itemId = $item->getId();
            $productId = $item->getProduct()->getId();
        }

        $url = $this->_getUrl('wishlist/index/updateItemOptions');
        if ($itemId) {
            $params = ['id' => $itemId, 'product' => $productId, 'qty' => $item->getQty()];
            return $this->_postDataHelper->getPostData($url, $params);
        }

        return false;
    }

    /**
     * Retrieve params for adding item to shopping cart
     *
     * @param string|Product|Item $item
     * @return string
     */
    public function getAddToCartUrl($item)
    {
        return $this->_getUrlStore($item)->getUrl('wishlist/index/cart', $this->_getCartUrlParameters($item));
    }

    /**
     * Retrieve URL for adding item to shopping cart
     *
     * @param string|Product|Item $item
     * @param bool $addReferer
     * @return string
     */
    public function getAddToCartParams($item, $addReferer = false)
    {
        $params = $this->_getCartUrlParameters($item);
        $params[ActionInterface::PARAM_NAME_URL_ENCODED] = '';

        if ($addReferer) {
            $params = $this->addRefererToParams($params);
        }

        return $this->_postDataHelper->getPostData(
            $this->_getUrlStore($item)->getUrl('wishlist/index/cart'),
            $params
        );
    }

    /**
     * Add UENC referer to params
     *
     * @param array $params
     * @return array
     */
    public function addRefererToParams(array $params)
    {
        $params[ActionInterface::PARAM_NAME_URL_ENCODED] =
            $this->urlEncoder->encode($this->_getRequest()->getServer('HTTP_REFERER'));
        return $params;
    }

    /**
     * Retrieve URL for adding item to shopping cart from shared wishlist
     *
     * @param string|Product|Item $item
     * @return string
     */
    public function getSharedAddToCartUrl($item)
    {
        return $this->_postDataHelper->getPostData(
            $this->_getUrlStore($item)->getUrl('wishlist/shared/cart'),
            $this->_getCartUrlParameters($item)
        );
    }

    /**
     * Retrieve URL for adding All items to shopping cart from shared wishlist
     *
     * @return string
     */
    public function getSharedAddAllToCartUrl()
    {
        return $this->_postDataHelper->getPostData(
            $this->_storeManager->getStore()->getUrl('*/*/allcart', ['_current' => true])
        );
    }

    /**
     * Get cart URL parameters
     *
     * @param string|Product|Item $item
     * @return array
     */
    protected function _getCartUrlParameters($item)
    {
        $params = [
            'item' => is_string($item) ? $item : $item->getWishlistItemId(),
        ];
        if ($item instanceof Item) {
            $params['qty'] = $item->getQty();
        }
        return $params;
    }

    /**
     * Retrieve customer wishlist url
     *
     * @param int $wishlistId
     * @return string
     */
    public function getListUrl($wishlistId = null)
    {
        $params = [];
        if ($wishlistId) {
            $params['wishlist_id'] = $wishlistId;
        }
        return $this->_getUrl('wishlist', $params);
    }

    /**
     * Check is allow wishlist module
     *
     * @return bool
     */
    public function isAllow()
    {
        $isOutputEnabled  = $this->_moduleManager->isOutputEnabled($this->_getModuleName());

        $isWishlistActive = $this->scopeConfig->getValue(
            'wishlist/general/active',
            ScopeInterface::SCOPE_STORE
        );

        return $isOutputEnabled && $isWishlistActive;
    }

    /**
     * Check is allow wishlist action in shopping cart
     *
     * @return bool
     */
    public function isAllowInCart()
    {
        return $this->isAllow() && $this->getCustomer();
    }

    /**
     * Retrieve customer name
     *
     * @return string|null
     */
    public function getCustomerName()
    {
        return $this->getCustomer()
            ? $this->_customerViewHelper->getCustomerName($this->getCustomer())
            : null;
    }

    /**
     * Retrieve RSS URL
     *
     * @param int|string|null $wishlistId
     * @return string
     */
    public function getRssUrl($wishlistId = null)
    {
        $params = [];
        $customer = $this->_getCurrentCustomer();
        if ($customer) {
            $key = $customer->getId() . ',' . $customer->getEmail();
            $params = ['data' => $this->urlEncoder->encode($key), '_secure' => false];
        }
        if ($wishlistId) {
            $params['wishlist_id'] = $wishlistId;
        }
        return $this->_getUrl('wishlist/index/rss', $params);
    }

    /**
     * Retrieve default empty comment message
     *
     * @return Phrase
     */
    public function defaultCommentString()
    {
        return __('Comment');
    }

    /**
     * Retrieve default empty comment message
     *
     * @return Phrase
     */
    public function getDefaultWishlistName()
    {
        return __('Wish List');
    }

    /**
     * Calculate count of wishlist items and put value to customer session.
     *
     * Method called after wishlist modifications and trigger 'wishlist_items_renewed' event.
     * Depends from configuration.
     *
     * @return $this
     */
    public function calculate()
    {
        $count = 0;
        if ($this->getCustomer()) {
            $collection = $this->getWishlistItemCollection()->setInStockFilter(true);
            if ($this->scopeConfig->getValue(
                self::XML_PATH_WISHLIST_LINK_USE_QTY,
                ScopeInterface::SCOPE_STORE
            )
            ) {
                $count = $collection->getItemsQty();
            } else {
                $count = $collection->count();
            }
            $this->_customerSession->setWishlistDisplayType(
                $this->scopeConfig->getValue(
                    self::XML_PATH_WISHLIST_LINK_USE_QTY,
                    ScopeInterface::SCOPE_STORE
                )
            );
            $this->_customerSession->setDisplayOutOfStockProducts(
                $this->scopeConfig->getValue(
                    self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK,
                    ScopeInterface::SCOPE_STORE
                )
            );
        }
        $this->_customerSession->setWishlistItemCount($count);
        $this->_eventManager->dispatch('wishlist_items_renewed');
        return $this;
    }

    /**
     * Should display item quantities in my wishlist link
     *
     * @return bool
     */
    public function isDisplayQty()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_WISHLIST_LINK_USE_QTY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve URL to item Product
     *
     * @param  Item|Product $item
     * @param  array $additional
     * @return string
     */
    public function getProductUrl($item, $additional = [])
    {
        if ($item instanceof Product) {
            $product = $item;
        } else {
            $product = $item->getProduct();
        }
        $buyRequest = $item->getBuyRequest();
        $fragment = [];
        if (is_object($buyRequest)) {
            $config = $buyRequest->getSuperProductConfig();
            if ($config && !empty($config['product_id'])) {
                $product = $this->productRepository->getById(
                    $config['product_id'],
                    false,
                    $this->_storeManager->getStore()->getStoreId()
                );
            }
            $fragment = $buyRequest->getSuperAttribute() ?? [];
            if ($buyRequest->getQty()) {
                $additional['_query']['qty'] = $buyRequest->getQty();
            }
        }
        $url = $product->getUrlModel()->getUrl($product, $additional);
        if ($fragment) {
            $url .= '#' . http_build_query($fragment);
        }

        return $url;
    }
}
