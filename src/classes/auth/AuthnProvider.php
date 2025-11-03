<?php

declare(strict_types=1);

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\Repository\DeefyRepository;

class AuthnProvider {

    public static function signIn(string $email, string $passwd) : User {
        $repo = DeefyRepository::getInstance();
        $user = $repo->trouverUser($email);

        if ($user === null) {
            throw new AuthnException("Email ou mot de passe incorrect");
        }

        if (!password_verify($passwd, $user->passwd)) { // compare le mdp pas chiffré que rentre l'utilisateur avec celui qui est enregistré dans la bdd sur cet utilisateur
            throw new AuthnException("Email ou mot de passe incorrect");
        }

        return $user;
    }

    /**
     * @throws AuthnException
     */
    public static function register(string $email, string $passwd) : User {

        $repo = DeefyRepository::getInstance();
        $user = $repo->trouverUser($email);

        if ($user !== null) {
            throw new AuthnException("Cet utilisateur existe déjà");
        }

        if (strlen($passwd) < 10) {
            throw new AuthnException("Mot de passe trop court");
        }


        $mdpChiffre = password_hash($passwd, PASSWORD_BCRYPT, ['cost' => 12]);

        $nouvUser = $repo->addUser($email, $mdpChiffre, 1);

        return $nouvUser;
    }

    public static function getSignedInUser() : User {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Aucun utilisateur connecté");
        }
        return $_SESSION['user'];
    }

    public static function isSignedIn() : bool{
        try {
            self::getSignedInUser();
            return true;
        } catch (AuthnException $e) {
            return false;
        }
    }

}