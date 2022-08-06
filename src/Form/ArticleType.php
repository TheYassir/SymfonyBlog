<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true, 
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>"Saisir un titre",
                ],
                'label' => 'Titre'
            ])
            ->add('content', TextareaType::class,[
                'label' => 'Description :', 
                'required' => true, 
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>"Saisir une description", 
                    'rows' => 5
                ]
            ])
            ->add('cover', FileType::class,[
                'required' => false, 
                "mapped" => false,
                "data_class" => null,
                'label' => 'Uploader une photo :', 
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M', 
                        'mimeTypes' => [
                            'image/jpeg', 
                            'image/png', 
                            'image/jpg'
                        ],
                    "mimeTypesMessage" => 'Format autorisés : jpg/png/jpeg.'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
