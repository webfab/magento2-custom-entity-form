<?php

namespace Webfab\CustomProduct\Controller\Adminhtml\Customproduct;

use Exception;
use Magento\Backend\App\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Webfab\CustomProduct\Model\CustomProductFactory as ModelFactory;
use Webfab\CustomProduct\Model\ResourceModel\CustomProduct as ResourceModel;

/**
 * Class View
 */
class View extends Action
{
    /**
     * @var ResourceModel
     */
    protected $_resourceModel;

    /**
     * @var ModelFactory
     */
    protected $_modelFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Session $customerSession
     * @param ResourceModel $resourceModel
     * @param ModelFactory $modelFactory
     */
    public function __construct(
        Action\Context $context,
        Session        $customerSession,
        ResourceModel  $resourceModel,
        ModelFactory   $modelFactory
    )
    {
        parent::__construct($context);
        $this->_resourceModel = $resourceModel;
        $this->_modelFactory = $modelFactory;
    }

    /**
     * Execute
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {

        $id = $this->getRequest()->getParam('request_id');

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            $model = $this->_modelFactory->create();

            try {
                $this->_resourceModel->load($model, $id);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while loading'));
                return $resultRedirect->setPath('*/*/');
            }

            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('Object ID does not exist'));
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $this->messageManager->addErrorMessage(__('No ID provided'));

            return $resultRedirect->setPath('*/*/');
        }

        return $this->resultFactory->create($this->resultFactory::TYPE_PAGE);
    }
}
