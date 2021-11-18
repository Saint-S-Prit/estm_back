<?php

namespace App\Controller;

use App\Entity\Superviseur;
use App\Repository\ProfileRepository;
use App\Repository\SuperviseurRepository;
use App\Service\UpdateEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SuperviseurController extends AbstractController
{
  private $repoSuprviseur;
  private $denormalizer;
  private $repoProfile;
  private $encoder;
  private $updateService;


  public function __construct(SuperviseurRepository $repoSuprviseur, DenormalizerInterface $denormalizer, ProfileRepository $repoProfile, UserPasswordEncoderInterface $encoder, UpdateEntity $updateService)
  {
    $this->repoSuprviseur = $repoSuprviseur;
    $this->denormalizer = $denormalizer;
    $this->repoProfile = $repoProfile;
    $this->encoder = $encoder;
    $this->updateService = $updateService;
  }

  /**
   * @Route(
   * path="api/superviseur",
   * name="superviseurAdd",
   * methods ={"POST"}),
   * defaults= {
   * "__controller="\App\Superviseur::superviseurAdd",
   * "__api_resource_class"=Superviseur::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function SuperviseurAdd(Request $request, EntityManagerInterface $entityManager)
  {
    $data =  $request->request->all();
    if ($request->files->get('avatar')) {
      $data['avatar'] = fopen(($request->files->get('avatar')), 'rb');
      $profile = $this->repoProfile->findOneByLibelle('superviseur');
      $superviser = $this->denormalizer->denormalize($data, 'App\Entity\Superviseur');
      $superviser->setProfile($profile);

      // on va encoder le mot de passe
      $encoded = $this->encoder->encodePassword($superviser, $superviser->getPassword());

      //modifer le mot de passe original avec celui encoder
      $superviser->setPassword($encoded);


      $entityManager->persist($superviser);
      $entityManager->flush();
      return $this->json("superviser créé avec succuès");
    } else {
      return $this->json("ou est le ndey photo");
    }
  }


  /**
   * @Route(
   * path="api/superviseur",
   * name="GetSuperviseurs",
   * methods ={"GET"}),
   * defaults= {
   * "__controller="\App\Superviseur::GetSuperviseurs",
   * "__api_resource_class"=Superviseur::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function GetSuperviseurs()
  {
    $superviseur =  $this->repoSuprviseur->findAll();
    return $this->json($superviseur, 200, [], ['groups' => 'user_read']);
  }

  /**
   * @Route(
   * path="api/superviseur/{id}/edite",
   * name="editSuperviseur",
   * methods ={"PUT"}),
   * defaults= {
   * "__controller="\App\Superviseur::editSuperviseur",
   * "__api_resource_class"=Superviseur::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function editSuperviseur(int $id, Request $request, $fileType = "avatar", EntityManagerInterface $entityManager)
  {
    $user =  $this->repoSuprviseur->findOneById($id);
    if ($user) {
      $data = $this->updateService->update($request, $fileType);
      foreach ($data as $key => $value) {
        if ($key !== 'profile') {
          $method = 'set' . ucfirst($key);
          if (method_exists($user, $method)) {
            if ($key == 'password') {
              $user->$method($this->encoder->encodePassword($user, $value));
            } else {
              $user->$method($value);
            }
          }
        }
      }
      $entityManager->persist($user);
      $entityManager->flush();
      return $this->json("mise à jour effectué !");
    } else {
      return $this->json("tal ndem id bi bakhoul");
    }
  }
}
