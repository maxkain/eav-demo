<?php

namespace App\Entity\Product\Attribute;

use App\Repository\Product\Attribute\MultiEnumValueRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\EavValueInterface;

#[ORM\Entity(repositoryClass: MultiEnumValueRepository::class)]
#[ORM\Table(name: 'product_multi_enum_attribute_value')]
#[ORM\UniqueConstraint(fields: ['attribute', 'title'])]
class MultiEnumValue implements EavValueInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(MultiEnumAttribute::class, inversedBy: 'values')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private MultiEnumAttribute $attribute;

    #[ORM\Column]
    private string $title;

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttribute(): MultiEnumAttribute
    {
        return $this->attribute;
    }

    public function setAttribute(MultiEnumAttribute $attribute): static
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }
}
