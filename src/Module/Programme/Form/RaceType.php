<?php

declare(strict_types=1);

namespace App\Module\Programme\Form;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Entity\RaceCategory;
use App\Module\Programme\Entity\RaceDiscipline;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('startDate', DateType::class, [
                'label' => 'programme.form.startDate',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'constraints' => [new Assert\NotNull()],
            ])
            ->add('endDate', DateType::class, [
                'label' => 'programme.form.endDate',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'help' => 'programme.form.endDate_help',
            ])
            ->add('location', TextType::class, [
                'label' => 'programme.form.location',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 150)],
            ])
            ->add('discipline', EnumType::class, [
                'label' => 'programme.form.discipline',
                'class' => RaceDiscipline::class,
                'choice_label' => static fn (RaceDiscipline $d): string => 'programme.discipline.'.$d->value,
                'data' => RaceDiscipline::Road,
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

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event): void {
            $race = $event->getData();
            if (!$race instanceof Race) {
                return;
            }
            $end = $race->getEndDate();
            if (null !== $end && $end < $race->getStartDate()) {
                $event->getForm()->get('endDate')->addError(
                    new FormError((string) (new Assert\GreaterThanOrEqual('startDate'))->message),
                );
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Race::class,
            'empty_data' => static function (FormInterface $form): Race {
                $startDate = $form->get('startDate')->getData();
                if (!$startDate instanceof \DateTimeImmutable) {
                    $startDate = new \DateTimeImmutable('today');
                }
                $discipline = $form->get('discipline')->getData();
                if (!$discipline instanceof RaceDiscipline) {
                    $discipline = RaceDiscipline::Road;
                }

                return new Race(
                    (string) ($form->get('name')->getData() ?? ''),
                    $startDate,
                    (string) ($form->get('location')->getData() ?? ''),
                    $discipline,
                );
            },
        ]);
    }
}
