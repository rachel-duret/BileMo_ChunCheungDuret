<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ...
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *          "getOneProduct",
 *          parameters = { "id" = "expr(object.getId())"}
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"Default", "getProducts"}),
 * )

 * 
 * 
 */

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(['type' => 'integer'])]
    #[Groups(["Default", "getProducts"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["Default", "getProducts"])]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    #[Groups(["Default", "getProducts"])]
    private ?string $model = null;

    #[ORM\Column]
    #[Groups(["Default", "getProducts"])]
    private ?int $price = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["Default", "getProducts"])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["Default", "getProducts"])]
    private ?string $imagePath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }
}
