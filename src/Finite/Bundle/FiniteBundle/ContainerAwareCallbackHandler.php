<?php

namespace Finite\Event;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Container aware callback handler
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ContainerAwareCallbackHandler extends CallbackHandler implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    protected function call($callback, $object, TransitionEvent $e)
    {
        if (
            null !== $this->container
            && is_array($callback)
            && 0 === strpos($callback[0], '@')
            && $this->container->has(substr($callback[0], 1))
        ) {
            $callback['do'][0] = $this->container->get(substr($callback['do'][0], 1));
        }

        return parent::call($callback, $object, $e);
    }
}
