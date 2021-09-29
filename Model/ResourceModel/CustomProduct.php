<?php

namespace Webfab\CustomProduct\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CustomProduct
 *
 */
class CustomProduct extends AbstractDb
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('custom_product_request', 'request_id');
    }
}
