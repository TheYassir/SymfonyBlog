<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if($options['userBack'] == true)
        {
            $builder
            ->add('roles', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'User' => '',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Le Role de l\'utilisateur'
            ]);

             // Data transformer
            $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
        } elseif($options['userFront'] == true) {
            $builder
                ->add('email', EmailType::class, [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'Email'
                ])
                ->add('plainPassword', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control'
                    ],
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrer un mot de passe.',
                        ]),
                        new Length([
                            'min' => 5,
                            'minMessage' => 'Votre mot de passe doit faire au minimum 5 caractères',
                            'maxMessage' => 'Votre mot de passe doit faire au maximum 24 caractères',
                            'max' => 24,
                        ]),
                    ],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'userBack' => false,
            'userFront' => false
        ]);
    }
}
