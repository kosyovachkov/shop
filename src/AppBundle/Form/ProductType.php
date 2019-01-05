<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label"=>"Име на продукт",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('model', TextType::class, [
                "required"=>false,
                "label"=>"Модел",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('brand', TextType::class, [
                "required"=>false,
                "label"=>"Производител",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('length', IntegerType::class, [
                "required"=>false,
                "label"=>"Дължина",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('size', TextType::class, [
                "required"=>false,
                "label"=>"Размер",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('image', FileType::class, [
                'data_class' => null,
                "required"=>false,
                "label"=>"Добави снимка",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('description', TextareaType::class, [
                "label"=>"Описание",
                "attr"=>[
                    "style"=>"width:500px; height:150px",
                    "class"=>"form-control"
                ]
            ])
            ->add('quantity', IntegerType::class, [
                "label"=>"Количество",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('price', MoneyType::class, [
                "currency"=>false,
                "label"=>"Цена",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('promoPrice', MoneyType::class, [
                "currency"=>false,
                "required"=>false,
                "label"=>"Промоционална цена",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('featured', CheckboxType::class, [
                "required"=>false,
                "label"=>"Препоръчано",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('category', EntityType::class, [
                "class"=>"AppBundle\Entity\Category",
                "choice_label"=>"name",
                "placeholder"=>'Избери категория...',
                "label"=>"Категория",
                "attr"=>[
                    "class"=>"form-control"
                ]
            ])
            ->add('submit', SubmitType::class, [
                "attr"=>[
                    "class"=>"btn btn-primary"
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product'
        ));
    }

}
