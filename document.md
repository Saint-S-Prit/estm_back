# installation de symfony version 5.2
symfony new nom --full --version=5.2

# creation de projet
symfony new my_project_name --full


# pour creer la table user avec email et mot de pass
php bin/console make: User


# pour creer les tables admin agent etudiant superviseur
php bin/console make: entity User

# pour relier l'appli et la BD
php bin/console doctrine:database:create

# dossier .env.local pour securiser la BD 

# pour creer un fichier de migration
php bin/console make:migration

# pour les tables vers la BD
php bin/console doctrine:migrations:migrate

# pour la creation d'api
composer req api


# [BLOCAGE] = xampp SQLSTATE[HY000]: General error: 2006 MySQL server has gone away
https://www.youtube.com/watch?v=zDaaG8hFYlk

# INSTALL JWT
composer req jwt

# configuration avec deux clés(private, public)
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out var/jwt/public.pem

# CONFIGURATION TOKEN
https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/index.md#installation