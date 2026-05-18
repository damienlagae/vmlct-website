<?php

declare(strict_types=1);

namespace App\Module\Programme\Form;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Entity\RaceCategory;
use App\Module\Programme\Entity\RaceDiscipline;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class RaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'programme.form.name',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 200)],
            ])
            ->add('startsAt', DateTimeType::class, [
                'label' => 'programme.form.startsAt',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'constraints' => [new Assert\NotNull()],
            ])
            ->add('location', TextType::class, [
                'label' => 'programme.form.location',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 150)],
            ])
            ->add('discipline', EnumType::class, [
                'label' => 'programme.form.discipline',
                'class' => RaceDiscipline::class,
                'choice_label' => static fn (RaceDiscipline $d): string => 'programme.discipline.'.$d->value,
            ])
            ->add('categories', EnumType::class, [
                'label' => 'programme.form.categories',
                'class' => RaceCategory::class,
                'choice_label' => static fn (RaceCategory $c): string => 'programme.category.'.$c->value,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'programme.form.description',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('externalUrl', UrlType::class, [
                'label' => 'programme.form.externalUrl',
                'required' => false,
                'help' => 'programme.form.externalUrl_help',
                'constraints' => [new Assert\Length(max: 500), new Assert\Url(requireTld: true)],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Race::class,
            'empty_data' => static function (FormInterface $form): Race {
                $startsAt = $form->get('startsAt')->getData();
                if (!$startsAt instanceof \DateTimeImmutable) {
                    $startsAt = new \DateTimeImmutable();
                }
                $discipline = $form->get('discipline')->getData();
                if (!$discipline instanceof RaceDiscipline) {
                    $discipline = RaceDiscipline::Road;
                }

                return new Race(
                    (string) ($form->get('name')->getData() ?? ''),
                    $startsAt,
                    (string) ($form->get('location')->getData() ?? ''),
                    $discipline,
                );
            },
        ]);
    }
}
