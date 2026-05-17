<?php

declare(strict_types=1);

namespace App\Module\Team\Form;

use App\Module\Team\Entity\Staff;
use App\Module\Team\Entity\StaffRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class StaffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'team.form.firstName',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 100)],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'team.form.lastName',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 100)],
            ])
            ->add('role', EnumType::class, [
                'label' => 'team.form.role',
                'class' => StaffRole::class,
                'choice_label' => fn (StaffRole $r): string => 'team.staff_role.'.$r->value,
            ])
            ->add('photoFile', VichImageType::class, [
                'label' => 'team.form.photo',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'image_uri' => true,
                'imagine_pattern' => 'team_photo',
                'help' => 'team.form.photo_help',
                'attr' => ['data-controller' => 'image-preview', 'accept' => 'image/png,image/jpeg,image/webp'],
                'constraints' => [
                    new Assert\Image(
                        maxSize: '4M',
                        mimeTypes: ['image/png', 'image/jpeg', 'image/webp'],
                    ),
                ],
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'team.form.active',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Staff::class,
            'empty_data' => static function (FormInterface $form): Staff {
                return new Staff(
                    (string) ($form->get('firstName')->getData() ?? ''),
                    (string) ($form->get('lastName')->getData() ?? ''),
                    $form->get('role')->getData() ?? StaffRole::Coach,
                );
            },
        ]);
    }
}
