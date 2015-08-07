<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Controller of data option selection
 */
class DataOption extends AbstractActionController
{
    /**
     * @return ViewModel|\Zend\Http\Response
     */
    public function indexAction()
    {
        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setTemplate('/magento/setup/data-option.phtml');
        return $view;
    }
}
