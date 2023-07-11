<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture extends AbstractEntity
{
    /**
     * @var File|null
     */
    private ?File $file = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Groups(['product:list'])]
    private ?string $path = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $alt = null;

    /**
     * @var Product|null
     */
    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Product $product = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return $this
     */
    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     * @return $this
     */
    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return $this
     */
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param string|null $imageThumbnailUrl
     * @return $this
     */
    public function setThumbnail(?string $imageThumbnailUrl): self
    {
        $this->thumbnail = $imageThumbnailUrl;

        return $this;
    }
}
