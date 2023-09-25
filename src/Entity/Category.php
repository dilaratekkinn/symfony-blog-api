<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"category"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     * @JMS\Groups({"category"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @JMS\Type("string")
     * @JMS\Groups({"category"})
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=BlogPost::class, inversedBy="categories")
     */
    private $blogPosts;

    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, BlogPost>
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts[] = $blogPost;
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        $this->blogPosts->removeElement($blogPost);

        return $this;
    }
}
