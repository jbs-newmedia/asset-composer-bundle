<?php

namespace JBSNewMedia\AssetComposerBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AssetComposer
{
    public function getAssetFile(string $namespace, string $package, string $asset, string $projectDir): Response
    {
        $vendorDir = $projectDir.'/vendor/'.$namespace.'/'.$package.'/';
        if (!is_dir($vendorDir)) {
            throw new BadRequestHttpException('Vendor not found');
        }

        $vendorFile = $vendorDir.$asset;
        if (substr(realpath($vendorFile), 0, strlen($vendorDir)) !== $vendorDir) {
            throw new BadRequestHttpException('Asset not found');
        }

        $fileType = pathinfo($vendorFile, PATHINFO_EXTENSION);
        $content = file_get_contents($vendorFile);
        $response = new Response($content);
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', strtotime('+10 years')));
        $response->headers->set('Cache-Control', 'max-age=315360000, public');
        $response->headers->set('Pragma', 'cache');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', filemtime($vendorFile)));
        switch ($fileType) {
            case 'js':
                $response->headers->set('Content-Type', 'application/javascript');
                break;
            case 'css':
                $response->headers->set('Content-Type', 'text/css');

                break;
            case 'json':
                $response = new JsonResponse(json_decode($content, true));

                break;
            default:
                $response->headers->set('Content-Type', 'text/plain');

                break;
        }

        return $response;
    }

    public function getAssetFileName(string $asset, string $projectDir): string
    {
        $vendorFile = $projectDir.'/vendor/'.$asset;
        if (substr(realpath($vendorFile), 0, strlen($projectDir)) !== $projectDir) {
            throw new BadRequestHttpException('Asset not found');
        }

        return 'assets/composer/'.$asset.'?v='.filemtime($vendorFile);
    }
}
