<?php

declare(strict_types=1);

use App\Factory\LoggerFactory;
use App\Model\Email;
use App\Provider\SendMail;
use Carbon\Carbon;
use Darkalchemy\Twig\TwigCompiler;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Views\Twig;
use Spatie\Url\Url;

/**
 * @param array $array
 * @param array $keys
 *
 * @return array
 */
function array_clean(array $array, array $keys): array
{
    return array_intersect_key($array, array_flip($keys));
}

/**
 * @param ContainerInterface $container
 *
 * @throws NotFoundExceptionInterface
 * @throws ContainerExceptionInterface
 * @throws Exception
 *
 * @return int
 */
function compile_twig_templates(ContainerInterface $container): int
{
    $settings    = $container->get('settings');
    $twig_config = $settings['twig'];
    $cache       = $twig_config['cache'] ?? VIEWS_DIR . 'cache/';
    $twig        = $container->get(Twig::class)->getEnvironment();
    $compiler    = new TwigCompiler($twig, $cache);

    $compiler->compile();

    return 0;
}

/**
 * @param string $text
 * @param mixed  ...$replacements
 *
 * @return string
 */
function __f(string $text, ...$replacements): string
{
    global $i18n;

    return $i18n->translateFormatted($text, ...$replacements);
}

/**
 * @param string $text
 * @param mixed  ...$replacements
 *
 * @return string
 */
function __fe(string $text, ...$replacements): string
{
    global $i18n;

    return $i18n->translateFormattedExtended($text, ...$replacements);
}

/**
 * @param string $text
 * @param string $alternative
 * @param int    $count
 *
 * @return string
 */
function __p(string $text, string $alternative, int $count): string
{
    global $i18n;

    return $i18n->translatePlural($text, $alternative, $count);
}

/**
 * @param string $text
 * @param string $alternative
 * @param int    $count
 * @param mixed  ...$replacements
 *
 * @return string
 */
function __pf(string $text, string $alternative, int $count, ...$replacements): string
{
    global $i18n;

    return $i18n->translatePluralFormatted($text, $alternative, $count, ...$replacements);
}

/**
 * @param string $text
 * @param string $alternative
 * @param int    $count
 * @param mixed  ...$replacements
 *
 * @return string
 */
function __pfe(string $text, string $alternative, int $count, ...$replacements): string
{
    global $i18n;

    return $i18n->translatePluralFormattedExtended($text, $alternative, $count, ...$replacements);
}

/**
 * @param string $text
 * @param string $context
 *
 * @return string
 */
function __c(string $text, string $context): string
{
    global $i18n;

    return $i18n->translateWithContext($text, $context);
}

/**
 * @param string $text
 *
 * @return string
 */
function __m(string $text): string
{
    global $i18n;

    return $i18n->markForTranslation($text);
}

/**
 * @param int $bytes
 * @param int $precision
 *
 * @return string
 */
function human_readable_size(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    for ($i = 0; $bytes > 1024; ++$i) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * @param ContainerInterface $container
 *
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function remove_cached_files(ContainerInterface $container): void
{
    $settings    = $container->get('settings');
    $twig_config = $settings['twig'];
    $cache       = $twig_config['cache'] ?? VIEWS_DIR . 'cache/';
    if (file_exists($cache)) {
        removeDirectory($cache, false);
    }
    removeDirectory($settings['di_compilation_path'], false);
    removeDirectory(dirname($settings['router_cache_file']), false);
}

/**
 * @param null|string $path
 * @param bool        $removePath
 *
 * @return bool
 */
function removeDirectory(?string $path, bool $removePath): bool
{
    if (empty($path)) {
        return true;
    }
    $iterator = new DirectoryIterator($path);
    foreach ($iterator as $fileInfo) {
        if ($fileInfo->isDot() || !$fileInfo->isDir()) {
            continue;
        }
        $dirName = $fileInfo->getPathname();
        removeDirectory($dirName, true);
    }

    $files = new DirectoryIterator($path);
    $types = ['php', 'cache'];
    foreach ($files as $file) {
        if (in_array($file->getExtension(), $types)) {
            unlink($file->getPathname());
        }
    }

    if ($removePath) {
        return rmdir($path);
    }

    return true;
}

/**
 * sendEmail function.
 *
 * @param ContainerInterface $container
 *
 * @return bool
 */
function sendEmail(ContainerInterface $container): bool
{
    $email     = $container->get(Email::class);
    $emails    = $email
        ->with('user')
        ->with('activations')
        ->where('sent', 0)
        ->orderBy('priority')
        ->orderBy('created_at')
        ->take(10)
        ->get();
    $sendmail      = $container->get(SendMail::class);
    $loggerFactory = $container->get(LoggerFactory::class);
    $logger        = $loggerFactory->addFileHandler('sendmail_error.log')->createInstance('sendMail');
    $twig          = $container->get(Twig::class);

    foreach ($emails as $item) {
        $data  = [
            'email' => $item->user->email,
            'code'  => $item->activations->code,
        ];
        $params = http_build_query($data);
        $link   = $item->uri . $params;
        $sendmail->addRecipient($item->user->email, $item->user->username);
        $sendmail->setSubject($item->subject);
        // {% set link = cli_full_url_for('auth.activate', {}, {'email': user.email, 'code': code}) %}
        $sendmail->setMessage($twig->fetch('email/auth/password/activate.twig', [
            'user' => [
                'username' => $item->user->username,
            ],
            'link' => $link,
        ]));

        try {
            $sendmail->send();
            $email->where('id', $item->id)
                ->increment('send_count', 1, [
                    'sent'       => 1,
                    'date_sent'  => Carbon::now(),
                ]);
            $logger->success('SendMail Successful: ' . $item->user->email . ' ' . $item->subject);
        } catch (Exception $e) {
            $email->find($item->id)->increment('error_count');
            $logger->error('SendMail Failed: ' . $e->getMessage());
        }
    }

    return true;
}
