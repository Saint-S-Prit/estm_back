<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\AgentRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgentRepository::class)
  * @ORM\Entity(repositoryClass=AdministrateurRepository::class)
  * @ApiResource(
 *  normalizationContext = {"groups"={"user_read"}},
 *  denormalizationContext = {"groups"={"user_write"}},
 *      collectionOperations = {
 *          "POST" = {"path" = "/agent"},
 *          "GET" = {"path" = "/agent"},
 *      },
 *      itemOperations = {
 *          "GET" = {"path" = "/agent/{id}"},
 *          "DELETE" = {"path" = "/agent/{id}/delete"},
 *          "PUT" = {"path" = "/agent/{id}/edit"}
 *      }
 * )
 */
class Agent extends User
{
    
}
