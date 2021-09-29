<?php

namespace Webfab\CustomProduct\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Contact
 *
 */
class CustomProduct extends AbstractModel
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\CustomProduct::class);
    }
}
