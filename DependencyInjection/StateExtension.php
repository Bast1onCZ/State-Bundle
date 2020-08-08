<?php
declare(strict_types=1);

namespace BastSys\StateBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class StateExtension
 * @package BastSys\StateBundle\DependencyInjection
 * @author mirkl
 */
class StateExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ .'/../Resources/config'));
        $loader->load('eventListener.yaml');
    }

}
