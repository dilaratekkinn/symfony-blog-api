<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCategoryRequest extends BaseRequest
{
    /**
     * @Assert\NotBlank
     */
    protected $name;
    /**
     * @Assert\NotBlank
     */
    protected $description;
    /**
     *
     * @Assert\NotBlank
     */

}