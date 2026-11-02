<?php

namespace App\Support;

/**
 * Works out which versions a Composer constraint such as "^3.2.1 || ^4.0" allows.
 *
 * Versions are compared by their major, minor and patch numbers. Anything after
 * those, like a fourth number or a stability suffix ("-beta1"), is ignored.
 */
final class ComposerConstraint
{
    /**
     * A version like "v4.1.2-beta1", capturing its major, minor and patch numbers.
     */
    private const VERSION = 'v?(\d+)(?:\.(\d+))?(?:\.(\d+))?(?:\.\d+)?(?:[._-]?(?:stable|beta|b|rc|alpha|a|patch|pl|p)(?:[.-]?\d+)*)?(?:[.-]?dev)?';

    /**
     * The lowest version of each given major version that the constraint allows, keyed
     * by major version, lowest first: "^3.2.1 || ^4.0" gives [3 => '3.2.1', 4 => '4.0'].
     *
     * Alternatives that don't name a version, like "dev-main", are skipped.
     *
     * @param  array<int, int>  $majorVersions
     * @return array<int, string>
     */
    public static function lowestVersionsByMajor(string $constraint, array $majorVersions): array
    {
        $constraint = trim(preg_replace('/@(?:stable|rc|beta|alpha|dev)\b/i', '', $constraint));

        $ranges = array_filter(array_map(self::range(...), preg_split('/\s*\|\|?\s*/', $constraint)));

        sort($majorVersions);

        $lowest = [];

        foreach ($majorVersions as $major) {
            foreach ($ranges as [$from, $limit, $includesLimit]) {
                $candidate = max($from, [$major, 0, 0]);

                if ($candidate[0] === $major && self::isBelow($candidate, $limit, $includesLimit)) {
                    $lowest[$major] = min($lowest[$major] ?? $candidate, $candidate);
                }
            }
        }

        return array_map(self::format(...), $lowest);
    }

    /**
     * The versions one "||" alternative allows, as [lowest version, upper limit, whether
     * the limit itself is allowed]. A null limit means there isn't one. Returns null
     * if the alternative can't be read or allows no version at all.
     *
     * @return array{0: array{int, int, int}, 1: array{int, int, int}|null, 2: bool}|null
     */
    private static function range(string $alternative): ?array
    {
        $version = self::VERSION;

        $range = preg_match("/^{$version}\s+-\s+{$version}$/i", $alternative, $matches, PREG_UNMATCHED_AS_NULL)
            ? self::hyphenRange(self::numbers(array_slice($matches, 1, 3)), self::numbers(array_slice($matches, 4, 3)))
            : self::intersection(preg_split('/[\s,]+/', preg_replace('/(?<=[<>=!~^])\s+/', '', $alternative)));

        if ($range === null || ! self::isBelow($range[0], $range[1], $range[2])) {
            return null;
        }

        return $range;
    }

    /**
     * The versions all of the terms allow, like ">=3.1 <4.2".
     *
     * @param  array<int, string>  $terms
     * @return array{0: array{int, int, int}, 1: array{int, int, int}|null, 2: bool}|null
     */
    private static function intersection(array $terms): ?array
    {
        $from = [0, 0, 0];
        $limit = null;
        $includesLimit = false;

        foreach ($terms as $term) {
            $range = self::termRange($term);

            if ($range === null) {
                return null;
            }

            $from = max($from, $range[0]);

            if ($range[1] !== null && ($limit === null || $range[1] < $limit || ($range[1] === $limit && ! $range[2]))) {
                [$limit, $includesLimit] = [$range[1], $range[2]];
            }
        }

        return [$from, $limit, $includesLimit];
    }

