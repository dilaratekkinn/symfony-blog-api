<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest extends BaseRequest
{
    /**
     * @Assert\NotBlank
     */
    protected $email;
    /**
     * @Assert\NotBlank
     */
    protected $password;

}