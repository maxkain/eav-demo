<?php

namespace App\Entity\Product\Attribute;

use App\Entity\Product\Product;
use App\Repository\Product\Attribute\StringEavRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\EavAttributeInterface;
use Maxkain\EavBundle\Contracts\Entity\EavInterface;
use Maxkain\EavBundle\Contracts\Entity\EavEntityInterface;

#[ORM\Entity(repositoryClass: StringEavRepository::class)]
#[ORM\Table('product_string_eav')]
#[ORM\UniqueConstraint(fields: ['entity', 'attribute'])]
class StringEav implements EavInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(Product::class, inversedBy: 'stringEavs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $entity;

    #[ORM\ManyToOne(StringAttribute::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private StringAttribute $attribute;

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

    public function setEntity(EavEntityInterface|Product $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    public function getAttribute(): StringAttribute
    {
        return $this->attribute;
    }

    public function setAttribute(StringAttribute|EavAttributeInterface $attribute): static
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
