<?php

namespace App\Entity\Product;

use App\Repository\Product\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavTagInterface;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'product_tag')]
class Tag implements EavTagInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $name;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
}
