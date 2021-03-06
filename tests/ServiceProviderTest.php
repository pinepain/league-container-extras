<?php

/*
  +------------------------------------------------------------------------+
  | This file is part of the pinepain/league-container-extras PHP library. |
  |                                                                        |
  | Copyright (c) 2015-2016 Bogdan Padalko <pinepain@gmail.com>            |
  |                                                                        |
  | Licensed under the MIT license: http://opensource.org/licenses/MIT     |
  |                                                                        |
  | For the full copyright and license information, please view the        |
  | LICENSE file that was distributed with this source or visit            |
  | http://opensource.org/licenses/MIT                                     |
  +------------------------------------------------------------------------+
*/


namespace Pinepain\League\Container\Extras\Tests;


use Pinepain\League\Container\Extras\ServiceProvider;


class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \League\Container\ContainerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $container;
    /**
     * @var ServiceProvider
     */
    private $obj;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('\League\Container\ContainerInterface')
                                ->setMethods(['get', 'has'])
                                ->getMockForAbstractClass();

        $this->obj = new ServiceProvider();

        $this->obj->setContainer($this->container);
    }

    public function testAdd()
    {
        $s = $this->obj;

        /** @var \League\Container\ServiceProvider\ServiceProviderInterface | \PHPUnit_Framework_MockObject_MockObject $resolved */
        $resolved = $this->getMockBuilder('\League\Container\ServiceProvider\ServiceProviderInterface')
                         ->setMethods(['provides'])
                         ->getMockForAbstractClass();

        $resolved->expects($this->once())
                 ->method('provides')
                 ->willReturn(['service']);

        $this->container->expects($this->once())
                        ->method('has')
                        ->with('test')
                        ->willReturn(true);

        $this->container->expects($this->once())
                        ->method('get')
                        ->with('test')
                        ->willReturn($resolved);

        $this->assertFalse($s->provides('service'));

        $s->add('test');

        $this->assertTrue($s->provides('service'));
    }
}
