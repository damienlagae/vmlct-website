<?php

declare(strict_types=1);

namespace App\Shared\Admin\Twig\Component;

use DH\Auditor\Model\Entry;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Query;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use Doctrine\ORM\EntityManagerInterface;
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
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Classify a nested per-record diff (e.g. a single block inside `content`).
     *
     * Auditor produces sub-field diffs like `{html: {new: ...}, type: {new: ...}}`.
     * - all sub-fields carry only `new` → the record was inserted
     * - all sub-fields carry only `old` → the record was removed
     * - mix of old/new → in-place modification
     *
     * Note: this heuristic cannot detect a move (auditor diffs are computed
     * per index), which surfaces as a `modify` on every reshuffled record.
     *
     * @param array<string, mixed> $item
     *
     * @return 'add'|'remove'|'modify'
     */
    public static function classifyChange(array $item): string
    {
        $hasOld = false;
        $hasNew = false;

        foreach ($item as $change) {
            if (!\is_array($change)) {
                continue;
            }
            if (\array_key_exists('old', $change)) {
                $hasOld = true;
            }
            if (\array_key_exists('new', $change)) {
                $hasNew = true;
            }
        }

        if ($hasNew && !$hasOld) {
            return 'add';
        }
        if ($hasOld && !$hasNew) {
            return 'remove';
        }

        return 'modify';
    }

    /**
     * Human-readable rendering of a diff value. Auditor stores associations
     * as `{class, id, label, table}` arrays; resolve them through Doctrine
     * and surface a usable label (full name, title, email, ...) instead of
     * the raw FQCN + ULID.
     */
    public function formatValue(mixed $value): string
    {
        if (\is_array($value) && isset($value['class'], $value['id']) && \is_string($value['class']) && class_exists($value['class'])) {
            return $this->labelForReference($value['class'], (string) $value['id']);
        }

        if (\is_scalar($value) || null === $value) {
            return null === $value ? 'null' : (string) $value;
        }

        return (string) json_encode($value, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param class-string $class
     */
    private function labelForReference(string $class, string $id): string
    {
        $shortClass = (new \ReflectionClass($class))->getShortName();

        try {
            $entity = $this->em->find($class, $id);
        } catch (\Throwable) {
            $entity = null;
        }

        if (null === $entity) {
            return \sprintf('%s#%s', $shortClass, $id);
        }

        foreach (['getFullName', 'getTitle', 'getName', 'getEmail', '__toString'] as $method) {
            if (method_exists($entity, $method)) {
                $label = (string) $entity->{$method}();
                if ('' !== $label) {
                    return \sprintf('%s — %s', $shortClass, $label);
                }
            }
        }

        return \sprintf('%s#%s', $shortClass, $id);
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
