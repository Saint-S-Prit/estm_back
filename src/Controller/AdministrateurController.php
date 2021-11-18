<?php

namespace App\Controller;

use App\Repository\AdministrateurRepository;
use App\Repository\ProfileRepository;
use App\Service\UpdateEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AdministrateurController extends AbstractController
{
  private $repoAdmin;
  private $repoProfile;
  private $denormalizer;
  private $encoder;
  private $updateService;
  public function __construct(AdministrateurRepository $repoAdmin, DenormalizerInterface $denormalizer, ProfileRepository $repoProfile, UserPasswordEncoderInterface $encoder, UpdateEntity $updateService)
  {
    $this->repoAdmin = $repoAdmin;
    $this->repoProfile = $repoProfile;
    $this->denormalizer = $denormalizer;
    $this->encoder = $encoder;
    $this->updateService = $updateService;
  }

  /**
   * @Route(
   * path="api/administrateur",
   * name="administrateurAdd",
   * methods ={"POST"}),
   * defaults= {
   * "__controller="\App\Administrateur::administrateurAdd",
   * "__api_resource_class"=Administrateur::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function administrateurAdd(Request $request, EntityManagerInterface $entityManager)
  {
    //pour recupérer les données de la requete
    $data =  $request->request->all();

    //vérifions si la requete contient un ficher(avatar)
    if ($request->files->get('avatar')) {

      //convertir le fichier en blob
      $data['avatar'] = fopen(($request->files->get('avatar')), 'rb');

      //trouver le role du user
      $profile = $this->repoProfile->findOneByLibelle('administrateur');

      //transformer les données objet en json
      $admin = $this->denormalizer->denormalize($data, 'App\Entity\Administrateur');

      // on va encoder le mot de passe
      $encoded = $this->encoder->encodePassword($admin, $admin->getPassword());

      //modifer le mot de passe original avec celui encoder
      $admin->setPassword($encoded);

      // modifier le profile
      $admin->setProfile($profile);

      //préparer la requete d'insertion
      $entityManager->persist($admin);

      //executé la requete d'insertion
      $entityManager->flush();

      // si les données sont sont bien insserréés; on retourne le message de succés
      return $this->json("administrateur créé avec succuès");
    } else {
      return $this->json("ou est le ndey photo");
    }
  }


  /**
   * @Route(
   * path="api/administrateur",
   * name="GetAdministrateurs",
   * methods ={"GET"}),
   * defaults= {
   * "__controller="\App\Administrateur::GetAdministrateurs",
   * "__api_resource_class"=Administrateur::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function GetAdministrateurs()
  {
    $admins =  $this->repoAdmin->findAll();
    return $this->json($admins, 200, [], ['groups' => 'user_read']);
  }



  /**
   * @Route(
   * path="api/administrateur/{id}/delete",
   * name="DeleteAdministrateur",
   * methods ={"DELETE"}),
   * defaults= {
   * "__controller="\App\Administrateur::DeleteAdministrateur",
   * "__api_resource_class"=Administrateur::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function DeleteAdministrateur(int $id, EntityManagerInterface $entityManager)
  {
    $admin =  $this->repoAdmin->findOneById($id);

    if ($admin) {
      $admin->setIsDeleted(true);
      $entityManager->persist($admin);
      $entityManager->flush();
      return $this->json("suppression valide");
    } else {
      return $this->json("tal ndem id bi bakhoul");
    }
  }


  /**
   * @Route(
   * path="api/administrateur/{id}/edite",
   * name="editAdministrateur",
   * methods ={"PUT"}),
   * defaults= {
   * "__controller="\App\Administrateur::editAdministrateur",
   * "__api_resource_class"=Administrateur::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function editAdministrateur(int $id, Request $request, $fileType = "avatar", EntityManagerInterface $entityManager)
  {
    $user =  $this->repoAdmin->findOneById($id);
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
