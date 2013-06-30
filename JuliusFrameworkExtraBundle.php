<?php

/*
 * This file is part of the JuliusFrameworkExtraBundle package.
 *
 * (c) Eymen Gunay <eymen@egunay.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Julius\FrameworkExtraBundle;

use Julius\FrameworkExtraBundle\DependencyInjection\Compiler\FormPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JuliusFrameworkExtraBundle extends Bundle
{
	/**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FormPass());
    }
}