    /**
     * The versions a single term like "^3.0", "~3.1", ">=3.1", "4.1.0" or "4.*" allows.
     *
     * @return array{0: array{int, int, int}, 1: array{int, int, int}|null, 2: bool}|null
     */
    private static function termRange(string $term): ?array
    {
        if (preg_match('/^[x*]$/i', $term)) {
            return [[0, 0, 0], null, false];
        }

        if (preg_match('/^v?(\d+)(?:\.(\d+))?\.[x*](?:-dev)?$/i', $term, $matches, PREG_UNMATCHED_AS_NULL)) {
            [$major, $minor] = self::numbers(array_slice($matches, 1, 2));

            return $minor === null
                ? [[$major, 0, 0], [$major + 1, 0, 0], false]
                : [[$major, $minor, 0], [$major, $minor + 1, 0], false];
        }

        if (! preg_match('/^(\^|~>?|<>|!=|>=?|<=?|==?)?'.self::VERSION.'$/i', $term, $matches, PREG_UNMATCHED_AS_NULL)) {
            return null;
        }

        [$major, $minor, $patch] = self::numbers(array_slice($matches, 2, 3));
        $version = [$major, $minor ?? 0, $patch ?? 0];

        return match ($matches[1]) {
            '^' => [$version, self::caretLimit($major, $minor, $patch), false],
            '~', '~>' => [$version, $patch === null ? [$major + 1, 0, 0] : [$major, $minor + 1, 0], false],
            '>=' => [$version, null, false],
            '>' => [[$major, $minor ?? 0, ($patch ?? 0) + 1], null, false],
            '<' => [[0, 0, 0], $version, false],
            '<=' => [[0, 0, 0], $version, true],
            '!=', '<>' => [[0, 0, 0], null, false],
            default => [$version, $version, true],
        };
    }

    /**
     * The version a caret constraint stops before: ^3.2 allows up to 4.0, but
     * ^0.3 only up to 0.4 and ^0.0.3 only up to 0.0.4.
     *
     * @return array{int, int, int}
     */
    private static function caretLimit(int $major, ?int $minor, ?int $patch): array
    {
        return match (true) {
            $major > 0 || $minor === null => [$major + 1, 0, 0],
            $minor > 0 || $patch === null => [0, $minor + 1, 0],
            default => [0, 0, $patch + 1],
        };
    }

    /**
     * "3.0 - 4.1" allows 3.0.0 up to, but not including, 4.2.0, while a complete
     * upper version ("3.0 - 4.1.5") is itself allowed.
     *
     * @param  array<int, int|null>  $from
     * @param  array<int, int|null>  $to
     * @return array{0: array{int, int, int}, 1: array{int, int, int}, 2: bool}
     */
    private static function hyphenRange(array $from, array $to): array
    {
        [$toMajor, $toMinor, $toPatch] = $to;

        return [
            [$from[0], $from[1] ?? 0, $from[2] ?? 0],
            match (true) {
                $toPatch !== null => [$toMajor, $toMinor, $toPatch],
                $toMinor !== null => [$toMajor, $toMinor + 1, 0],
                default => [$toMajor + 1, 0, 0],
            },
            $toPatch !== null,
        ];
    }

    /**
     * Whether a version is below a range's upper limit, or on it when the limit is allowed.
     *
     * @param  array{int, int, int}  $version
     * @param  array{int, int, int}|null  $limit
     */
    private static function isBelow(array $version, ?array $limit, bool $includesLimit): bool
    {
        return $limit === null || $version < $limit || ($includesLimit && $version === $limit);
    }

    /**
     * @param  array<int, string|null>  $numbers
     * @return array<int, int|null>
     */
    private static function numbers(array $numbers): array
    {
        return array_map(fn (?string $number): ?int => $number === null ? null : (int) $number, array_values($numbers));
    }

    /**
     * Write [4, 0, 0] as "4.0" and [4, 5, 2] as "4.5.2".
     *
     * @param  array{int, int, int}  $version
     */
    private static function format(array $version): string
    {
        [$major, $minor, $patch] = $version;

        return $patch === 0 ? "{$major}.{$minor}" : "{$major}.{$minor}.{$patch}";
    }
}
