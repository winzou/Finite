<?php

namespace Finite\Bundle\FiniteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SerializedParsedExpression;

/**
 * Replace '@xxx' with the defined service if exists in loader configs
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ParseExpressionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $loaders = $container->findTaggedServiceIds('finite.loader');
        $language = new ExpressionLanguage();

        foreach ($loaders as $id => $loader) {
            $definition = $container->getDefinition($id);
            $config = $definition->getArgument(0);
            if (isset($config['callbacks'])) {
                foreach (array('before', 'after') as $position) {
                    foreach ($config['callbacks'][$position] as &$callback) {
                        foreach ($callback['args'] as &$arg) {
                            //$arg = array(serialize($language->parse($arg, array('object', 'event'))));
                            $arg = $language->compile($arg, array('object', 'event'));
                        }
                    }
                }

                $definition->replaceArgument(0, $config);
            }
        }
    }
}
