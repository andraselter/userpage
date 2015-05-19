<?php

namespace Acme\DemoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('full_name', 'text');
        $builder->add('email', 'email');
        $builder->add('phone', 'text');

       /* $builder->add('user', 'collection', array(
            'type' => new UserType(),
            'allow_add'    => true,
            'allow_delete'    => true,
            'by_reference' => false,
        ));*/

        $builder->add('password', 'repeated', array(
            'first_name'  => 'password',
            'second_name' => 'confirm',
            'type'        => 'password',
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\DemoBundle\Entity\User',

        ));
    }

    public function getName()
    {
        return 'user';
    }
}