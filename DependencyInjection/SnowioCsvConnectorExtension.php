<?php

namespace Snowio\Bundle\CsvConnectorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SnowioCsvConnectorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('steps.yml');
        $loader->load('writers.yml');
        $loader->load('jobs.yml');
        $loader->load('form_parameters.yml');
        $loader->load('job_defaults.yml');
        $loader->load('job_constraints.yml');
    }
}
