<?php

namespace Tests\Unit\Support;

use App\Support\ComposerConstraint;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ComposerConstraintTest extends TestCase
{
    /**
     * @param  array<int, string>  $expected
     */
    #[DataProvider('constraints')]
    public function test_it_finds_the_lowest_version_allowed_in_each_major_version(string $constraint, array $expected): void
    {
        $this->assertSame($expected, ComposerConstraint::lowestVersionsByMajor($constraint, [4, 3]));
    }

    public function test_only_the_given_major_versions_are_considered(): void
    {
        $this->assertSame([4 => '4.0'], ComposerConstraint::lowestVersionsByMajor('^3.0 || ^4.0', [4]));
    }

    public function test_versions_are_listed_lowest_major_first(): void
    {
        $this->assertSame(
            [3 => '3.0', 4 => '4.0'],
            ComposerConstraint::lowestVersionsByMajor('^4.0 || ^3.0', [4, 3]),
        );
    }

    /**
     * @return array<string, array{0: string, 1: array<int, string>}>
     */
    public static function constraints(): array
    {
        return [
            'one major' => ['^4.0', [4 => '4.0']],
            'both majors with double pipes' => ['^3.0 || ^4.0', [3 => '3.0', 4 => '4.0']],
            'both majors with a single pipe' => ['^3.0|^4.0', [3 => '3.0', 4 => '4.0']],
            'a patch release in one major' => ['^3.2.1 || ^4.0', [3 => '3.2.1', 4 => '4.0']],
            'a minor release' => ['^4.3', [4 => '4.3']],
            'a different minimum in each major' => ['^3.0 || ^4.5.2', [3 => '3.0', 4 => '4.5.2']],
            'a zero patch number is dropped' => ['^3.0.0', [3 => '3.0']],
            'the lowest alternative in a major wins' => ['^3.5 || ^3.2', [3 => '3.2']],
            'any version' => ['*', [3 => '3.0', 4 => '4.0']],
            'a minimum with no upper limit' => ['>=3.1', [3 => '3.1', 4 => '4.0']],
            'a space after the operator' => ['>= 3.1', [3 => '3.1', 4 => '4.0']],
            'a range ending part way through a major' => ['>=3.0 <4.2', [3 => '3.0', 4 => '4.0']],
            'a comma separated range ending before a major' => ['>=3.0,<4.0', [3 => '3.0']],
            'an upper limit only' => ['<4.0', [3 => '3.0']],
            'an inclusive upper limit' => ['<=4.0', [3 => '3.0', 4 => '4.0']],
            'an exclusive minimum' => ['>3.0', [3 => '3.0.1', 4 => '4.0']],
            'a tilde on a minor version' => ['~3.1', [3 => '3.1']],
            'a tilde on a patch version' => ['~4.1.2', [4 => '4.1.2']],
            'a major wildcard' => ['3.*', [3 => '3.0']],
            'an x wildcard' => ['4.x', [4 => '4.0']],
            'a minor wildcard' => ['3.4.*', [3 => '3.4']],
            'an exact version' => ['4.1.0', [4 => '4.1']],
            'an exact version with a v prefix' => ['v4.2', [4 => '4.2']],
            'a hyphen range' => ['3.5 - 4.1', [3 => '3.5', 4 => '4.0']],
            'a stability flag' => ['^4.0@beta', [4 => '4.0']],
            'a pre-release suffix' => ['^4.1-beta1', [4 => '4.1']],
            'a branch alternative is skipped' => ['^3.0 || dev-main', [3 => '3.0']],
            'an older major only' => ['^2.0', []],
            'a branch only' => ['dev-main', []],
            'an empty constraint' => ['', []],
            'a range that allows nothing' => ['^3.0 ^4.0', []],
        ];
    }
}
