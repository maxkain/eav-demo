<?php

namespace App\Entity\Product\Attribute;

use App\Entity\Product\Product;
use App\Repository\Product\Attribute\EnumEavRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\EavAttributeInterface;
use Maxkain\EavBundle\Contracts\Entity\EavInterface;
use Maxkain\EavBundle\Contracts\Entity\EavEntityInterface;

#[ORM\Entity(repositoryClass: EnumEavRepository::class)]
#[ORM\Table('product_enum_eav')]
#[ORM\UniqueConstraint(fields: ['entity', 'attribute'])]
class EnumEav implements EavInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(Product::class, inversedBy: 'enumEavs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $entity;

    #[ORM\ManyToOne(EnumAttribute::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private EnumAttribute $attribute;

    #[ORM\ManyToOne(EnumValue::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private EnumValue $value;

    public function __toString(): string
    {
        return $this->value->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntity(): Product
    {
        return $this->entity;
    }

    public function setEntity(EavEntityInterface $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    public function getAttribute(): EnumAttribute
    {
        return $this->attribute;
    }

    public function setAttribute(EavAttributeInterface|EnumAttribute $attribute): static
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getValue(): EnumValue
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }
}
