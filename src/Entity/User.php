<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Since;

/**
 * ...
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *          "getOneUser",
 *          parameters = { "id" = "expr(object.getId())"}
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups="getUsers"),
 * )
 * 
 * @Hateoas\Relation(
 *      "delete",
 *       href = @Hateoas\Route(
 *          "deleteOneUser",
 *          parameters = { "id" = "expr(object.getId())"}
 *       ),
 *         exclusion = @Hateoas\Exclusion(groups="getUsers", excludeIf = "expr(not is_granted('ROLE_USER'))"),
 * )
 * 
 * 
 */

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getUsers"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(["getUsers"])]
    #[Assert\NotBlank(message: "Username is required .")]
    #[Assert\Length(min: 4, max: 255, minMessage: " Username minimun {{ limit }} caracteres", maxMessage: "Username maximun{{ limit }} caracteres")]
    private ?string $username = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(["getUsers"])]
    #[Assert\NotBlank(message: "Email is required .")]
    #[Assert\Email(message: "Have to be valid email .")]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(["getUsers"])]
    //#[Assert\NotBlank(message: "Date is required .")]
    #[Since("2.0")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(["getUsers"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
