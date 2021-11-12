<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class FiliereController extends AbstractController
{
    
   
    private $repoFiliere;
    private $denormalizer;


    public function __construct(FiliereRepository $repoFiliere, DenormalizerInterface $denormalizer)
    {
      $this->repoFiliere = $repoFiliere;
      $this->denormalizer = $denormalizer;
    }

    /**
    * @Route(
    * path="api/filiere",
    * name="filiereAdd",
    * methods ={"POST"}),
    * defaults= {
    * "__controller="\App\Filiere::filiereAdd",
    * "__api_resource_class"=Filiere::class,
    * "__api_collection_operation_name"="filiere_write"
    * }
    */
    public function agentAdd(Request $request, EntityManagerInterface $entityManager)
    {
        $requestData = \json_decode($request->getContent(), true);
        $libelle = strtoupper($requestData['libelle']);
        $filiere = new Filiere();
        $filiere-> setLibelle($libelle);
         $entityManager->persist($filiere);
         $entityManager->flush();
         return $this->json("filiere créé avec succuès");
      
    }


    /**
    * @Route(
    * path="api/filiere",
    * name="GetFilieres",
    * methods ={"GET"}),
    * defaults= {
    * "__controller="\App\Filiere::GetFilieres",
    * "__api_resource_class"=Filiere::class,
    * "__api_collection_operation_name"="filiere_write"
    * }
    */
    public function GetFilieres()
    {
       $filiere =  $this->repoFiliere->findAll();
       return $this->json($filiere, 200,[], ['groups' => 'filiere_read']);
      
    }
}
