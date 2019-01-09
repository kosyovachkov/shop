<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('firstName', TextType::class, [
                "label" => "Име",
                "attr" => [
                    "class" => "form-control",
                ]
            ])
            ->add('lastName', TextType::class, [
                "label" => "Фамилия",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('password', RepeatedType::class, [
                "label" => "Парола",
                'type' => PasswordType::class,
                'invalid_message' => 'Двете пароли трябва да съвпадат.',
                'options' => [
                    'attr' => [
                        'class' => 'form-control password-field'
                    ]
                ],
                'required' => true,
                'first_options' => ['label' => 'Въведи нова парола'],
                'second_options' => ['label' => 'Повтори паролата']
            ])
            ->add('address', TextareaType::class, [
                "label" => "Адрес",
                "attr" => [
                    "style" => "width:500px; height:150px",
                    "class" => "form-control"
                ]
            ])
            ->add('phone', TextType::class, [
                "label"=>"Телефон",
                "attr"=>[
                    "class"=>"form-control"
                ]]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

}
