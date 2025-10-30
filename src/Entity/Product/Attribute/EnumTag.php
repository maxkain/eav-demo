<?php

namespace App\Entity\Product\Attribute;

use App\Entity\Product\Category;
use App\Repository\Product\Attribute\EnumTagRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavAttributeTagInterface;

#[ORM\Entity(repositoryClass: EnumTagRepository::class)]
#[ORM\Table(name: 'product_enum_attribute_tag')]
#[ORM\UniqueConstraint(fields: ['attribute', 'tag'])]
class EnumTag implements EavAttributeTagInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(EnumAttribute::class, inversedBy: 'tags')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private EnumAttribute $attribute;

    #[ORM\ManyToOne(Category::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Category $tag;

    /**
     * @var bool Any option
     */
    #[ORM\Column]
    private bool $showInFilter = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttribute(): EnumAttribute
    {
        return $this->attribute;
    }

    public function setAttribute(EnumAttribute $attribute): static
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getTag(): Category
    {
        return $this->tag;
    }

    public function setTag(Category $tag): static
    {
        $this->tag = $tag;
        return $this;
    }

    public function isShowInFilter(): bool
    {
        return $this->showInFilter;
    }

    public function setShowInFilter(bool $showInFilter): static
    {
        $this->showInFilter = $showInFilter;
        return $this;
    }
}
