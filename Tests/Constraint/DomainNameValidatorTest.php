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

use Infinite\ValidationBundle\Constraint\DomainName;
use Infinite\ValidationBundle\Constraint\DomainNameValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class DomainNameValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new DomainNameValidator();
    }

    public function testNullValid()
    {
        $this->validator->validate(null, new DomainName());
        $this->assertNoViolation();
    }

    public function testRequiresString()
    {
        $this->validator->validate(new \stdClass(), new DomainName());
        $this->assertCount(1, $violations = $this->context->getViolations());
        $this->assertEquals('property.path', $violations->get(0)->getPropertyPath());
    }

    public function testRequiresCorrectConstraint()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('google.com', new NotBlank());
    }

    /**
     * @dataProvider getDomainNameTests
     *
     * @param $domainName
     * @param $expectSuccess
     */
    public function testDomainNames($domainName, $expectSuccess)
    {
        $this->validator->validate($domainName, new DomainName());

        if ($expectSuccess) {
            $this->assertNoViolation();
        } else {
            $this->assertCount(1, $violations = $this->context->getViolations());
            $this->assertEquals('property.path', $violations->get(0)->getPropertyPath());
        }
    }

    public function getDomainNameTests()
    {
        return [
            ['caffeinatedhusky.com',    true],
            ['caffeinatedhusky.com.au', true],
            ['caffeinatedhusky.dog',    true],
            ['caffeinated.husky.com',   true],
            ['caffeinated-husky.com',   true],
            ['caffeinatedhusky123.com', true],

            ['CAFFEINATEDHUSKY.com',    false],
            ['caffeinated_husky.com',   false],
            ['caffeinatedhusky.q',      false],
            ['caffeinatedhusky-.com',   false],
            ['-caffeinatedhusky.com',   false],
            ['caffeinatedhusky',        false],
            ['caffeinatedhusky.com.',   false],
            ['caffeinatedhusky.',       false],
            ['caffeinatedhusky..',      false],
            ['.caffeinatedhusky.com',   false],
            ['ćâfféïñãtédhûský.com',    false],
            ['',                        false],
            ['caffeinatedhusky .com',   false],
            ['caffeinatedhusky. com',   false],
            ['part-that-is-too-long-aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.com', false],
            ['domain-that-is-too-long.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.aaaaaaaaaaaaaaaaaa.com', false],
        ];
    }
}
