<?php

declare(strict_types=1);

namespace JBSNewMedia\AssetComposerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AssetComposerExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $builder): void
    {
        $projectDir = $builder->getParameter('kernel.project_dir');
        $filePath = $projectDir.'/config/routes/asset_composer.yaml';
        $bundlePath = __DIR__.'/../Resources/config/routes.yaml';

        if (!file_exists($filePath)) {
            file_put_contents($filePath, file_get_contents($bundlePath));
        }
    }
}
