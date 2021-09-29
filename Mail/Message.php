<?php

namespace Webfab\CustomProduct\Mail;

use Magento\Framework\Mail\MailMessageInterface;
use Zend\Mail\Message as MailMessage;
use Zend\Mail\MessageFactory as MailMessageFactory;
use Zend\Mime\MessageFactory as MimeMessageFactory;
use Zend\Mime\Mime;
use Zend\Mime\Part;
use Zend\Mime\PartFactory;

/**
 * Class Message
 *
 */
class Message implements MailMessageInterface
{
    /**
     * @var PartFactory
     */
    protected $partFactory;

    /**
     * @var MimeMessageFactory
     */
    protected $mimeMessageFactory;

    /**
     * @var MailMessage
     */
    private $zendMessage;

    /**
     * @var Part[]
     */
    protected $parts = [];

    /**
     * Message constructor.
     *
     * @param PartFactory $partFactory
     * @param MimeMessageFactory $mimeMessageFactory
     * @param string $charset
     */
    public function __construct(
        PartFactory        $partFactory,
        MimeMessageFactory $mimeMessageFactory,
                           $charset = 'utf-8'
    )
    {
        $this->partFactory = $partFactory;
        $this->mimeMessageFactory = $mimeMessageFactory;
        $this->zendMessage = MailMessageFactory::getInstance();
        $this->zendMessage->setEncoding($charset);
    }

    /**
     * Add the HTML mime part to the message.
     *
     * @param string $content
     * @return $this
     */
    public function setBodyText($content)
    {
        $textPart = $this->partFactory->create();

        $textPart->setContent($content)
            ->setType(Mime::TYPE_TEXT)
            ->setCharset($this->zendMessage->getEncoding());

        $this->parts[] = $textPart;

        return $this;
    }

    /**
     * Add the text mime part to the message.
     *
     * @param string $content
     * @return $this
     */
    public function setBodyHtml($content)
    {
        $htmlPart = $this->partFactory->create();

        $htmlPart->setContent($content)
            ->setType(Mime::TYPE_HTML)
            ->setCharset($this->zendMessage->getEncoding());

        $this->parts[] = $htmlPart;

        return $this;
    }

    /**
     * Add the attachment mime part to the message.
     *
     * @param string $content
     * @param string $fileName
     * @param string $fileType
     * @return $this
     */
    public function setBodyAttachment($content, $fileName, $fileType)
    {
        $attachmentPart = $this->partFactory->create();

        $attachmentPart->setContent($content)
            ->setType($fileType)
            ->setFileName($fileName)
            ->setDisposition(Mime::DISPOSITION_ATTACHMENT);
        $attachmentPart->setEncoding(Mime::ENCODING_BASE64);

        $this->parts[] = $attachmentPart;

        return $this;
    }

    /**
     * Set parts to Zend message body.
     *
     * @return $this
     */
    public function setPartsToBody()
    {
        $mimeMessage = $this->mimeMessageFactory->create();
        $mimeMessage->setParts($this->parts);
        $this->zendMessage->setBody($mimeMessage);

        return $this;
    }

    /**
     * SetBody
     *
     * @param mixed $body
     *
     * @return $this|MailMessageInterface
     */
    public function setBody($body)
    {
        return $this;
    }

    /**
     * SetSubject
     *
     * @param string $subject
     *
     * @return $this|MailMessageInterface
     */
    public function setSubject($subject)
    {
        $this->zendMessage->setSubject($subject);

        return $this;
    }

    /**
     * GetSubject
     *
     * @return string|null
     */
    public function getSubject()
    {
        return $this->zendMessage->getSubject();
    }

    /**
     * GetBody
     *
     * @return object|string|\Zend\Mime\Message
     */
    public function getBody()
    {
        return $this->zendMessage->getBody();
    }

    /**
     * SetFrom
     *
     * @param array|string $fromAddress
     *
     * @return $this|MailMessageInterface
     */
    public function setFrom($fromAddress)
    {
        $this->zendMessage->setFrom($fromAddress);

        return $this;
    }

    /**
     * SetFromAddress
     *
     * @param array|string $fromAddress
     *
     * @return $this|MailMessageInterface
     */
    public function setFromAddress($fromAddress)
    {
        return $this->setFrom($fromAddress);
    }

    /**
     * AddTo
     *
     * @param array|string $toAddress
     *
     * @return $this|MailMessageInterface
     */
    public function addTo($toAddress)
    {
        $this->zendMessage->addTo($toAddress);

        return $this;
    }

    /**
     * AddCc
     *
     * @param array|string $ccAddress
     *
     * @return $this|MailMessageInterface
     */
    public function addCc($ccAddress)
    {
        $this->zendMessage->addCc($ccAddress);

        return $this;
    }

    /**
     * AddBcc
     *
     * @param array|string $bccAddress
     *
     * @return $this|MailMessageInterface
     */
    public function addBcc($bccAddress)
    {
        $this->zendMessage->addBcc($bccAddress);

        return $this;
    }

    /**
     * SetReplyTo
     *
     * @param array|string $replyToAddress
     *
     * @return $this|MailMessageInterface
     */
    public function setReplyTo($replyToAddress)
    {
        $this->zendMessage->setReplyTo($replyToAddress);

        return $this;
    }

    /**
     * GetRawMessage
     *
     * @return string
     */
    public function getRawMessage()
    {
        return $this->zendMessage->toString();
    }

    /**
     * SetMessageType
     *
     * @param string $type
     *
     * @return $this|MailMessageInterface
     */
    public function setMessageType($type)
    {
        return $this;
    }
}
