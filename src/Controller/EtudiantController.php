<?php

namespace App\Controller;

use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use App\Repository\ProfileRepository;
use App\Service\SendMessage;
use App\Service\UpdateEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EtudiantController extends AbstractController
{
  private $repoEtudiant;
  private $repoProfile;
  private $repoFiliere;
  private $denormalizer;
  private $updateService;
  private $encoder;
  private $sendMessage;
  private $mailer;


  public function __construct(
    EtudiantRepository $repoEtudiant,
    DenormalizerInterface $denormalizer,
    FiliereRepository $repoFiliere,
    UpdateEntity $updateService,
    ProfileRepository $repoProfile,
    UserPasswordEncoderInterface $encoder,
    SendMessage $sendMessage,
    \Swift_Mailer $mailer
  ) {
    $this->repoEtudiant = $repoEtudiant;
    $this->repoProfile = $repoProfile;
    $this->repoFiliere = $repoFiliere;
    $this->denormalizer = $denormalizer;
    $this->updateService = $updateService;
    $this->encoder = $encoder;
    $this->sendMessage = $sendMessage;
    $this->mailer = $mailer;
  }

  /**
   * @Route(
   * path="api/etudiant",
   * name="etudiantAdd",
   * methods ={"POST"}),
   * defaults= {
   * "__controller="\App\Etudiant::etudiantAdd",
   * "__api_resource_class"=Etudiant::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function etudiantAdd(Request $request, EntityManagerInterface $entityManager)
  {
    $etudiants =  $this->repoEtudiant->findAll();

    (int)$countEtudiants = count($etudiants);
    (int)$countEtudiants++;
    switch ($countEtudiants) {
      case $countEtudiants <= 9:
        $conter = '0000';
        break;
      case $countEtudiants <= 99:
        $conter = '000';
        break;
      case $countEtudiants <= 999:
        $conter = '00';
        break;
      case $countEtudiants <= 9999:
        $conter = '0';
        break;
    }
    //gérére date inscrit
    $dateCurrent = date("Y");
    $nextDate = $dateCurrent + 1;

    // génére code unique pour etudiant
    $codeEtudiant = 'ESTM-' . $dateCurrent . '-' . $conter . '' . $countEtudiants;


    $data =  $request->request->all();
    if ($request->files->get('avatar')) {
      $data['avatar'] = fopen(($request->files->get('avatar')), 'rb');
      $profile = $this->repoProfile->findOneByLibelle('etudiant');
      $filiere = $this->repoFiliere->findOneByLibelle($data['filiere']);
      unset($data['filiere']);
      $etudiant = $this->denormalizer->denormalize($data, 'App\Entity\Etudiant');
      $etudiant->setAnneeScolaire($dateCurrent . '/' . $nextDate);
      $etudiant->setIne($codeEtudiant);
      $etudiant->setFiliere($filiere);
      $etudiant->setProfile($profile);

      // on va encoder le mot de passe
      $encoded = $this->encoder->encodePassword($etudiant, $etudiant->getPassword());

      //modifer le mot de passe original avec celui encoder
      $etudiant->setPassword($encoded);

      //send massage to user;an accound is create form him
      $this->sendMessage->sendMessageActivatorAccount($this->mailer, $etudiant);

      $entityManager->persist($etudiant);
      $entityManager->flush();


      return $this->json("etudiant créé avec succuès");
    } else {
      return $this->json("ou est le ndey photo");
    }
  }


  /**
   * @Route(
   * path="api/etudiant/{ine}/edite",
   * name="editEtudiant",
   * methods ={"PUT"}),
   * defaults= {
   * "__controller="\App\Superviseur::editEtudiant",
   * "__api_resource_class"=Etudiant::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function editEtudiant(int $ine, Request $request, $fileType = "avatar", EntityManagerInterface $entityManager)
  {
    $user =  $this->repoEtudiant->findOneByIne($ine);
    if ($user) {
      $data = $this->updateService->update($request, $fileType);
      foreach ($data as $key => $value) {
        if ($key !== 'profile' && $key !== 'filiere') {
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


  /**
   * @Route(
   * path="api/etudiant/{ine}/bloque",
   * name="bloqueEtudiant",
   * methods ={"DELETE"}),
   * defaults= {
   * "__controller="\App\Etudiant::bloqueEtudiant",
   * "__api_resource_class"=Etudiant::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function bloqueEtudiant($ine, EntityManagerInterface $entityManager)
  {
    $user =  $this->repoEtudiant->findOneByIne($ine);

    if ($user) {
      if ($user->getIsDeleted() == false) {
        $user->setIsDeleted(true);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json("Etudiant bloqué");
      } else {
        return $this->json("Etudiant  déjà bloqué");
      }
    } else {
      return $this->json("tal ndem id bi bakhoul");
    }
  }

  /**
   * @Route(
   * path="api/etudiant/{ine}/debloque",
   * name="DeblocEtudiant",
   * methods ={"DELETE"}),
   * defaults= {
   * "__controller="\App\Etudiant::DeblocEtudiant",
   * "__api_resource_class"=Etudiant::class,
   * "__api_collection_operation_name"="user_write"
   * }
   */
  public function DeblocEtudiant($ine, EntityManagerInterface $entityManager)
  {
    $user =  $this->repoEtudiant->findOneByIne($ine);

    if ($user) {
      if ($user->getIsDeleted() == true) {
        $user->setIsDeleted(false);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json("Etudiant actif");
      } else {
        return $this->json("Etudiant est déjà actif");
      }
    } else {
      return $this->json("tal ndem id bi bakhoul");
    }
  }
}
