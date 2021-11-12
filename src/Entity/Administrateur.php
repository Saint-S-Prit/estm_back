<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AdministrateurRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=AdministrateurRepository::class)
  * @ApiResource(
 *  normalizationContext = {"groups"={"user_read"}},
 *  denormalizationContext = {"groups"={"user_write"}},
 *      collectionOperations = {
 *          "POST" = {"path" = "/administrateur"},
 *          "GET" = {"path" = "/administrateur"},
 *      },
 *      itemOperations = {
 *          "GET" = {"path" = "/administrateur/{id}"},
 *          "DELETE" = {"path" = "/administrateur/{id}/delete"},
 *          "PUT" = {"path" = "/administrateur/{id}/edite"},
 * 
 *      }
 * )
 */
class Administrateur extends User
{
    
}
