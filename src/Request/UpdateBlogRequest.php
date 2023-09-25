<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateBlogRequest extends BaseRequest
{
    /**
     * @Assert\NotBlank
     */
    protected $title;
    /**
     * @Assert\NotBlank
     */
    protected $content;
    /**
     *
     * @Assert\NotBlank
     */
    protected $categories;
    /**
     *
     * @Assert\NotBlank
     */
    protected $tags;
    /**
     *
     * @Assert\NotBlank
     */
}