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
}
