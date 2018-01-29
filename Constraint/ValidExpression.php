<?php

/**
 * This file is part of the Infinite invocation project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ValidationBundle\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class ValidExpression extends Constraint
{
    public $message = 'The expression failed to compile';
    public $variableNames = [];
}
