<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Form;

use App\Module\Programme\Entity\Race;
use App\Module\Team\Entity\Rider;
use App\Module\Uitslagen\Entity\Result;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class ResultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('race', EntityType::class, [
                'label' => 'uitslagen.form.race',
                'class' => Race::class,
                'choice_label' => static fn (Race $r): string => \sprintf(
                    '%s — %s',
                    $r->getStartDate()->format('d/m/Y'),
                    $r->getName(),
                ),
                'query_builder' => static fn ($repo) => $repo->createQueryBuilder('r')->orderBy('r.startDate', 'DESC'),
                'placeholder' => 'uitslagen.form.race_placeholder',
                'constraints' => [new Assert\NotNull()],
            ])
            ->add('rider', EntityType::class, [
                'label' => 'uitslagen.form.rider',
                'class' => Rider::class,
                'choice_label' => static fn (Rider $r): string => $r->getFullName(),
                'placeholder' => 'uitslagen.form.rider_placeholder',
                'constraints' => [new Assert\NotNull()],
            ])
            ->add('stageNumber', IntegerType::class, [
                'label' => 'uitslagen.form.stageNumber',
                'required' => false,
                'attr' => ['min' => 1],
                'help' => 'uitslagen.form.stageNumber_help',
                'constraints' => [new Assert\Positive(), new Assert\LessThanOrEqual(50)],
            ])
            ->add('rank', IntegerType::class, [
                'label' => 'uitslagen.form.rank',
                'attr' => ['min' => 1],
                'constraints' => [new Assert\NotNull(), new Assert\Positive(), new Assert\LessThanOrEqual(9999)],
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'uitslagen.form.notes',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Result::class,
            'empty_data' => static function (FormInterface $form): ?Result {
                $rider = $form->get('rider')->getData();
                $race = $form->get('race')->getData();
                $rank = (int) ($form->get('rank')->getData() ?? 0);
                if (!$rider instanceof Rider || !$race instanceof Race || $rank <= 0) {
                    return null;
                }

                return new Result($rider, $race, $rank);
            },
        ]);
    }
}
