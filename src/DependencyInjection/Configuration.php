<?php

declare(strict_types=1);

namespace JBSNewMedia\AssetComposerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('mycorp_forms');

        $treeBuilder->getRootNode()
            ->children()
            ->booleanNode('favorite_submenu_enabled')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
