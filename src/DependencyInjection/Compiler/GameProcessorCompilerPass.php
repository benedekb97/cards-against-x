<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Processor\Game\GameProcessor;
use App\Processor\Game\GameProcessorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GameProcessorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(GameProcessor::class)) {
            return;
        }

        $definition = $container->findDefinition(GameProcessor::class);

        $taggedServices = $container->findTaggedServiceIds(GameProcessorInterface::SERVICE_TAG);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addProcessor', [$tags[0]['priority'], new Reference($id)]);
        }
    }
}
