<?php

namespace App\Entity\Product\Attribute;

use App\Entity\Product\Product;
use App\Repository\Product\Attribute\MultiStringEavRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\EavAttributeInterface;
use Maxkain\EavBundle\Contracts\Entity\EavInterface;

#[ORM\Entity(repositoryClass: MultiStringEavRepository::class)]
#[ORM\Table('product_multi_string_eav')]
#[ORM\UniqueConstraint(fields: ['entity', 'attribute', 'value'])]
class MultiStringEav implements EavInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(Product::class, inversedBy: 'multiStringEavs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $entity;

    #[ORM\ManyToOne(MultiStringAttribute::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private MultiStringAttribute $attribute;

    #[ORM\Column]
    private string $value;

    public function __toString(): string
    {
        return $this->attribute->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntity(): Product
    {
        return $this->entity;
    }

    public function setEntity(mixed $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    public function getAttribute(): MultiStringAttribute
    {
        return $this->attribute;
    }

    public function setAttribute(EavAttributeInterface|MultiStringAttribute $attribute): static
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }
}
