<?php

/**
 * This file is part of the Infinite Invocation project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ValidationBundle\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates multi-part domain names.
 */
class DomainNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DomainName) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\DomainName');
        }

        if (null === $value) {
            return;
        }

        if (!is_string($value)) {
            $this->context->buildViolation('This value must be a string')
                ->addViolation();

            return;
        }

        $isValid = $this->validateDomain($value);

        if (!$isValid) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }

    private function validateDomain($domain)
    {
        // The domain name must have at least two components.
        // The last component must have only letters.
        // The other components may be alphanumeric and can contain hyphens.

        // Maximum domain name length is 253
        if (strlen($domain) > 253) {
            return false;
        }

        $labels = explode('.', $domain);

        // Must have at least two parts (we won't accept "com" as a domain name)
        if (count($labels) < 2) {
            return false;
        }

        // TLD must be 2-63 letters
        $tld = array_pop($labels);

        if (!preg_match('/^[a-z]{2,63}$/', $tld)) {
            return false;
        }

        // Other labels must have 1-63 alphanumeric chars and may have *internal* hyphens
        foreach ($labels as $label) {
            if (0 === strpos($label, '-') || '-' === substr($label, -1)) {
                return false;
            }

            if (!preg_match('/^[a-z0-9-]{1,63}$/', $label)) {
                return false;
            }
        }

        return true;
    }
}
