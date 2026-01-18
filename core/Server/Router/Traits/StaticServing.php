<?php

namespace App\Router\Traits;

use App\Router\Request;
use App\Router\Response;
use Lazervel\Path\Path;
use Symfony\Component\Mime\MimeTypes;

trait StaticServing
{
    public function serveStatic(string $path, string $dir): static
    {
        $this->register(['GET'], $path, function (Request $request, Response $response) use ($path, $dir) {

            $requestedFile = Path::relative($path, $request->path);
            $resolvedRequestedFile = realpath($dir . '/' . ltrim($requestedFile, '/'));

            if ($resolvedRequestedFile === false || !str_starts_with($resolvedRequestedFile, realpath($dir)) || !is_file($resolvedRequestedFile)) {
                $response->setStatusCode(404)->send();
                return;
            }

            $ext = strtolower(ltrim(Path::extname($resolvedRequestedFile), '.'));
            $mime = MimeTypes::getDefault()->getMimeTypes($ext)[0] ?? 'text/plain';

            $content = file_get_contents($resolvedRequestedFile);

            $response
                ->setContentType($mime)
                ->setContent($content)
                ->send();
        });

        return $this;
    }
}
