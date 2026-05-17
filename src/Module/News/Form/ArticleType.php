<?php

declare(strict_types=1);

namespace App\Module\News\Form;

use App\Module\News\Entity\Article;
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
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'news.form.title',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 200)],
            ])
            ->add('slug', TextType::class, [
                'label' => 'news.form.slug',
                'required' => false,
                'help' => 'news.form.slug_help',
                'constraints' => [new Assert\Length(max: 220), new Assert\Regex(pattern: '/^[a-z0-9-]*$/', message: 'news.form.slug_invalid')],
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'news.form.excerpt',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('content', HiddenType::class, [
                'required' => false,
            ])
            ->add('coverFile', VichImageType::class, [
                'label' => 'news.form.cover',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'image_uri' => false,
                'help' => 'news.form.cover_help',
                'attr' => ['data-controller' => 'image-preview', 'accept' => 'image/png,image/jpeg,image/webp'],
                'constraints' => [
                    new Assert\Image(maxSize: '5M', mimeTypes: ['image/png', 'image/jpeg', 'image/webp']),
                ],
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'news.form.publishedAt',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'help' => 'news.form.publishedAt_help',
            ])
        ;

        $builder->get('content')->addModelTransformer(new JsonBlocksTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'empty_data' => static function (FormInterface $form): Article {
                return new Article(
                    (string) ($form->get('title')->getData() ?? ''),
                    (string) ($form->get('slug')->getData() ?? ''),
                );
            },
        ]);
    }
}
