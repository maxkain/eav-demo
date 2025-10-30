<?php

namespace App\Entity\Product\Attribute;

use App\Entity\Product\Category;
use App\Entity\Product\Tag;
use App\Repository\Product\Attribute\MultiEnumAttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\EavEnumAttributeInterface;
use Doctrine\Common\Collections\Collection;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavAttributeWithTagsInterface;

#[ORM\Entity(repositoryClass: MultiEnumAttributeRepository::class)]
#[ORM\Table('product_multi_enum_attribute')]
class MultiEnumAttribute implements EavEnumAttributeInterface, EavAttributeWithTagsInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $name;

    /**
     * @var Collection<MultiEnumValue>
     */
    #[ORM\OneToMany(MultiEnumValue::class, mappedBy: 'attribute', cascade: ['persist'], orphanRemoval: true)]
    private Collection $values;

    /**
     * @var Collection<MultiEnumTag>
     */
    #[ORM\OneToMany(MultiEnumTag::class, mappedBy: 'attribute', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tags;

    /**
     * @var Collection<MultiEnumCategory>
     */
    #[ORM\OneToMany(MultiEnumCategory::class, mappedBy: 'attribute', cascade: ['persist'], orphanRemoval: true)]
    private Collection $categories;

    #[ORM\Column]
    private bool $forAllTags = true;

    #[ORM\Column]
    private bool $forAllCategories = true;

    public function __toString(): string
    {
        return $this->name;
    }

    public function isForAllEavTags(string $tagFqcn): bool
    {
        return match ($tagFqcn) {
            Tag::class => $this->forAllTags,
            Category::class => $this->forAllCategories
        };
    }

    public function getEavTags(string $tagFqcn): iterable
    {
        return match ($tagFqcn) {
            Tag::class => $this->tags,
            Category::class => $this->categories
        };
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

    public function isForAllTags(): bool
    {
        return $this->forAllTags;
    }

    public function setForAllTags(bool $forAllTags): static
    {
        $this->forAllTags = $forAllTags;
        return $this;
    }

    public function isForAllCategories(): bool
    {
        return $this->forAllCategories;
    }

    public function setForAllCategories(bool $forAllCategories): static
    {
        $this->forAllCategories = $forAllCategories;
        return $this;
    }

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->values = new ArrayCollection();
    }

    public function getValues(): Collection
    {
        return $this->values;
    }

    public function addValue(MultiEnumValue $value): static
    {
        $value->setAttribute($this);
        $this->values->add($value);

        return $this;
    }

    public function removeValue(MultiEnumValue $value): static
    {
        $this->values->removeElement($value);

        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(MultiEnumTag $tag): static
    {
        $tag->setAttribute($this);
        $this->tags->add($tag);
        return $this;
    }

    public function removeTag(MultiEnumTag $tag): static
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(MultiEnumCategory $category): static
    {
        $category->setAttribute($this);
        $this->categories->add($category);
        return $this;
    }

    public function removeCategory(MultiEnumCategory $category): static
    {
        $this->categories->removeElement($category);
        return $this;
    }
}
