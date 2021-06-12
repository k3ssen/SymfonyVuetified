<?php
declare(strict_types=1);

namespace K3ssen\SymfonyVuetified\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class SymfonyVuetifiedExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        if(strtolower($container->getParameter('kernel.environment')) === 'dev') {
            $loader->load('services_dev.yaml');
        }
    }
}
