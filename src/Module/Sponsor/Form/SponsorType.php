<?php

declare(strict_types=1);

namespace App\Module\Sponsor\Form;

use App\Module\Sponsor\Entity\Sponsor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class SponsorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'sponsor.form.name',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 150)],
            ])
            ->add('websiteUrl', UrlType::class, [
                'label' => 'sponsor.form.websiteUrl',
                'required' => false,
                'constraints' => [new Assert\Length(max: 500)],
            ])
            ->add('logoFile', VichImageType::class, [
                'label' => 'sponsor.form.logo',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'image_uri' => false,
                'help' => 'sponsor.form.logo_help',
                'attr' => ['data-controller' => 'image-preview', 'accept' => 'image/png,image/jpeg,image/webp,image/svg+xml'],
                'constraints' => [
                    new Assert\Image(
                        maxSize: '2M',
                        mimeTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                    ),
                ],
            ])
            ->add('displayOrder', IntegerType::class, [
                'label' => 'sponsor.form.displayOrder',
                'help' => 'sponsor.form.displayOrder_help',
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'sponsor.form.active',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sponsor::class,
            'empty_data' => static function (FormInterface $form): Sponsor {
                $name = $form->get('name')->getData();

                return new Sponsor(\is_string($name) && '' !== $name ? $name : 'Untitled');
            },
        ]);
    }
}
