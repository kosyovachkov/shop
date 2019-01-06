<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                "label"=>"Email",
                "attr"=>[
                    "class"=>"form-control"
                ]])
            ->add('subject', TextType::class, [
                "label"=>"Тема на съобщението",
                "attr"=>[
                    "class"=>"form-control"
                ]])
            ->add('phone', TextType::class, [
                "label"=>"Телефон за обратна връзка",
                "attr"=>[
                    "class"=>"form-control"
                ]])
            ->add('content', TextareaType::class,[
                "label"=>"Текст на съобщението",
                "attr"=>[
                    "class"=>"form-control"
                ]]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Message'
        ));
    }

}
