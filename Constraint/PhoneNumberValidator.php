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

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber as RealPhoneNumber;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PhoneNumberValidator extends ConstraintValidator
{
    private $defaultRegion;
    private $phoneUtil;

    public function __construct(PhoneNumberUtil $phoneUtil, $defaultRegion)
    {
        $this->phoneUtil = $phoneUtil;
        $this->defaultRegion = $defaultRegion;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PhoneNumber) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\PhoneNumber');
        }

        if (!$value) {
            return;
        }

        if (!is_string($value) && !$value instanceof RealPhoneNumber) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\PhoneNumber');
        }

        try {
            $value = !$value instanceof RealPhoneNumber
                ? $this->phoneUtil->parseAndKeepRawInput($value, $this->defaultRegion)
                : $value;

            if (!$this->phoneUtil->isValidNumber($value)) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        } catch (NumberParseException $e) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
