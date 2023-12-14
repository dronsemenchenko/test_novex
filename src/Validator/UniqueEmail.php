<?php


namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEmail extends Constraint
{
    public string $entityClass;
    public string $field;
    public string $message = 'This value is already used.';

    public function __construct(
        string $field,
        string $entityClass,
        string $message = null,
        array $groups = null,
        $payload = null,
        array $options = []
    ) {        
        $options['field'] = $field;
        $options['entityClass'] = $entityClass;
        parent::__construct($options, $groups, $payload);        
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function getRequiredOptions(): array
    {
        return ['entityClass', 'field'];
    }

    public function validatedBy(): string
    {
        return get_class($this).'Validator';
    }

}