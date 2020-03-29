<?php
declare(strict_types=1);

namespace App\Twig;

use App\Router\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Alternative to the original RoutingExtension: it's practically the same, but it doesn't enforce an array as parameter,
 * so that objects can be passed as well.
 */
class RoutingExtension extends AbstractExtension
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', [$this, 'getUrl']),
            new TwigFunction('path', [$this, 'getPath']),
        ];
    }

    public function getPath(string $name, $parameters = [], bool $relative = false): string
    {
        return $this->router->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function getUrl(string $name, $parameters = [], bool $schemeRelative = false): string
    {
        return $this->router->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
