<?php

namespace Digitalshift\MailboxFormBundle\Form\DataTransformer;

use Digitalshift\MailboxAbstractionBundle\Entity\MessageHeaders;
use Digitalshift\MailboxAbstractionBundle\Entity\MessageMimePartCollection;
use Digitalshift\MailboxAbstractionBundle\Entity\Message;
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

        return array(
            'contentPlain' => $content->getPartsWithType(Message::MIME_TYPE_PLAIN),
            'contentHtml' => $content->getPartsWithType(Message::MIME_TYPE_HTML),
            'attachmentsEmbeded' => array(),
            'attachmentsExtended' => array()
        );;
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param string $content
     *
     * @return Message|null
     *
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($content)
    {
        if (!$content) {
            return null;
        }

        return new Message(new MessageHeaders(), new MessageMimePartCollection(), 'INBOX', 1);;
    }
} 