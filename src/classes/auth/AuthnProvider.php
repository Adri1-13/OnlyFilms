<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\auth;

use iutnc\onlyfilms\exception\AuthnException;
use iutnc\onlyfilms\Repository\OnlyFilmsRepository;

class AuthnProvider {

    public static function signIn(string $mail, string $passwd) : User {
        $repo = OnlyFilmsRepository::getInstance();
        $user = $repo->findUser($mail);

        if ($user === null) {
            throw new AuthnException("Email ou mot de passe incorrect");
        }

        if (!password_verify($passwd, $user->getPasswd())) { // compare le mdp pas chiffré que rentre l'utilisateur avec celui qui est enregistré dans la bdd sur cet utilisateur
            throw new AuthnException("Email ou mot de passe incorrect");
        }

        return $user;
    }

    /**
     * @throws AuthnException
     */
    public static function register(string $mail, string $passwd, string $name, string $firstname) : User {

        $repo = OnlyFilmsRepository::getInstance();
        $user = $repo->findUser($mail);

        if ($user !== null) {
            throw new AuthnException("Cet utilisateur existe déjà");
        }

        if (strlen($passwd) < 10) {
            throw new AuthnException("Mot de passe trop court");
        }

        $mdpChiffre = password_hash($passwd, PASSWORD_BCRYPT, ['cost' => 12]);

        // CORRECTION : Passer les paramètres dans le bon ordre (mail, passwd, name, firstname, role)
        $nouvUser = $repo->addUser($mail, $mdpChiffre, $name, $firstname, 1);

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