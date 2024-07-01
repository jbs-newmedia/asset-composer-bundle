<?php

namespace JBSNewMedia\AssetComposerBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AssetComposer
{
    private string $projectDir;
    private UrlGeneratorInterface $router;

    public function __construct(string $projectDir, UrlGeneratorInterface $router)
    {
        $this->projectDir = $projectDir;
        $this->router = $router;
    }

    public function getAssetFile(string $namespace, string $package, string $asset): Response
    {
        if (('app' === $namespace) && ('assets' === $package)) {
            $vendorDir = $this->projectDir.'/assets/';
        } else {
            $vendorDir = $this->projectDir.'/vendor/'.$namespace.'/'.$package.'/';
        }

        if (!is_dir($vendorDir)) {
            throw new BadRequestHttpException('Asset not found in vendor directory');
        }

        $vendorFile = $vendorDir.$asset;
        if (substr(realpath($vendorFile), 0, strlen($vendorDir)) !== $vendorDir) {
            throw new BadRequestHttpException('vendor directory traversal detected');
        }

        $fileType = pathinfo($vendorFile, PATHINFO_EXTENSION);
        $content = file_get_contents($vendorFile);
        $response = new Response($content);
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', strtotime('+10 years')));
        $response->headers->set('Cache-Control', 'max-age=315360000, public');
        $response->headers->set('Pragma', 'cache');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', filemtime($vendorFile)));

        $contentTypes = [
            'csv' => 'text/csv',
            'css' => 'text/css',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'eot' => 'font/eot',
            'gif' => 'image/gif',
            'gz' => 'application/gzip',
            'html' => 'text/html',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            'oga' => 'audio/ogg',
            'ogv' => 'video/ogg',
            'otf' => 'font/otf',
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'tar' => 'application/x-tar',
            'ttf' => 'font/ttf',
            'wav' => 'audio/wav',
            'webm' => 'video/webm',
            'webp' => 'image/webp',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xml' => 'application/xml',
            'zip' => 'application/zip',
        ];

        if (isset($contentTypes[$fileType])) {
            $contentType = $contentTypes[$fileType];
            $response->headers->set('Content-Type', $contentType);
        } else {
            $response->headers->set('Content-Type', 'text/plain');
        }

        return $response;
    }

    public function getAssetFileName(string $asset): string
    {
        $assetParts = explode('/', $asset);
        if (count($assetParts) < 3) {
            throw new BadRequestHttpException('Asset not found');
        }

        if (('app' === $assetParts[0]) && ('assets' === $assetParts[1])) {
            $vendorFile = $this->projectDir.'/assets/'.implode('/', array_slice($assetParts, 2));
        } else {
            $vendorFile = $this->projectDir.'/vendor/'.$asset;
            if (substr(realpath($vendorFile), 0, strlen($this->projectDir)) !== $this->projectDir) {
                throw new BadRequestHttpException('Asset not found');
            }
        }

        if (!file_exists($vendorFile)) {
            throw new BadRequestHttpException('Asset not found ('.str_replace($this->projectDir.'/', '', $vendorFile).')');
        }

        $baseUrl = $this->router->generate('jbs_new_media_assets_composer', [
            'namespace' => $assetParts[0],
            'package' => $assetParts[1],
            'asset' => implode('/', array_slice($assetParts, 2)),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $baseUrl.'?v='.filemtime($vendorFile);
    }
}
