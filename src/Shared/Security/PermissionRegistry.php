<?php

declare(strict_types=1);

namespace App\Shared\Security;

use App\Shared\Security\Permissions\PermissionInterface;

/**
 * Runtime catalog of every permission enum discovered at container build time.
 *
 * Populated by PermissionEnumDiscoveryPass. Used by OwnerVoter (to resolve an
 * enum case from a string value) and by the profile page (to iterate all
 * defined permissions and group them by domain).
 */
final class PermissionRegistry
{
    /**
     * @var array<string, PermissionInterface>|null
     */
    private ?array $byValueCache = null;

    /**
     * @param list<class-string<PermissionInterface>> $enumClasses
     */
    public function __construct(
        private readonly array $enumClasses = [],
    ) {
    }

    /**
     * @return list<class-string<PermissionInterface>>
     */
    public function getEnumClasses(): array
    {
        return $this->enumClasses;
    }

    public function find(string $value): ?PermissionInterface
    {
        return $this->indexByValue()[$value] ?? null;
    }

    /**
     * @return list<PermissionInterface>
     */
    public function all(): array
    {
        return array_values($this->indexByValue());
    }

    /**
     * @return array<string, PermissionInterface>
     */
    private function indexByValue(): array
    {
        if (null !== $this->byValueCache) {
            return $this->byValueCache;
        }

        $index = [];
        foreach ($this->enumClasses as $class) {
            foreach ($class::cases() as $case) {
                if (!\is_string($case->value)) {
                    throw new \LogicException(sprintf('Permission enum "%s" case "%s" must use a string value, got %s.', $class, $case->name, get_debug_type($case->value)));
                }
                $index[$case->value] = $case;
            }
        }

        return $this->byValueCache = $index;
    }
}
