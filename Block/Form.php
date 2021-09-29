<?php

namespace Webfab\CustomProduct\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Form
 */
class Form extends Template
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistorInterface;

    /**
     * Customer session
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Form constructor.
     *
     * @param Template\Context $context
     * @param DataPersistorInterface $dataPersistorInterface
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Template\Context       $context,
        DataPersistorInterface $dataPersistorInterface,
        Session                $customerSession,
        array                  $data = []
    )
    {
        parent::__construct($context, $data);
        $this->dataPersistorInterface = $dataPersistorInterface;
        $this->customerSession = $customerSession;
    }

    /**
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }

        $customer = $this->customerSession->getCustomerDataObject();
        return $customer->getEmail();
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }

        $customer = $this->customerSession->getCustomerDataObject();

        return $customer->getFirstname();
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }
        $customer = $this->customerSession->getCustomerDataObject();
        return $customer->getLastname();
    }
}
