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

use Infinite\ValidationBundle\Constraint\PhoneNumber;
use Infinite\ValidationBundle\Constraint\PhoneNumberValidator;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PhoneNumberValidatorTest extends ConstraintValidatorTestCase
{
    /** @var PhoneNumberUtil */
    private $util;

    protected function createValidator()
    {
        return new PhoneNumberValidator($this->util = PhoneNumberUtil::getInstance(), 'AU');
    }

    public function testNullValid()
    {
        $this->validator->validate(null, new PhoneNumber());
        $this->assertNoViolation();
    }

    public function testRequiresString()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(new \stdClass(), new PhoneNumber());
    }

    public function testRequiresCorrectConstraint()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('google.com', new NotBlank());
    }

    /**
     * @dataProvider getInvalidNumbers
     */
    public function testInvalid($number)
    {
        $this->validator->validate($number, new PhoneNumber());

        $this->assertCount(1, $violations = $this->context->getViolations());
        $this->assertEquals('property.path', $violations->get(0)->getPropertyPath());
    }

    public function getInvalidNumbers()
    {
        return [
            ['purple'],
            ['61266666'],
            ['9961261111'],
            ['+6126173133'],
            ['+61261731332314'],
        ];
    }

    /**
     * @dataProvider getValidNumbers
     */
    public function testValid($number)
    {
        $this->validator->validate($number, new PhoneNumber());

        $this->assertNoViolation();
    }

    public function getValidNumbers()
    {
        return [
            ['+64212345678'],
            ['0261731337'],
            ['+61261731337'],
        ];
    }
}
