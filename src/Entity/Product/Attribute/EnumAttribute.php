<?php

namespace App\Entity\Product\Attribute;

use App\Entity\Product\Category;
use App\Repository\Product\Attribute\EnumAttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavAttributeWithTagsInterface;
use Maxkain\EavBundle\Contracts\Entity\EavEnumAttributeInterface;

#[ORM\Entity(repositoryClass: EnumAttributeRepository::class)]
#[ORM\Table('product_enum_attribute')]
class EnumAttribute implements EavEnumAttributeInterface, EavAttributeWithTagsInterface
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

    /**
     * @var Collection<EnumValue>
     */
    #[ORM\OneToMany(EnumValue::class, mappedBy: 'attribute', cascade: ['persist'], orphanRemoval: true)]
    private Collection $values;

    /**
     * @var Collection<EnumTag>
     */
    #[ORM\OneToMany(EnumTag::class, mappedBy: 'attribute', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tags;

    #[ORM\Column]
    private bool $forAllTags = false;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->values = new ArrayCollection();
    }

    public function getEavTags(string $tagFqcn): iterable
    {
        return match ($tagFqcn) {
            Category::class => $this->tags
        };
    }

    public function isForAllEavTags(string $tagFqcn): bool
    {
        return match ($tagFqcn) {
            Category::class => $this->forAllTags
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

    public function getValues(): Collection
    {
        return $this->values;
    }

    public function addValue(EnumValue $value): static
    {
        $value->setAttribute($this);
        $this->values->add($value);

        return $this;
    }

    public function removeValue(EnumValue $value): static
    {
        $this->values->removeElement($value);

        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(EnumTag $tag): static
    {
        $tag->setAttribute($this);
        $this->tags->add($tag);

        return $this;
    }

    public function removeTag(EnumTag $tag): static
    {
        $this->tags->removeElement($tag);

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
}
