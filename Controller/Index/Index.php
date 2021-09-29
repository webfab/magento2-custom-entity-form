<?php

namespace Webfab\CustomProduct\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * CustomProduct Index
 *
 */
class Index extends Action
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
