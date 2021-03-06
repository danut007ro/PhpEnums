<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Bundle\DependencyInjection;

use Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\Configuration;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[]]);

        $this->assertEquals($this->getDefaultConfig(), $config);
    }

    public function testDisabledConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'argument_value_resolver' => false,
            'serializer' => false,
            'translation_extractor' => false,
        ]]);

        $this->assertEquals([
            'argument_value_resolver' => ['enabled' => false],
            'serializer' => ['enabled' => false],
            'translation_extractor' => [
                'enabled' => false,
                'paths' => [],
                'domain' => 'messages',
                'filename_pattern' => '*.php',
                'ignore' => [],
            ],
        ], $config);
    }

    public function testDoctrineConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'doctrine' => [
                'types' => [
                    Gender::class => ['name' => 'gender'],
                    Permissions::class => ['name' => 'permissions', 'type' => 'int'],
                ],
            ],
        ]]);

        $this->assertEquals($this->getDefaultConfig() + [
            'doctrine' => [
                'types' => [
                    Gender::class => ['name' => 'gender', 'type' => 'string'],
                    Permissions::class => ['name' => 'permissions', 'type' => 'int'],
                ],
            ],
        ], $config);
    }

    public function testDoctrineTypeConfigWithInvalidEnumClass()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid configuration for path "elao_enum.doctrine.types": Invalid classes ["stdClass"]. Expected instances of "Elao\Enum\EnumInterface"');

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [[
            'doctrine' => [
                'types' => [
                    \stdClass::class => ['name' => 'std'],
                ],
            ],
        ]]);
    }

    public function testTranslationExtractorConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'translation_extractor' => [
                'enabled' => true,
                'paths' => ['App\Enum' => '%kernel.project_dir%/src/Enum'],
                'domain' => 'messages_test',
                'filename_pattern' => '*Enum.php',
                'ignore' => ['%kernel.project_dir%/src/Enum/Ignored'],
            ],
        ]]);

        $this->assertEquals([
            'enabled' => true,
            'paths' => ['App\Enum' => '%kernel.project_dir%/src/Enum'],
            'domain' => 'messages_test',
            'filename_pattern' => '*Enum.php',
            'ignore' => ['%kernel.project_dir%/src/Enum/Ignored'],
        ], $config['translation_extractor']);
    }

    private function getDefaultConfig(): array
    {
        return [
            'argument_value_resolver' => ['enabled' => true],
            'serializer' => ['enabled' => true],
            'translation_extractor' => [
                'enabled' => false,
                'paths' => [],
                'domain' => 'messages',
                'filename_pattern' => '*.php',
                'ignore' => [],
            ],
        ];
    }
}
