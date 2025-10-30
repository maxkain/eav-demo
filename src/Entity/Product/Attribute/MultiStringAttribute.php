<?php

namespace App\Entity\Product\Attribute;

use App\Repository\Product\Attribute\MultiStringAttributeRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\EavAttributeInterface;

#[ORM\Entity(repositoryClass: MultiStringAttributeRepository::class)]
#[ORM\Table('product_multi_string_attribute')]
class MultiStringAttribute implements EavAttributeInterface
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
