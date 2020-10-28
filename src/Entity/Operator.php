<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OperatorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OperatorRepository::class)
 */
class Operator extends User
{

}
