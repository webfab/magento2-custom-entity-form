<?php
/**
 * MIME Mail Template Transport Builder
 *
 */

namespace Webfab\CustomProduct\Mail\Template;

use Laminas\Mime\Mime;
use Laminas\Mime\PartFactory;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\MimeInterface;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;

/**
 * TransportBuilder for MIME Mail Templates
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{

    /**
     * Template Identifier
     *
     * @var string
     */
    protected $templateIdentifier;

    /**
     * Template Model
     *
     * @var string
     */
    protected $templateModel;

    /**
     * Template Variables
     *
     * @var array
     */
    protected $templateVars;

    /**
     * Template Options
     *
     * @var array
     */
    protected $templateOptions;

    /**
     * Mail Transport
     *
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var FactoryInterface
     */
    protected $templateFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Message
     *
     * @var MessageInterface
     */
    protected $message;

    /**
     * @var SenderResolverInterface
     */
    protected $_senderResolver;

    /**
     * @var TransportInterfaceFactory
     */
    protected $mailTransportFactory;

    /**
     * @var array
     */
    private $messageData = [];

    /**
     * @var EmailMessageInterfaceFactory|mixed
     */
    private $emailMessageInterfaceFactory;

    /**
     * @var MimeMessageInterfaceFactory|mixed
     */
    private $mimeMessageInterfaceFactory;

    /**
     * @var MimePartInterfaceFactory|mixed
     */
    private $mimePartInterfaceFactory;

    /**
     * @var AddressConverter|mixed
     */
    private $addressConverter;
    /**
     * @var array
     */
    protected $attachments = [];

    /**
     * @var PartFactory
     */
    protected $partFactory;

    /**
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param MessageInterfaceFactory|null $messageFactory
     * @param EmailMessageInterfaceFactory|null $emailMessageInterfaceFactory
     * @param MimeMessageInterfaceFactory|null $mimeMessageInterfaceFactory
     * @param MimePartInterfaceFactory|null $mimePartInterfaceFactory
     * @param AddressConverter|null $addressConverter
     * @param PartFactory $partFactory
     */
    public function __construct(
        FactoryInterface             $templateFactory,
        MessageInterface             $message,
        SenderResolverInterface      $senderResolver,
        ObjectManagerInterface       $objectManager,
        TransportInterfaceFactory    $mailTransportFactory,
        MessageInterfaceFactory      $messageFactory = null,
        EmailMessageInterfaceFactory $emailMessageInterfaceFactory = null,
        MimeMessageInterfaceFactory  $mimeMessageInterfaceFactory = null,
        MimePartInterfaceFactory     $mimePartInterfaceFactory = null,
        AddressConverter             $addressConverter = null,
        PartFactory                  $partFactory
    )
    {
        $this->templateFactory = $templateFactory;
        $this->objectManager = $objectManager;
        $this->_senderResolver = $senderResolver;
        $this->mailTransportFactory = $mailTransportFactory;
        $this->partFactory = $partFactory;
        $this->emailMessageInterfaceFactory = $emailMessageInterfaceFactory ?: $this->objectManager
            ->get(EmailMessageInterfaceFactory::class);
        $this->mimeMessageInterfaceFactory = $mimeMessageInterfaceFactory ?: $this->objectManager
            ->get(MimeMessageInterfaceFactory::class);
        $this->mimePartInterfaceFactory = $mimePartInterfaceFactory ?: $this->objectManager
            ->get(MimePartInterfaceFactory::class);
        $this->addressConverter = $addressConverter ?: $this->objectManager
            ->get(AddressConverter::class);
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory,
            $messageFactory,
            $emailMessageInterfaceFactory,
            $mimeMessageInterfaceFactory,
            $mimePartInterfaceFactory,
            $addressConverter
        );
    }

    /**
     * @param array|string $address
     * @param string $name
     * @return $this|TransportBuilder
     */
    public function addCc($address, $name = '')
    {
        $this->addAddressByType('cc', $address, $name);

        return $this;
    }

    /**
     * @param array|string $address
     * @param string $name
     * @return $this|TransportBuilder
     */
    public function addTo($address, $name = '')
    {
        $this->addAddressByType('to', $address, $name);

        return $this;
    }

    /**
     * @param array|string $address
     * @return $this|TransportBuilder
     */
    public function addBcc($address)
    {
        $this->addAddressByType('bcc', $address);

        return $this;
    }

    /**
     *
     */
    public function setReplyTo($email, $name = null)
    {
        $this->addAddressByType('replyTo', $email, $name);

        return $this;
    }

    /**
     *
     */
    public function setFrom($from)
    {
        return $this->setFromByScope($from);
    }

    /**
     *
     */
    public function setFromByScope($from, $scopeId = null)
    {
        $result = $this->_senderResolver->resolve($from, $scopeId);
        $this->addAddressByType('from', $result['email'], $result['name']);

        return $this;
    }

    /**
     *
     */
    public function setTemplateIdentifier($templateIdentifier)
    {
        $this->templateIdentifier = $templateIdentifier;

        return $this;
    }

    /**
     * @param string $templateModel
     * @return $this|TransportBuilder
     */
    public function setTemplateModel($templateModel)
    {
        $this->templateModel = $templateModel;
        return $this;
    }

    /**
     * @param array $templateVars
     * @return $this|TransportBuilder
     */
    public function setTemplateVars($templateVars)
    {
        $this->templateVars = $templateVars;

        return $this;
    }

    /**
     * @param array $templateOptions
     * @return $this|TransportBuilder
     */
    public function setTemplateOptions($templateOptions)
    {
        $this->templateOptions = $templateOptions;

        return $this;
    }

    /**
     * @return \Magento\Framework\Mail\TransportInterface
     * @throws LocalizedException
     */
    public function getTransport()
    {
        try {
            $this->prepareMessage();
            $mailTransport = $this->mailTransportFactory->create(['message' => clone $this->message]);
        } finally {
            $this->reset();
        }

        return $mailTransport;
    }

    /**
     * @return $this|TransportBuilder
     */
    protected function reset()
    {
        $this->messageData = [];
        $this->templateIdentifier = null;
        $this->templateVars = null;
        $this->templateOptions = null;
        return $this;
    }

    /**
     * @return \Magento\Framework\Mail\TemplateInterface
     */
    protected function getTemplate()
    {
        return $this->templateFactory->get($this->templateIdentifier, $this->templateModel)
            ->setVars($this->templateVars)
            ->setOptions($this->templateOptions);
    }

    /**
     * @return $this|TransportBuilder
     * @throws LocalizedException
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate();
        $content = $template->processTemplate();
        switch ($template->getType()) {
            case TemplateTypesInterface::TYPE_TEXT:
                $part['type'] = MimeInterface::TYPE_TEXT;
                break;

            case TemplateTypesInterface::TYPE_HTML:
                $part['type'] = MimeInterface::TYPE_HTML;
                break;

            default:
                throw new LocalizedException(
                    new Phrase('Unknown template type')
                );
        }
        $mimePart = $this->mimePartInterfaceFactory->create(['content' => $content]);
        $parts = count($this->attachments) ? array_merge([$mimePart], $this->attachments) : [$mimePart];
        $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(
            ['parts' => $parts]
        );

        $this->messageData['subject'] = html_entity_decode(
            (string)$template->getSubject(),
            ENT_QUOTES
        );
        $this->message = $this->emailMessageInterfaceFactory->create($this->messageData);

        return $this;
    }

    /**
     * @param string $addressType
     * @param array|string $email
     * @param null $name
     */
    private function addAddressByType(string $addressType, $email, $name = null): void
    {
        if (is_string($email)) {
            $this->messageData[$addressType][] = $this->addressConverter->convert($email, $name);
            return;
        }
        $convertedAddressArray = $this->addressConverter->convertMany($email);
        if (isset($this->messageData[$addressType])) {
            $this->messageData[$addressType] = array_merge(
                $this->messageData[$addressType],
                $convertedAddressArray
            );
        }
    }

    /**
     * @param $content
     * @param $fileName
     * @param $fileType
     * @return $this
     */
    public function addAttachment($content, $fileName, $fileType): TransportBuilder
    {
        $attachmentPart = $this->partFactory->create();
        $attachmentPart->setContent($content)
            ->setType($fileType)
            ->setFileName($fileName)
            ->setDisposition(Mime::DISPOSITION_ATTACHMENT)
            ->setEncoding(Mime::ENCODING_BASE64);
        $this->attachments[] = $attachmentPart;

        return $this;
    }
}
