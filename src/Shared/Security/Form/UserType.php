<?php

declare(strict_types=1);

namespace App\Shared\Security\Form;

use App\Shared\Security\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isCreation = (bool) $options['is_creation'];

        $builder
            ->add('email', EmailType::class, [
                'label' => 'user.form.email',
                'constraints' => [new Assert\NotBlank(), new Assert\Email(), new Assert\Length(max: 180)],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'user.form.firstName',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 100)],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'user.form.lastName',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 100)],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'user.form.roles',
                'choices' => [
                    'user.form.role.admin' => 'ROLE_ADMIN',
                    'user.form.role.super_admin' => 'ROLE_SUPER_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'help' => 'user.form.roles_help',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $isCreation ? 'user.form.password' : 'user.form.password_change',
                'mapped' => false,
                'required' => $isCreation,
                'help' => $isCreation ? null : 'user.form.password_help',
                'constraints' => $isCreation
                    ? [new Assert\NotBlank(), new Assert\Length(min: 8)]
                    : [new Assert\When(expression: 'value != ""', constraints: [new Assert\Length(min: 8)])],
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'user.form.active',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
                'is_creation' => false,
                'empty_data' => static function (FormInterface $form): User {
                    return new User(
                        (string) $form->get('email')->getData(),
                        (string) $form->get('firstName')->getData(),
                        (string) $form->get('lastName')->getData(),
                    );
                },
            ])
            ->setAllowedTypes('is_creation', 'bool')
        ;
    }
}
