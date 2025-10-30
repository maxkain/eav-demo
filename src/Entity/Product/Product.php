<?php

namespace App\Entity\Product;

use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Attribute\MultiStringEav;
use App\Entity\Product\Attribute\StringEav;
use App\Repository\Product\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavEntityWithTagsInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Assert\Cascade]
class Product implements EavEntityWithTagsInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $name;

    #[ORM\ManyToOne(Category::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Category $category;

    /**
     * @var Collection<Tag>
     */
    #[ORM\ManyToMany(Tag::class)]
    #[ORM\JoinTable(name: 'product_product_tag')]
    private Collection $tags;

    /**
     * @var Collection<StringEav>
     */
    #[Assert\Valid]
    #[ORM\OneToMany(StringEav::class, 'entity', cascade: ['persist'], orphanRemoval: true)]
    private Collection $stringEavs;

    /**
     * @var Collection<MultiStringEav>
     */
    #[Assert\Valid]
    #[ORM\OneToMany(MultiStringEav::class, 'entity', cascade: ['persist'], orphanRemoval: true)]
    private Collection $multiStringEavs;

    /**
     * @var Collection<EnumEav>
     */
    #[Assert\Valid]
    #[ORM\OneToMany(EnumEav::class, 'entity', cascade: ['persist'], orphanRemoval: true)]
    private Collection $enumEavs;

    /**
     * @var Collection<MultiEnumEav>
     */
    #[Assert\Valid]
    #[ORM\OneToMany(MultiEnumEav::class, 'entity', cascade: ['persist'], orphanRemoval: true)]
    private Collection $multiEnumEavs;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->stringEavs = new ArrayCollection();
        $this->multiStringEavs = new ArrayCollection();
        $this->enumEavs = new ArrayCollection();
        $this->multiEnumEavs = new ArrayCollection();
    }

    public function getEavTags(string $tagFqcn): iterable
    {
        return match ($tagFqcn) {
            Category::class => isset($this->category) ? [$this->category] : [],
            Tag::class => $this->tags,
            default => [],
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        $this->tags->add($tag);
        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    public function getStringEavs(): Collection
    {
        return $this->stringEavs;
    }

    public function getMultiStringEavs(): Collection
    {
        return $this->multiStringEavs;
    }

    public function getEnumEavs(): Collection
    {
        return $this->enumEavs;
    }

    public function getMultiEnumEavs(): Collection
    {
        return $this->multiEnumEavs;
    }
}
