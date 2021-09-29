<?php

namespace Webfab\CustomProduct\Block\Adminhtml;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\View\Element\Template;
use Webfab\CustomProduct\Model\CustomProductFactory as ModelFactory;
use Webfab\CustomProduct\Model\ResourceModel\CustomProduct as ResourceModel;

/**
 * Class View
 */
class View extends Template
{

    protected $_resourceModel;


    protected $_modelFactory;


    protected $_model = null;


    /**
     * @var DateTimeFactory
     */
    protected $_dateTimeFactory;


    /**
     * @var Session
     */
    protected $customerSession;


    public function __construct(
        Template\Context $context,
        DateTimeFactory  $dateTimeFactory,
        ResourceModel    $resourceModel,
        ModelFactory     $modelFactory,
        Session          $customerSession
    )
    {
        parent::__construct($context);

        $this->_resourceModel = $resourceModel;
        $this->_modelFactory = $modelFactory;
        $this->_dateTimeFactory = $dateTimeFactory;
        $this->customerSession = $customerSession;
        $this->getCustomProductRequest();
    }


    /**
     * @return \Webfab\CustomProduct\Model\CustomProduct
     */
    public function getCustomProductRequest()
    {
        if ($this->_model === null) {
            $id = $this->getRequest()->getParam('request_id');
            $this->_model = $this->_modelFactory->create();
            $this->_resourceModel->load($this->_model, $id);
        }
        return $this->_model;
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        $product_type = $this->_model->getProductType();
        $options = [
            'panneaux' => 'Panneaux',
            'plaquettes_de_portes' => 'Plaquettes de portes',
            'chevalet_de_signalisation' => 'Chevalet de signalisation',
            'poteaux_a_angle' => 'Poteaux à sangle',
        ];
        return $options[$product_type] ?? 'N.C.';
    }

    /**
     * Get formated date
     *
     * @param string $date
     * @return string
     */
    public function getFormatedDate($date = null)
    {
        if ($this->getCustomProductRequest()->getCreatedAt()) {
            $date = $this->getCustomProductRequest()->getCreatedAt();
        }
        return $this->_dateTimeFactory->create($date)->format('d-m-Y');
    }
}
