<?php

namespace App\Entity\Product\Attribute;

use App\Entity\Product\Tag;
use App\Repository\Product\Attribute\MultiEnumTagRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavAttributeTagInterface;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavTagInterface;

#[ORM\Entity(repositoryClass: MultiEnumTagRepository::class)]
#[ORM\Table(name: 'product_multi_enum_attribute_tag')]
#[ORM\UniqueConstraint(fields: ['attribute', 'tag'])]
class MultiEnumTag implements EavAttributeTagInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(MultiEnumAttribute::class, inversedBy: 'tags')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private MultiEnumAttribute $attribute;

    #[ORM\ManyToOne(Tag::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Tag $tag;

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

    public function getTag(): EavTagInterface
    {
        return $this->tag;
    }

    public function setTag(Tag $tag): static
    {
        $this->tag = $tag;
        return $this;
    }
}
