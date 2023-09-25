<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequest extends BaseRequest
{
    /**
     * @Assert\NotBlank
     */
    protected $email;
    /**
     * @Assert\NotBlank
     */
    protected $password;
    /**
     *
     * @Assert\NotBlank
     */
    protected $name;

}