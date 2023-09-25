<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateCategoryRequest extends BaseRequest
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