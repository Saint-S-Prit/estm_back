<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

    private $repoUser;


    public function __construct(UserRepository $repoUser)
    {
        $this->repoUser = $repoUser;
    }


    /**
     * @Route(
     * path="api/user/{email}",
     * name="GetUserByEmail",
     * methods ={"GET"}),
     * defaults= {
     * "__controller="\App\User::GetUserByEmail",
     * "__api_resource_class"=User::class,
     * "__api_collection_operation_name"="user_write"
     * }
     */
    public function GetUserByEmail($email)
    {
        $etudiant =  $this->repoUser->findOneByEmail($email);
        return $this->json($etudiant, 200, [], ['groups' => 'user_read']);
    }
}
