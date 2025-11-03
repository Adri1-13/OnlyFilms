<?php

declare(strict_types=1);

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\Repository\DeefyRepository;

class Authz {

    public static function checkRole(int $roleAttendu) : void {
        $user = AuthnProvider::getSignedInUser();

        if ($user->role < $roleAttendu) {
            throw new AuthnException("Vous n'avez pas les droits");
        }

    }

    public static function checkPlaylistOwner(int $idPlaylist) : void {
        $user = AuthnProvider::getSignedInUser();

//        echo "User : {$user->email}";

        if ($user->role >= 100) {
            return;
        }

        $repo = DeefyRepository::getInstance();
        $tabPlaylistsDuUser = $repo->trouverToutesLesPlaylistsD_unUser($user->id);

        if (!in_array($idPlaylist, $tabPlaylistsDuUser)) {
            throw new AuthnException("Vous n'avez pas accès à cette playlist");
        }

    }
}