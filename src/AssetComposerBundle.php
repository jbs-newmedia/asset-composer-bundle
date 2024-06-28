<?php

namespace JBSNewMedia\AssetComposerBundle;

use JBSNewMedia\AssetComposerBundle\DependencyInjection\AssetComposerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class AssetComposerBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new AssetComposerExtension();
        }

        return $this->extension;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $projectDir = $builder->getParameter('kernel.project_dir');
        $filePath = $projectDir.'/config/routes/asset_composer.yaml';
        $bundlePath = __DIR__.'/Resources/config/routes.yaml';

        file_put_contents($filePath, file_get_contents($bundlePath));
    }
}
