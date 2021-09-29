<?php

namespace Webfab\CustomProduct\Controller\Adminhtml\Customproduct;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Webfab\CustomProduct\Model\CustomProductFactory as ModelFactory;
use Webfab\CustomProduct\Model\ResourceModel\CustomProduct as ResourceModel;

/**
 * Class Delete
 */
class Delete extends Action
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
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::content';

    /**
     * @param Action\Context $context
     * @param ResourceModel $resourceModel
     * @param ModelFactory $modelFactory
     */
    public function __construct(
        Action\Context $context,
        ResourceModel  $resourceModel,
        ModelFactory   $modelFactory
    )
    {
        parent::__construct($context);
        $this->_resourceModel = $resourceModel;
        $this->_modelFactory = $modelFactory;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_modelFactory->create();

                $this->_resourceModel->load($model, $id);
                $this->_resourceModel->delete($model);

                $this->messageManager->addSuccessMessage(__(' deleted'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('ID parameter is missing'));
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
