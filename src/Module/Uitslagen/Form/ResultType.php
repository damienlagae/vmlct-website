<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Form;

use App\Module\Programme\Entity\Race;
use App\Module\Team\Entity\Rider;
use App\Module\Uitslagen\Entity\Result;
use App\Module\Uitslagen\Entity\ResultDiscipline;
use App\Module\Uitslagen\Entity\ResultStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class ResultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rider', EntityType::class, [
                'label' => 'uitslagen.form.rider',
                'class' => Rider::class,
                'choice_label' => static fn (Rider $r): string => $r->getFullName(),
                'placeholder' => 'uitslagen.form.rider_placeholder',
                'constraints' => [new Assert\NotNull()],
            ])
            ->add('race', EntityType::class, [
                'label' => 'uitslagen.form.race',
                'class' => Race::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'uitslagen.form.race_placeholder',
                'help' => 'uitslagen.form.race_help',
            ])
            ->add('raceName', TextType::class, [
                'label' => 'uitslagen.form.raceName',
                'required' => false,
                'help' => 'uitslagen.form.raceName_help',
                'constraints' => [new Assert\Length(max: 200)],
            ])
            ->add('raceDate', DateType::class, [
                'label' => 'uitslagen.form.raceDate',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('raceLocation', TextType::class, [
                'label' => 'uitslagen.form.raceLocation',
                'required' => false,
                'constraints' => [new Assert\Length(max: 150)],
            ])
            ->add('discipline', EnumType::class, [
                'label' => 'uitslagen.form.discipline',
                'class' => ResultDiscipline::class,
                'choice_label' => static fn (ResultDiscipline $d): string => 'uitslagen.discipline.'.$d->value,
                'required' => false,
                'placeholder' => 'uitslagen.form.discipline_placeholder',
            ])
            ->add('status', EnumType::class, [
                'label' => 'uitslagen.form.status',
                'class' => ResultStatus::class,
                'choice_label' => static fn (ResultStatus $s): string => 'uitslagen.status.'.$s->value,
            ])
            ->add('rank', IntegerType::class, [
                'label' => 'uitslagen.form.rank',
                'required' => false,
                'attr' => ['min' => 1],
                'constraints' => [new Assert\Positive(), new Assert\LessThanOrEqual(9999)],
                'help' => 'uitslagen.form.rank_help',
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'uitslagen.form.notes',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
        ;

        // Cross-field validation + auto-mirror of the linked race's
        // startsAt into `raceDate` so ordering on the public list stays
        // consistent across standalone and linked results.
        $builder->addEventListener(
            \Symfony\Component\Form\FormEvents::POST_SUBMIT,
            static function (\Symfony\Component\Form\FormEvent $event): void {
                $result = $event->getData();
                if (!$result instanceof Result) {
                    return;
                }
                $form = $event->getForm();
                $race = $result->getRace();
                if (null === $race && (null === $result->getRaceName() || '' === $result->getRaceName())) {
                    $form->get('raceName')->addError(new \Symfony\Component\Form\FormError(
                        (string) (new Assert\NotBlank())->message,
                    ));
                }
                if (null !== $race && null === $result->getRaceDate()) {
                    $result->setRaceDate(\DateTimeImmutable::createFromInterface($race->getStartsAt()));
                }
            },
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Result::class,
            'empty_data' => static function (FormInterface $form): ?Result {
                $rider = $form->get('rider')->getData();
                if (!$rider instanceof Rider) {
                    return null; // Validation will flag the missing rider.
                }

                return new Result($rider);
            },
        ]);
    }
}
