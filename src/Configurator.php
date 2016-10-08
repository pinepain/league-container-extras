<?php

/*
  +----------------------------------------------------------------------+
  | This file is part of the pinepain/container-extras PHP library.      |
  |                                                                      |
  | Copyright (c) 2015-2016 Bogdan Padalko <pinepain@gmail.com>          |
  |                                                                      |
  | Licensed under the MIT license: http://opensource.org/licenses/MIT   |
  |                                                                      |
  | For the full copyright and license information, please view the      |
  | LICENSE file that was distributed with this source or visit          |
  | http://opensource.org/licenses/MIT                                   |
  +----------------------------------------------------------------------+
*/

/* Based on League\Container\Container v1.x class (https://github.com/thephpleague/container/blob/1.x/src/Container.php)
 * which is authored by Phil Bennett (https://github.com/philipobenito)
 * and other contributors (https://github.com/thephpleague/container/contributors).
 */


namespace Pinepain\Container\Extras;


use League\Container\ContainerInterface;
use League\Container\Definition\ClassDefinition;
use League\Container\Definition\DefinitionInterface;
use Pinepain\Container\Extras\Exceptions\InvalidConfigException;
use Traversable;


class Configurator implements ConfiguratorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function configure($config)
    {
        if (!is_array($config) && !($config instanceof Traversable)) {
            throw new InvalidConfigException(
                'You can only load definitions from an array or an object that implements Traversable interface.'
            );
        }

        if (empty($config)) {
            return;
        }

        $this->populateFromTraversable($this->container, $config);
    }

    /**
     * @param ContainerInterface  $container
     * @param array | Traversable $traversable
     */
    protected function populateFromTraversable(ContainerInterface $container, $traversable)
    {
        foreach ($traversable as $alias => $options) {
            $this->createDefinition($container, $options, $alias);
        }
    }

    /**
     * Create a definition from a config entry
     *
     * @param ContainerInterface $container
     * @param  mixed             $options
     * @param  string            $alias
     *
     */
    protected function createDefinition(ContainerInterface $container, $options, $alias)
    {
        $concrete = $this->resolveConcrete($options);

        $share = is_array($options) && !empty($options['share']);

        // define in the container, with constructor arguments and method calls
        $definition = $container->add($alias, $concrete, $share);

        if ($definition instanceof DefinitionInterface) {
            $this->addDefinitionArguments($definition, $options);
        }

        if ($definition instanceof ClassDefinition) {
            $this->addDefinitionMethods($definition, $options);
        }
    }

    protected function addDefinitionArguments(DefinitionInterface $definition, $options)
    {
        $arguments = [];

        if (is_array($options)) {
            $arguments = (array_key_exists('arguments', $options)) ? (array)$options['arguments'] : [];
        }

        $definition->withArguments($arguments);
    }

    protected function addDefinitionMethods(ClassDefinition $definition, $options)
    {
        $methods = [];

        if (is_array($options)) {
            $methods = (array_key_exists('methods', $options)) ? (array)$options['methods'] : [];
        }

        $definition->withMethodCalls($methods);
    }

    /**
     * Resolves the concrete class
     *
     * @param mixed $concrete
     *
     * @return mixed
     */
    protected function resolveConcrete($concrete)
    {
        if (is_array($concrete)) {
            if (array_key_exists('definition', $concrete)) {
                $concrete = $concrete['definition'];
            } elseif (array_key_exists('class', $concrete)) {
                $concrete = $concrete['class'];
            } else {
                $concrete = null;
            }
        }

        // if the concrete doesn't have a class associated with it then it
        // must be either a Closure or arbitrary type so we just bind that
        return $concrete;
    }


}