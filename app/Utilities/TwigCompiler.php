<?php

declare(strict_types=1);

namespace App\Utilities;

use DirectoryIterator;
use Exception;
use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * Class TwigCompiler.
 */
class TwigCompiler
{
    protected Environment $twig;
    protected string $cachePath;
    protected bool $verbose;

    /**
     * The TwigCompiler constructor.
     *
     * @param Environment $twig      The Twig Environment instance
     * @param string      $cachePath The twig cache path
     * @param bool        $verbose   Enable verbose output
     */
    public function __construct(Environment $twig, string $cachePath, bool $verbose = false)
    {
        if (empty($cachePath)) {
            throw new InvalidArgumentException(_f('The cache path is required'));
        }

        $this->twig      = $twig;
        $this->cachePath =  str_replace('\\', '/', $cachePath);
        $this->verbose   = $verbose;
    }

    /**
     * Compile all twig templates.
     *
     * @throws Exception Exception
     *
     * @return bool Success
     */
    public function compile(): bool
    {
        // Delete old twig cache files
        if (file_exists($this->cachePath)) {
            $this->removeDirectory($this->cachePath);
        }
        if (!file_exists($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }

        // Iterate over all your templates and force compilation
        $this->twig->disableDebug();
        $this->twig->enableAutoReload();

        // The Twig cache must be enabled
        if (!$this->twig->getCache()) {
            $this->twig->setCache($this->cachePath);
        }

        $loader = $this->twig->getLoader();

        if ($loader instanceof FilesystemLoader) {
            $paths = $loader->getPaths();

            foreach ($paths as $path) {
                $this->compileFiles($path);
            }
        }

        return true;
    }

    /**
     * @param string $viewPath
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function compileFiles(string $viewPath)
    {
        $directory = new RecursiveDirectoryIterator($viewPath, FilesystemIterator::SKIP_DOTS);

        foreach (new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST) as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'twig') {
                continue;
            }

            $templateName = substr($file->getPathname(), strlen($viewPath) + 1);
            $templateName = str_replace('\\', '/', $templateName);

            if ($this->verbose) {
                echo sprintf("Parsing: %s\n", $templateName);
            }

            $this->twig->load($templateName);
        }
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function removeDirectory(string $path): bool
    {
        $iterator = new DirectoryIterator($path);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || !$fileInfo->isDir()) {
                continue;
            }
            $dirName = $fileInfo->getPathname();
            $this->removeDirectory($dirName);
        }

        $files = new FilesystemIterator($path);
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $fileName = $file->getPathname();

                try {
                    unlink($fileName);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }
        }

        if ($path !== $this->cachePath) {
            return rmdir($path);
        }

        return true;
    }
}
