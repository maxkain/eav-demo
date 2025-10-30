<?php

namespace App\Entity\Product\Attribute;

use App\Entity\Product\Product;
use App\Repository\Product\Attribute\MultiEnumEavRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\EavInterface;
use Maxkain\EavBundle\Contracts\Entity\EavEntityInterface;

#[ORM\Entity(repositoryClass: MultiEnumEavRepository::class)]
#[ORM\Table('product_multi_enum_eav')]
#[ORM\UniqueConstraint(fields: ['entity', 'attribute', 'value'])]
class MultiEnumEav implements EavInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(Product::class, inversedBy: 'multiEnumEavs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $entity;

    #[ORM\ManyToOne(MultiEnumAttribute::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private MultiEnumAttribute $attribute;

    #[ORM\ManyToOne(MultiEnumValue::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private MultiEnumValue $value;

    public function __toString(): string
    {
        return $this->value->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntity(): EavEntityInterface
    {
        return $this->entity;
    }

    public function setEntity(EavEntityInterface $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    public function getAttribute(): MultiEnumAttribute
    {
        return $this->attribute;
    }

    public function setAttribute(mixed $attribute): static
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getValue(): MultiEnumValue
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }
}
