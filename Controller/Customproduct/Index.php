<?php

namespace Webfab\CustomProduct\Controller\Customproduct;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;

/**
 * Class Index
 *
 * @package Webfab\CustomProduct\Controller\Contact
 * @author Frédéric TISSOT <ftissot@agencenetdesign.com>
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
        $type = $this->getRequest()->getParam('type');
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create($this->resultFactory::TYPE_PAGE);

        $title = __('Produits personnalisés');

        $resultPage->getConfig()->getTitle()->set($title);

        /** @var Subtitle $subTitleBlock */
        //$subTitleBlock = $resultPage->getLayout()->getBlock('page.main.subtitle');
        //$subTitleBlock->setPageSubtitle($subtitle);

        return $resultPage;
    }
}
