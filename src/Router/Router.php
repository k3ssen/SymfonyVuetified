<?php
declare(strict_types=1);

namespace App\Router;

use App\Entity\Interfaces\IdentifiableInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router as BaseRouter;
use Symfony\Component\Routing\RouterInterface;

class Router implements RouterInterface, CacheWarmerInterface
{
    /** @var BaseRouter */
    protected $router;

    public function __construct(BaseRouter $router)
    {
        $this->router = $router;
    }

    public static function create(BaseRouter $router) {
        return new Router($router);
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if (is_object($parameters)) {
            $idName = 'id';
            if ($parameters instanceof IdentifiableInterface) {
                $idName = $parameters::getIdName();
            }
            $getter = 'get'.ucfirst($idName);
            if (method_exists($parameters, $getter)) {
                $parameters = [
                    $idName => $parameters->$getter(),
                ];
            }
        }
        return $this->router->generate($name, $parameters, $referenceType);
    }

    public function setContext(RequestContext $context)
    {
        $this->router->setContext($context);
    }

    public function getContext()
    {
        return $this->router->getContext();
    }

    public function getRouteCollection()
    {
        return $this->router->getRouteCollection();
    }

    public function match($pathinfo)
    {
        return $this->router->match($pathinfo);
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * Warms up the cache.
     */
    public function warmUp(string $cacheDir)
    {
        // TODO: Implement warmUp() method.
    }
}