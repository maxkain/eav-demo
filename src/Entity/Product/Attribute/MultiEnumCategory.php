<?php

namespace App\Entity\Product\Attribute;

use App\Entity\Product\Category;
use App\Repository\Product\Attribute\MultiEnumCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavTagInterface;

#[ORM\Entity(repositoryClass: MultiEnumCategoryRepository::class)]
#[ORM\Table(name: 'product_multi_enum_attribute_category')]
#[ORM\UniqueConstraint(fields: ['attribute', 'tag'])]
class MultiEnumCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(MultiEnumAttribute::class, inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private MultiEnumAttribute $attribute;

    #[ORM\ManyToOne(Category::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Category $tag;

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

    public function setTag(Category $tag): static
    {
        $this->tag = $tag;
        return $this;
    }
}
