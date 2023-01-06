<?php

namespace MediaPhoto\mf\auth;

use MediaPhoto\mf\router\Router;
use MediaPhoto\mf\exceptions\AuthentificationException;

abstract class AbstractAuthentification
{
    /* la taille minimum des mot de passe */
    const MIN_PASSWORD_LENGTH = 6;

    protected static function loadProfile(int $id): void
    {
        /* 
         * La méthode loadProfile : 
         *
         * Méthode pour enregistrer le profile de utilisateur en session 
         *
         * ATTENTION : cette méthode est appelée uniquement quand la connexion 
         *             réussie par la méthode login (cf. plus bas)
         *
         * Paramètre :
         *    $id : id de l'utilisateur
         * 
         */

        $_SESSION['user_profile']['id'] = $id;
    }



    public static function logout(): void
    {

        /* 
         * la méthode logout :
         * 
         * Méthode pour effectuer la déconnexion, (effacement du 
         * profile de la session) 
         *
         */

        unset($_SESSION['user_profile']);
        unset($_SESSION['idGallery']);
    }


    public static function connectedUser(): ?int
    {

        /* 
         * la méthode connectedUser :
         * 
         * Méthode pour retourner l'ID de l'utilisateur connecté
         *
         */

        if (isset($_SESSION['user_profile']['id']))
            return $_SESSION['user_profile']['id'];
        return null;
    }


    protected static function makePassword(string $password): string
    {

        /* 
         * La méthode makePassword:
         * 
         * Méthode qui vérifie que le $password respecte la politque
         * de mots de passe. (une taille minimale au moins) 
         *
         * Paramètres : 
         * 
         *   $password : le mot de passe choisi par l'utilisateur
         *
         * Retourne : 
         *   le haché du mot de passe (a stoquer en BD)
         *
         * Algorithme :
         *    
         *   Si le mot de passe ne corespond pas la politique 
         *       Soulever une exception
         *   sinon le hacher et retourner son empreinte
         */

        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            //throw new AuthentificationException("La longueur du mot de passe est trop faible. 6 caractères minimum !");
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            return $passwordHash;
        }
    }


    protected static function checkPassword(
        string $given_pass,
        string $db_hash,
        int $id
    ): void {


        /* 
         * La méthode login:
         * 
         * Méthode qui réalise la connexion d'un utilisateur.
         *
         * Paramètres : 
         * 
         *   $given_pass : le mot de passe fourni par l'utilisateur 
         *   $db_hash : le haché du mot de passe stocké en BD
         *   $id : l'identifiant de l'utilisateur
         *   $level : son niveau d'accès
         * 
         * Algorithme :
         *    
         *   Si le mot de passe ne corespond pas au haché 
         *       Soulever une exception
         *   Sinon
         *       charger le profile 
         */
        if (password_verify($given_pass, $db_hash)) {
            self::loadProfile($id);
        } else {
            throw new AuthentificationException("Echec de l'authentification");
        }
    }
}
