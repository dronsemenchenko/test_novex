<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use App\Validator\UniqueEmail;

class UserRequest extends BaseRequest
{
    //public const SEX = ['female', 'male'];

    #[Assert\NotBlank]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]    
   
    #[UniqueEmail(
        entityClass: User::class,
        field: 'email'        
    )]
    protected string $email;

    #[Assert\NotBlank]
    protected string $name;

    protected int $age;

    #[Assert\NotBlank]
    //#[Assert\Choice(choices: UserRequest::SEX, message: ' {{ value }} Choose a valid SEX from choices {{ choices }}.')]
    protected bool $sex;

    #[Assert\NotBlank]
    #[Assert\Date]
    protected string $birthday;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8,
        max: 11,
        minMessage: 'Your phone must be at least {{ limit }} characters long',
        maxMessage: 'Your phone cannot be longer than {{ limit }} characters'
    )]
    #[Assert\Regex(pattern:"/^[0-9]*$/", message:"number_only") ]    
    protected string $phone;
    
}
