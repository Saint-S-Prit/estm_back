<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EtudiantRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EtudiantRepository::class)
  * @ApiResource(
 *  normalizationContext = {"groups"={"user_read"}},
 *  denormalizationContext = {"groups"={"user_write"}},
 *      collectionOperations = {
 *          "POST" = {"path" = "/etudiant"},
 *          "GET" = {"path" = "/agenetudiant"},
 *      },
 *      itemOperations = {
 *          "GET" = {"path" = "/etudiant/{id}"},
 *          "DELETE" = {"path" = "/etudiant/{id}/delete"},
 *          "PUT" = {"path" = "/etudiant/{id}/edite"},
 *      }
 * )
 */
class Etudiant extends User
{


    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_write","user_read"})
     */
    private $niveau;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_write","user_read"})
     * 
     */
    private $dateNaiss;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_write","user_read"})
     * 
     */
    private $ine;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_read"})
     * 
     */
    private $anneeScolaire;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_write","user_read"})
     * 
     */
    private $nationalite;

    /**
     * @ORM\ManyToOne(targetEntity=Filiere::class, inversedBy="etudiants")
     * @Groups({"user_write","user_read"})
     */
    private $filiere;


    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getDateNaiss(): ?string
    {
        return $this->dateNaiss;
    }

    public function setDateNaiss(string $dateNaiss): self
    {
        $this->dateNaiss = $dateNaiss;

        return $this;
    }

    public function getIne(): ?string
    {
        return $this->ine;
    }

    public function setIne(string $ine): self
    {
        $this->ine = $ine;

        return $this;
    }

    public function getAnneeScolaire(): ?string
    {
        return $this->anneeScolaire;
    }

    public function setAnneeScolaire(string $anneeScolaire): self
    {
        $this->anneeScolaire = $anneeScolaire;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(string $nationalite): self
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }
}
