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


namespace Pinepain\Container\Extras;


use League\Container\Argument\ArgumentResolverInterface;
use League\Container\Argument\ArgumentResolverTrait;
use League\Container\Exception\NotFoundException;
use League\Container\ImmutableContainerAwareTrait;


class AliasContainer implements ArgumentResolverInterface, AliasContainerInterface
{
    use ArgumentResolverTrait;
    use ImmutableContainerAwareTrait;

    private $aliases = [];

    public function __construct(array $aliases = [])
    {
        $this->aliases = $aliases;
    }

    /**
     * {@inheritdoc}
     */
    public function add($alias, $id)
    {
        $this->aliases[$alias] = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, array $args = [])
    {
        if (!$this->has($id)) {
            throw new NotFoundException(
                sprintf('Alias (%s) is not registered and therefore cannot be resolved', $id)
            );
        }

        return $this->getContainer()->get($this->aliases[$id], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return array_key_exists($id, $this->aliases);
    }
}