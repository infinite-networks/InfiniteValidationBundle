<?php

/**
 * This file is part of the Infinite Invocation project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Infinite\ValidationBundle\Constraint;

use Infinite\ValidationBundle\Constraint\ValidExpression;
use Infinite\ValidationBundle\Constraint\ValidExpressionValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ValidExpressionValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new ValidExpressionValidator();
    }

    public function testNullValid()
    {
        $this->validator->validate(null, new ValidExpression());
        $this->assertNoViolation();
    }

    public function testRequiresString()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(new \stdClass(), new ValidExpression());
    }

    public function testRequiresCorrectConstraint()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('google.com', new NotBlank());
    }

    public function testExpression()
    {
        $this->validator->validate('true', new ValidExpression());

        $this->assertNoViolation();
    }

    public function testInvalidExpression()
    {
        $this->validator->validate('"', new ValidExpression());

        $this->assertCount(1, $violations = $this->context->getViolations());
        $this->assertEquals('property.path', $violations->get(0)->getPropertyPath());
    }

    public function testMissingVariableExpression()
    {
        $this->validator->validate('purple', new ValidExpression([
            'variableNames' => ['orange'],
        ]));

        $this->assertCount(1, $violations = $this->context->getViolations());
        $this->assertEquals('property.path', $violations->get(0)->getPropertyPath());
    }

    public function testVariableExpression()
    {
        $this->validator->validate('orange', new ValidExpression([
            'variableNames' => ['orange'],
        ]));

        $this->assertNoViolation();
    }
}
