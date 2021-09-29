<?php

namespace Webfab\CustomProduct\Controller\Customproduct;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Success
 */
class Success extends Action
{
    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        return $this->resultFactory->create($this->resultFactory::TYPE_PAGE);
    }
}
