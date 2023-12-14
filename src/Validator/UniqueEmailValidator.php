<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

use Doctrine\Persistence\ManagerRegistry;

class UniqueEmailValidator extends ConstraintValidator
{
    private ManagerRegistry $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new UnexpectedTypeException($constraint, UniqueEmail::class);
        }

        if(!$constraint->entityClass) {
            throw new InvalidArgumentException('Entity class is required.');
        }

        if (!is_scalar($constraint->field)) {
            throw new \InvalidArgumentException('"field" parameter should be any scalar type');
        }

        $repository = $this->em->getRepository($constraint->entityClass);

        $searchResults = $repository->findBy([
            $constraint->field => $value
        ]);

        if (count($searchResults) > 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
