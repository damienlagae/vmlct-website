<?php

declare(strict_types=1);

namespace App\Shared\Content\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Converts between an array of block payloads (entity-side) and its JSON
 * string representation (form-side, carried by a HiddenType field).
 *
 * @implements DataTransformerInterface<array<int, array<string, mixed>>, string>
 */
final class JsonBlocksTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): string
    {
        if (null === $value) {
            return '[]';
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException('Expected an array of block payloads.');
        }

        try {
            return json_encode($value, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
        } catch (\JsonException $e) {
            throw new TransformationFailedException('Unable to serialise blocks.', previous: $e);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function reverseTransform(mixed $value): array
    {
        if (null === $value || '' === $value) {
            return [];
        }

        if (!\is_string($value)) {
            throw new TransformationFailedException('Expected a JSON string for blocks.');
        }

        try {
            $decoded = json_decode($value, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new TransformationFailedException('Invalid JSON payload for blocks.', previous: $e);
        }

        if (!\is_array($decoded)) {
            throw new TransformationFailedException('Block payload must decode to an array.');
        }

        return array_values(array_filter($decoded, '\is_array'));
    }
}
