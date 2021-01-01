<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('summary', TextType::class)
            ->add('releaseDate', TextType::class)
            ->add('category', CategoryType::class)
            ->add(
                'tags',
                CollectionType::class,
                ["entry_type" => TagType::class, "allow_add" => true, "allow_delete" => true]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Book::class,
                'csrf_protection' => false,
            ]
        );
    }
}
