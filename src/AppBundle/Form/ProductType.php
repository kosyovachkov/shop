<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\DecimalType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('name', TextType::class)
            ->add('model', TextType::class)
            ->add('brand', TextType::class)
            ->add('length', IntegerType::class)
            ->add('size', TextType::class)
            ->add('image', TextType::class)
            ->add('description', TextType::class)
            ->add('quantity', IntegerType::class)
            ->add('price', MoneyType::class)
            ->add('promoPrice', MoneyType::class)
            ->add('featured', CheckboxType::class)
            ->add('category', EntityType::class, [
                "class"=>"AppBundle\Entity\Category",
                "choice_label"=>"name"
            ])
            ->add('submit', SubmitType::class);
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
