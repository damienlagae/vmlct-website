<?php

declare(strict_types=1);

namespace App\Module\Menu\Form;

use App\Module\Menu\Entity\MenuItem;
use App\Module\Menu\Entity\MenuTargetType;
use App\Module\Menu\Repository\MenuItemRepository;
use App\Module\Menu\Routing\MenuRouteCatalog;
use App\Page\Entity\Page;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'menu.form.label',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 100)],
            ])
            ->add('targetType', EnumType::class, [
                'label' => 'menu.form.targetType',
                'class' => MenuTargetType::class,
                'choice_label' => static fn (MenuTargetType $t): string => 'menu.target.type.'.$t->value,
            ])
            ->add('page', EntityType::class, [
                'label' => 'menu.form.page',
                'class' => Page::class,
                'choice_label' => 'title',
                'required' => false,
                'placeholder' => 'menu.form.page_placeholder',
                'help' => 'menu.form.page_help',
            ])
            ->add('routeName', ChoiceType::class, [
                'label' => 'menu.form.routeName',
                'required' => false,
                'placeholder' => 'menu.form.route_placeholder',
                'choices' => MenuRouteCatalog::choices(),
            ])
            ->add('parent', EntityType::class, [
                'label' => 'menu.form.parent',
                'class' => MenuItem::class,
                'choice_label' => 'label',
                'required' => false,
                'placeholder' => 'menu.form.parent_placeholder',
                'help' => 'menu.form.parent_help',
                'query_builder' => function (MenuItemRepository $repo) use ($options) {
                    $qb = $repo->createQueryBuilder('m')
                        ->where('m.parent IS NULL')
                        ->orderBy('m.position', 'ASC')
                    ;
                    // Prevent picking self as parent on edit.
                    $current = $options['data'] ?? null;
                    if ($current instanceof MenuItem && null !== $current->getId()) {
                        $qb->andWhere('m.id != :self')
                            ->setParameter('self', $current->getId(), 'ulid');
                    }

                    return $qb;
                },
            ])
            ->add('position', IntegerType::class, [
                'label' => 'menu.form.position',
                'help' => 'menu.form.position_help',
                'attr' => ['min' => 0],
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'menu.form.active',
                'required' => false,
            ])
            ->add('openInNewTab', CheckboxType::class, [
                'label' => 'menu.form.openInNewTab',
                'required' => false,
            ])
        ;

        // Cross-field validation: ensure the chosen target field is filled.
        $builder->addEventListener(
            \Symfony\Component\Form\FormEvents::POST_SUBMIT,
            static function (\Symfony\Component\Form\FormEvent $event): void {
                $item = $event->getData();
                if (!$item instanceof MenuItem) {
                    return;
                }
                $form = $event->getForm();
                if (MenuTargetType::Page === $item->getTargetType() && null === $item->getPage()) {
                    $form->get('page')->addError(new \Symfony\Component\Form\FormError(
                        (string) (new Assert\NotBlank())->message,
                    ));
                }
                if (MenuTargetType::Route === $item->getTargetType() && null === $item->getRouteName()) {
                    $form->get('routeName')->addError(new \Symfony\Component\Form\FormError(
                        (string) (new Assert\NotBlank())->message,
                    ));
                }
            },
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuItem::class,
            'empty_data' => static function (FormInterface $form): MenuItem {
                $label = (string) ($form->get('label')->getData() ?? '');
                $target = $form->get('targetType')->getData();
                if (!$target instanceof MenuTargetType) {
                    $target = MenuTargetType::Page;
                }

                return new MenuItem($label, $target);
            },
        ]);
    }
}
