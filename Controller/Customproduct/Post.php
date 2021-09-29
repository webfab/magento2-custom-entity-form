<?php

namespace Webfab\CustomProduct\Controller\Customproduct;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Webfab\CustomProduct\Mail\Template\TransportBuilder;
use Webfab\CustomProduct\Model\CustomProductFactory;
use Webfab\CustomProduct\Model\ResourceModel;

/**
 * Class Post
 *
 */
class Post extends Action
{

    /**
     * @var mixed
     */
    protected $formKeyValidator;

    /**
     * @var UrlInterface
     */
    protected $urlModel;

    /**
     * @var
     */
    protected $destinationFolder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomProductFactory
     */
    protected $customProductFactory;

    /**
     * @var ResourceModel\CustomProduct
     */
    protected $customProductResourceModel;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistorInterface;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;


    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * @param Context $context
     * @param UrlFactory $urlFactory
     * @param Validator $formKeyValidator
     * @param CustomProductFactory $customProductFactory
     * @param ResourceModel\CustomProduct $customProductResourceModel
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param DataPersistorInterface $dataPersistorInterface
     */
    public function __construct(
        Context                     $context,
        UrlFactory                  $urlFactory,
        Validator                   $formKeyValidator,
        CustomProductFactory        $customProductFactory,
        ResourceModel\CustomProduct $customProductResourceModel,
        StoreManagerInterface       $storeManager,
        ScopeConfigInterface        $scopeConfig,
        TransportBuilder            $transportBuilder,
        StateInterface              $inlineTranslation,
        DataPersistorInterface      $dataPersistorInterface
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->customProductFactory = $customProductFactory;
        $this->customProductResourceModel = $customProductResourceModel;
        $this->urlModel = $urlFactory->create();
        $this->dataPersistorInterface = $dataPersistorInterface;
        $this->formKeyValidator = $formKeyValidator ?: ObjectManager::getInstance()->get(Validator::class);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->getRequest()->isPost() || !$this->formKeyValidator->validate($this->getRequest())) {
            $url = $this->urlModel->getUrl('customproduct', ['_secure' => true]);
            $resultRedirect->setUrl($this->_redirect->error($url));
            return $resultRedirect;
        }

        $data = $this->getRequest()->getPostValue();

        if (!empty($data)) {
            $this->_saveAndSend($data);

            return $resultRedirect->setPath('*/*/success');
        }

        return $resultRedirect;
    }

    /**
     * SaveAndSend
     *
     * @param array $data
     */
    protected function _saveAndSend($data)
    {

        $model = $this->customProductFactory->create();
        $model->setData($data);

        try {
            $this->customProductResourceModel->save($model);
            $this->messageManager->addSuccessMessage(__('Your Request has been submitted'));
            // Send email
            $this->_sendEmail($data, 'admin');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong. Please try again'));
        }
    }

    /**
     * Send email
     *
     * @param array $data
     * @param string $to
     * @throws FileSystemException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    protected function _sendEmail($data, $to = 'admin')
    {
        $templateOptions = [
            'area' => Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        ];

        $setReplyTo = true;

        $notification_email = $this->scopeConfig->getValue(
            'customproduct/general/email',
            ScopeInterface::SCOPE_STORE
        );

        $clasEmail = $notification_email;

        $clasName = 'name test';

        if ($to == 'admin') {
            /**
             * Send mail to admin
             */
            $from = [
                'email' => $data['email'],
                'name' => $data['civilities'] . ' ' . $data['firstname'] . ' ' . $data['lastname']
            ];

            $toEmail = $clasEmail;
            $templateIdentifier = 'email_product_custom';
        } else {
            /**
             * Send mail to customer
             */
            $from = [
                'email' => $clasEmail,
                'name' => $clasName
            ];

            $toEmail = $data['email'];
            $templateIdentifier = 'contact_new_contact_email_to_customer';
        }

        //$body = ''; // Not used...
        $data['subject'] = 'Novap Produit Personnalisé : Nouvelle Demande';
        //$data['body'] = $body;

        $this->inlineTranslation->suspend();

        $this->transportBuilder
            ->setTemplateIdentifier($templateIdentifier)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($data)
            ->setFromByScope($from)
            ->addTo($toEmail);

        if ($setReplyTo) {
            $this->transportBuilder->setReplyTo($data['email']);
        }

        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
