<?php

namespace App\Entity;

use App\Repository\SuperviseurRepository;
use App\Entity\User;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SuperviseurRepository::class)
  * @ApiResource(
 *  normalizationContext = {"groups"={"user_read"}},
 *  denormalizationContext = {"groups"={"user_write"}},
 *      collectionOperations = {
 *          "POST" = {"path" = "/superviseur"},
 *          "GET" = {"path" = "/superviseur"},
 *      },
 *      itemOperations = {
 *          "GET" = {"path" = "/superviseur/{id}"},
 *          "DELETE" = {"path" = "/superviseur/{id}/delete"},
 *          "PUT" = {"path" = "/superviseur/{id}/edit"}
 * 
 *      }
 * )
 */
class Superviseur extends User
{
   
}
