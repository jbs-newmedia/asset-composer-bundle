<?php

namespace JBSNewMedia\AssetComposerBundle\Twig;

use JBSNewMedia\AssetComposerBundle\Service\AssetComposer;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class AssetComposerExtension extends AbstractExtension
{
    public AssetComposer $assetComposer;
    private $assets = [];

    public function __construct(AssetComposer $assetComposer)
    {
        $this->assetComposer = $assetComposer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('addAssetComposer', [$this, 'addAssetComposer']),
            new TwigFunction('renderAssetComposerStylesheets', [$this, 'renderStylesheets']),
            new TwigFunction('renderAssetComposerJavascripts', [$this, 'renderJavascripts']),
            new TwigFunction('getAssetComposerFile', [$this, 'getAssetComposerFile']),
        ];
    }

    public function addAssetComposer(string $assetFilename, string $position = 'all'): void
    {
        $assetInfo = pathinfo($assetFilename);
        switch ($assetInfo['extension']) {
            case 'css':
                $this->assets[$position]['css'][$assetFilename] = $assetFilename;
                break;
            case 'js':
                $this->assets[$position]['js'][$assetFilename] = $assetFilename;
                break;
            default:
                throw new \InvalidArgumentException('Invalid asset type');
        }
    }

    public function renderStylesheets(string $position = 'all'): Markup
    {
        $stylesheets = '';
        if ((isset($this->assets[$position])) && (isset($this->assets[$position]['css'])) && ([] !== $this->assets[$position]['css'])) {
            foreach ($this->assets[$position]['css'] as $assetFilename) {
                $stylesheets .= '<link rel="stylesheet" href="'.$this->assetComposer->getAssetFileName(
                    $assetFilename
                ).'">';
            }
        }

        return new Markup($stylesheets, 'UTF-8');
    }

    public function renderJavascripts(string $position = 'all'): Markup
    {
        $javascripts = '';
        if ((isset($this->assets[$position])) && (isset($this->assets[$position]['js'])) && ([] !== $this->assets[$position]['js'])) {
            foreach ($this->assets[$position]['js'] as $assetFilename) {
                $javascripts .= '<script src="'.$this->assetComposer->getAssetFileName(
                    $assetFilename
                ).'"></script>';
            }
        }

        return new Markup($javascripts, 'UTF-8');
    }

    public function getAssetComposerFile(string $assetFilename): string
    {
        return $this->assetComposer->getAssetFileName($assetFilename);
    }
}
