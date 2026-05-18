<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Form;

use App\Module\Programme\Entity\RaceStage;
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

/**
 * Edit-only form for an existing Result. Creating new results will go
 * through the multi-step wizard (Symfony FormFlow) — see roadmap.
 */
final class ResultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stage', EntityType::class, [
                'label' => 'uitslagen.form.stage',
                'class' => RaceStage::class,
                'choice_label' => static function (RaceStage $s): string {
                    $label = $s->getName() ?? $s->getRace()->getName();

                    return $s->getEffectiveDate()->format('d/m/Y').' · '.$label;
                },
                'query_builder' => static fn ($repo) => $repo->createQueryBuilder('s')
                    ->innerJoin('s.race', 'race')
                    ->orderBy('race.startDate', 'DESC')
                    ->addOrderBy('s.position', 'ASC'),
                'constraints' => [new Assert\NotNull()],
            ])
            ->add('rider', EntityType::class, [
                'label' => 'uitslagen.form.rider',
                'class' => Rider::class,
                'choice_label' => static fn (Rider $r): string => $r->getFullName(),
                'constraints' => [new Assert\NotNull()],
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
                $stage = $form->get('stage')->getData();
                $rank = (int) ($form->get('rank')->getData() ?? 0);
                if (!$rider instanceof Rider || !$stage instanceof RaceStage || $rank <= 0) {
                    return null;
                }

                return new Result($rider, $stage, $rank);
            },
        ]);
    }
}
