<?php

declare(strict_types=1);

namespace App\Page\Form;

use App\Page\Entity\Page;
use App\Shared\Content\Form\JsonBlocksTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'page.form.title',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 200)],
            ])
            ->add('path', TextType::class, [
                'label' => 'page.form.path',
                'help' => 'page.form.path_help',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 220),
                    new Assert\Regex(
                        pattern: '#^[a-z0-9]+(?:[-/][a-z0-9]+)*$#',
                        message: 'page.form.path_invalid',
                    ),
                ],
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'page.form.excerpt',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('content', HiddenType::class, [
                'required' => false,
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'page.form.publishedAt',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'help' => 'page.form.publishedAt_help',
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'page.form.metaTitle',
                'required' => false,
                'help' => 'page.form.metaTitle_help',
                'constraints' => [new Assert\Length(max: 200)],
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'page.form.metaDescription',
                'required' => false,
                'help' => 'page.form.metaDescription_help',
                'attr' => ['rows' => 3, 'maxlength' => 320],
            ])
        ;

        $builder->get('content')->addModelTransformer(new JsonBlocksTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'empty_data' => static function (FormInterface $form): Page {
                return new Page(
                    (string) ($form->get('title')->getData() ?? ''),
                    (string) ($form->get('path')->getData() ?? ''),
                );
            },
        ]);
    }
}
