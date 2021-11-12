<?php

namespace App\Controller;

use App\Repository\AgentRepository;
use App\Repository\ProfileRepository;
use App\Service\UpdateEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AgentController extends AbstractController
{
   
    private $repoAgent;
    private $repoProfile;
    private $denormalizer;
    private $encoder;
    private $updateService;


    public function __construct(AgentRepository $repoAgent, DenormalizerInterface $denormalizer, ProfileRepository $repoProfile, UserPasswordEncoderInterface $encoder, UpdateEntity $updateService)
    {
      $this->repoAgent = $repoAgent;
      $this->repoProfile = $repoProfile;
      $this->denormalizer = $denormalizer;
      $this->encoder = $encoder;
      $this->updateService = $updateService;
    }

    /**
    * @Route(
    * path="api/agent",
    * name="agentAdd",
    * methods ={"POST"}),
    * defaults= {
    * "__controller="\App\Agent::agentAdd",
    * "__api_resource_class"=Agent::class,
    * "__api_collection_operation_name"="user_write"
    * }
    */
    public function agentAdd(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
       $data =  $request->request->all();
       if ($request->files->get('avatar')) {
         $data['avatar'] = fopen(($request->files->get('avatar')), 'rb');
         $profile = $this->repoProfile->findOneByLibelle('agent');
         $agent = $this->denormalizer->denormalize($data, 'App\Entity\Agent');
         $agent->setProfile($profile);

          // on va encoder le mot de passe
          $encoded = $this->encoder->encodePassword($agent, $agent->getPassword());

          //modifer le mot de passe original avec celui encoder
          $agent->setPassword($encoded);
 
         $entityManager->persist($agent);
         $entityManager->flush();
         return $this->json("agent créé avec succuès");
       }
       else
       {
        return $this->json("ou est le ndey photo");
       }
      
    }


    /**
    * @Route(
    * path="api/agent",
    * name="GetAgents",
    * methods ={"GET"}),
    * defaults= {
    * "__controller="\App\Administrateur::GetAgents",
    * "__api_resource_class"=Agent::class,
    * "__api_collection_operation_name"="user_write"
    * }
    */
    public function GetAgents()
    {
       $agents =  $this->repoAgent->findAll();
       return $this->json($agents, 200,[], ['groups' => 'user_read']);
      
    }

    
    /**
    * @Route(
    * path="api/agent/{id}/edite",
    * name="editAgent",
    * methods ={"PUT"}),
    * defaults= {
    * "__controller="\App\Agent::editAgent",
    * "__api_resource_class"=Agent::class,
    * "__api_collection_operation_name"="user_write"
    * }
    */
    public function editAgent(int $id , Request $request ,$fileType = "avatar", EntityManagerInterface $entityManager)
    {
      // dd($id);
       $user =  $this->repoAgent->findOneById($id);
      //  dd($user);
      if ($user)
      {
        $data = $this->updateService->update($request, $fileType);
        foreach ($data as $key => $value) {
          if ($key !== 'profile') {
            $method = 'set'.ucfirst($key);
              if (method_exists($user, $method)) {
                if ($key == 'password') {
                  $user->$method($this->encoder->encodePassword($user, $value));
                }
                else {
                  $user->$method($value);
                }
              }
          }
        }
        $entityManager->persist($user);
        $entityManager->flush();
       return $this->json("mise à jour effectué !");
      }
      else
      {
        return $this->json("tal ndem id bi bakhoul");
      }
    }
}
