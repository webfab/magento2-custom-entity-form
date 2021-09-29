<?php

namespace Webfab\CustomProduct\Model\ResourceModel\CustomProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Webfab\CustomProduct\Model;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(Model\CustomProduct::class, Model\ResourceModel\CustomProduct::class);
    }
}
