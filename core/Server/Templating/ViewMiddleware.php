<?php
namespace Core\Server\Templating;

use Core\Server\Router\Request;
use Core\Server\Router\Response;
use Lazervel\Path\Path;


final class ViewMiddleware
{
    use TemplatingHelpers;

    private string $viewDir;

    public function __construct(string $viewDir)
    {
        $this->viewDir = Path::normalize($viewDir);
    }

    public function __invoke(Request $request, ?Response $response): Response|null
    {
        $response = $response ?? new Response();

        $relPath = ltrim($request->path, '/');

        // this check allows visiting / and seeing index.php
        if ($relPath === '') {
            $relPath = 'index';
        }

        // resolve against the view root
        $absPath = Path::resolve($this->viewDir, $relPath);

        // enforce directory boundary
        $viewRoot = rtrim($this->viewDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (strpos($absPath, $viewRoot) !== 0) {
            return $response; // directory traversal attempt
        }

        $candidates = [
            $relPath . '.php',
            Path::join($relPath, 'index.php')
        ];

        $fileFound = false;
        $viewFile = '';

        // try to first find the view file.
        foreach ($candidates as $candidate) {
            $viewAbs = Path::join($this->viewDir, $candidate);
            if (file_exists($viewAbs)) {
                $fileFound = true;
                $viewFile = $viewAbs;
                break;
            }
        }

        // if the view does not exists, we propagate
        // to the next handlers (or trigger a 404 if none).
        if (!$fileFound) {
            return $response; // propagate to other handlers.
        }

        // render the view.
        $html = $this->renderViewInIsolation($viewFile, []);

        // try to render with layouts if they exist.
        $layoutDirRel = Path::relative($this->viewDir, Path::dirname($viewFile));

        while (true) {
            $layoutDirAbs = Path::resolve($this->viewDir, $layoutDirRel);
            $layoutFile = Path::join($layoutDirAbs, '_layout.php');

            if (file_exists($layoutFile)) {
                $html = $this->renderViewInIsolation($layoutFile, ['content' => $html]);
            }

            if ($layoutDirRel === '.') {
                break;
            }

            $layoutDirRel = Path::dirname($layoutDirRel);
        }

        return new Response($html, 200, ['Content-Type' => 'text/html']);
    }
}