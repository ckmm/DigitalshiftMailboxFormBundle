<?php

namespace Digitalshift\MailboxFormBundle\Form\Type;

use Digitalshift\MailboxFormBundle\Form\DataTransformer\MimePartCollectionToArrayTransformer;
use Digitalshift\MailboxPersistenceBundle\Persistence\Attachment\PersistenceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * MessageContentType
 *
 * @author Soeren Helbig <Soeren.Helbig@digitalshift.de>
 * @copyright Digitalshift (c) 2014
 */
class MessageContentType extends AbstractType
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
     * @{inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentPlain', 'textarea')
            ->add('contentHtml', 'ckeditor')
            ->add('attachmentsEmbedded', 'collection', array(
                'type' => 'hidden',
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->add('attachmentsExtended', 'collection', array(
                'type' => 'text',
                'allow_add' => true,
                'allow_delete' => true
            ));

        $transformer = new MimePartCollectionToArrayTransformer($this->attachmentPersister);
        $builder->addModelTransformer($transformer);
    }

    /**
     * @{inheritdoc}
     */
    public function getName()
    {
        return 'digitalshift_mailbox_message_content';
    }
} 