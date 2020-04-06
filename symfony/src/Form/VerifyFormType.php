<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VerifyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('verificationCode', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control form-control-lg'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a verification code',
                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 4,
                        'minMessage' => 'The verification code is a 4 digit number.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
