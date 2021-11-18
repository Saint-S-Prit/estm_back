<?php

namespace App\Service;


class SendMessage
{



    public function sendMessageActivatorAccount(\Swift_Mailer $mailer, $user)
    {

        $url = 'test';
        $message = (new \Swift_Message('Ouverture de votre Compte sur ESTM'))
            ->setFrom('saintespritt@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(

                "
                img
                <hr><h4>Salut " . $user->getPrenom() . ' ' . $user->getNom() . "!</h4> <br>
                <p>
                Votre compte a été créé  pour l'entité ESTM. <br>
                Vous êtes demander de bien vouloir modifier votre mot de passe.
                <hr>
                <h3>Vos identifiants</h3>
                <b>Pseudo</b> :" . $user->getEmail() . "<br>
                <b>Mot de passe</b> :ESTM-2021
                <hr>
                Pour vous connectez, veuillez cliquer ici:  <a href='.$url.'>ici</a></p>",
                'text/html'
            );

        $mailer->send($message);
    }


    public function sendMessageChangePassword(\Swift_Mailer $mailer, $user)
    {
        $message = (new \Swift_Message('Modification mot de passe de votre Compte sur SEN INTERIM'))
            ->setFrom('saintespritt@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(

                "
                <hr><h4>Salut " . $user->getFirstname() . ' ' . $user->getLastname() . "!</h4> <br>
                <p>
                Votre mot de passe pour l'entité SEN INTERIM.  a été changer <br>
                Vous êtes demander de bien vouloir nous contacter en cas d'erreur.
                <hr>
                <h3>Merci</h3>",
                'text/html'
            );

        $mailer->send($message);
    }
}
