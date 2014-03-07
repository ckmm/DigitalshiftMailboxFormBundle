<?php

namespace Digitalshift\MailboxFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * MessageType
 *
 * @author Soeren Helbig <Soeren.Helbig@digitalshift.de>
 * @copyright Digitalshift (c) 2014
 */
class MessageType extends AbstractType
{
    const TYPE_NAME = 'digitalshift_mailbox_message';
    const DATA_CLASS = 'Digitalshift\MailboxAbstractionBundle\Entity\Message';

    /**
     * @{inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('mailboxPath', 'hidden')
            ->add('mailboxUid', 'hidden')
            ->add('content', 'digitalshift_mailbox_message_content');
    }

    /**
     * @{inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => self::DATA_CLASS,
        ));
    }

    /**
     * @{inheritdoc}
     */
    public function getName()
    {
        return self::TYPE_NAME;
    }
} 