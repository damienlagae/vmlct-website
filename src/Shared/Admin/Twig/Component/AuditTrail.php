<?php

declare(strict_types=1);

namespace App\Shared\Admin\Twig\Component;

use DH\Auditor\Model\Entry;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Query;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Vich\UploaderBundle\Util\ClassUtils;

/**
 * Renders the audit trail for a given entity as a styled timeline.
 *
 * Usage:
 *   <twig:Admin:AuditTrail entity="{{ article }}" />
 *
 * The component reads from the auditor-bundle storage and renders the
 * events in our own admin layout — no use of the bundle's default UI.
 */
#[AsTwigComponent(name: 'Admin:AuditTrail', template: '@Shared/admin/components/AuditTrail.html.twig')]
final class AuditTrail
{
    public ?object $entity = null;

    public int $limit = 50;

    public function __construct(
        private readonly Reader $reader,
        private readonly Security $security,
    ) {
    }

    /**
     * @return list<Entry>
     */
    public function getEntries(): array
    {
        if (null === $this->entity || !$this->security->isGranted('ROLE_ADMIN')) {
            return [];
        }

        $entityId = $this->resolveEntityId();
        if (null === $entityId) {
            return [];
        }

        try {
            $query = $this->reader->createQuery($this->resolveEntityClass(), [
                'object_id' => $entityId,
                'page_size' => $this->limit,
            ]);
            $query->addOrderBy(Query::CREATED_AT, 'DESC');

            return array_values($query->execute());
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return class-string
     */
    private function resolveEntityClass(): string
    {
        \assert(null !== $this->entity);
        $class = ClassUtils::getClass($this->entity);
        \assert(class_exists($class));

        return $class;
    }

    private function resolveEntityId(): ?string
    {
        if (null === $this->entity || !method_exists($this->entity, 'getId')) {
            return null;
        }

        $id = $this->entity->getId();
        if (null === $id) {
            return null;
        }

        return (string) $id;
    }
}
