<?php declare(strict_types=1);

use Lmc\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(
        Option::SKIP,
        [
            ClassNameSuffixByParentSniff::class => ['src/Model/Command/*.php'],
        ]
    );

    $containerConfigurator->import(__DIR__ . '/vendor/lmc/coding-standard/ecs.php');

    $services = $containerConfigurator->services();

    // All tests must have @test annotation instead of "test" prefix.
    $services->set(PhpUnitTestAnnotationFixer::class)
        ->call('configure', [['style' => 'annotation']]);

    // Force line length
    $services->set(LineLengthFixer::class)
        ->call(
            'configure',
            [['line_length' => 120, 'break_long_lines' => true, 'inline_short_lines' => false]]
        );
};
