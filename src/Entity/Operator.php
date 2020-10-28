<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OperatorRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OperatorRepository::class)
 * @ApiResource(
 *      attributes={"pagination_items_per_page"=20, "order"={"createdAt": "desc"}},
 *      normalizationContext={"groups"={"operator:get"}, "disable_type_enforcement"=true},
 *      denormalizationContext={"groups"={"operator:post"}, "disable_type_enforcement"=true},
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
class Operator extends User
{

}
