<?php

namespace JBSNewMedia\AssetComposerBundle\Twig;

use JBSNewMedia\AssetComposerBundle\Service\AssetComposer;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class AssetsComposerExtension extends AbstractExtension
{
    public AssetComposer $assetComposer;
    public string $projectDir;
    private $assets = [];

    public function __construct(string $projectDir, AssetComposer $assetComposer)
    {
        $this->projectDir = $projectDir;
        $this->assetComposer = $assetComposer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('addAssetComposer', [$this, 'assetComposer']),
            new TwigFunction('renderAssetComposerStylesheets', [$this, 'renderStylesheets']),
            new TwigFunction('renderAssetComposerJavascripts', [$this, 'renderJavascripts']),
        ];
    }

    public function assetComposer(string $assetFilename, string $position = 'all'): void
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
                    $assetFilename,
                    $this->projectDir
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
                    $assetFilename,
                    $this->projectDir
                ).'"></script>';
            }
        }

        return new Markup($javascripts, 'UTF-8');
    }
}
