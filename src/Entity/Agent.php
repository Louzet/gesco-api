<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AgentRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=AgentRepository::class)
 * @ApiResource(
 *      attributes={"pagination_items_per_page"=20, "order"={"createdAt": "desc"}},
 *      normalizationContext={"groups"={"agent:get"}, "disable_type_enforcement"=true},
 *      denormalizationContext={"groups"={"agent:post"}, "disable_type_enforcement"=true},
 *      collectionOperations={
 *          "get",
 *          "post"
 *      },
 *      itemOperations={
 *          "get",
 *          "put",
 *          "delete"
 *      }
 * )
 */
class Agent extends User implements UserInterface
{

    /**
     * @ORM\Column(type="string")
     * @Groups({"agent:post"})
     */
    private ?string $password;

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
