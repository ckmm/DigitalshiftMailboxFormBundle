<?php

namespace Digitalshift\MailboxFormBundle\Form\DataTransformer;

use Digitalshift\MailboxAbstractionBundle\Entity\MessageHeaders;
use Digitalshift\MailboxAbstractionBundle\Entity\MessageMimePart;
use Digitalshift\MailboxAbstractionBundle\Entity\MessageMimePartCollection;
use Digitalshift\MailboxAbstractionBundle\Entity\Message;
use Digitalshift\MailboxPersistenceBundle\Persistence\Attachment\PersistenceInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * MimePartCollectionToArrayTransformer
 *
 * @author Soeren Helbig <Soeren.Helbig@digitalshift.de>
 * @copyright Digitalshift (c) 2014
 */
class MimePartCollectionToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var PersistenceInterface
     */
    private $attachmentPersister;

    /**
     * @param PersistenceInterface $attachmentPersister
     */
    public function __construct(PersistenceInterface $attachmentPersister)
    {
        $this->attachmentPersister = $attachmentPersister;
    }

    /**
     * Transforms an object (MessageMimePartCollection) to a array.
     *
     * @param $content MessageMimePartCollection
     * @return array
     */
    public function transform($content)
    {
        if (null === $content) {
            return array();
        }

        $contentPlain = $content->getPartsWithType(Message::MIME_TYPE_PLAIN);
        $contentHtml = $content->getPartsWithType(Message::MIME_TYPE_HTML);
        $attachmentsEmbedded = $content->getPartsWithAttachment(Message::ATTACHMENT_EMBEDDED);
        $attachmentsExtended = $content->getPartsWithAttachment(Message::ATTACHMENT_EXTENDED);

        $this->persistAttachments($attachmentsEmbedded);
        $this->persistAttachments($attachmentsExtended);

        $contentHtml = $this->replaceCids($contentHtml, $attachmentsEmbedded);

        $attachmentsEmbedded = $this->flattenAttachments($attachmentsEmbedded);
        $attachmentsExtended = $this->flattenAttachments($attachmentsExtended);

        return array(
            'contentPlain' => $contentPlain,
            'contentHtml' => $contentHtml,
            'attachmentsEmbedded' => $attachmentsEmbedded,
            'attachmentsExtended' => $attachmentsExtended
        );
    }

    /**
     * @param $attachmentsEmbedded
     */
    private function persistAttachments($attachmentsEmbedded)
    {
        /** @var MessageMimePart $attachment */
        foreach ($attachmentsEmbedded as $attachment) {
            $this->attachmentPersister->persist($attachment);
        }

    }

    /**
     * @param string $contentHtml
     * @param array $attachments
     *
     * @return string
     */
    private function replaceCids($contentHtml, $attachments)
    {
        $html = $contentHtml;

        /** @var MessageMimePart $attachment */
        foreach ($attachments as $attachment) {
            $cid = $attachment->getHeader('content-id');
            $html = str_replace('cid:'.$cid, $attachment->getPath(), $html);
        }

        return $html;
    }

    /**
     * @param array $attachments
     * @return array
     */
    private function flattenAttachments($attachments)
    {
        $attachmentPaths = array();

        /** @var MessageMimePart $attachment */
        foreach ($attachments as $attachment) {
            $attachmentPaths[] = $attachment->getPath();
        }

        return $attachmentPaths;
    }

    /**
     * Transforms a array to an MessageMimePartCollection.
     *
     * @param array $content
     *
     * @return MessageMimePartCollection|null
     *
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($content)
    {
        if (!$content) {
            return null;
        }

        return new MessageMimePartCollection(new MessageHeaders(), new MessageMimePartCollection(), 'INBOX', 1);;
    }
} 