<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\repository\OnlyFilmsRepository;
use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\AuthnException;

class AddCommentAction extends Action
{
    public function executeGet(): string
    {
            // La méthode GET pourrait rediriger vers la page de commentaire d'une série si nécessaire.
            // On pourrait afficher un formulaire vide ou un message d'avertissement ici.
        return <<<HTML
        <h1>Ajouter un commentaire</h1>
        <p>Pour ajouter un commentaire, vous devez d'abord être connecté.</p>
        HTML;
    }

    public function executePost(): string
    {
        // Vérification si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            return "<p>Vous devez être connecté pour laisser un commentaire.</p>";
        }

        // Récupération des données du formulaire
        $userId = $_SESSION['user']->getId();
        $serieId = (int)$_POST['serie_id']; // ID de la série commentée
        $comment = $_POST['comment']; // Le commentaire
        $note = (int)$_POST['note']; // La note de 1 à 5

        // Vérification de la note
        if ($note < 1 || $note > 5) {
            return "<p>La note doit être entre 1 et 5.</p>";
        }

        // Interaction avec le repository pour enregistrer le commentaire et la note
        $repository = OnlyFilmsRepository::getInstance();

        try {
            // Ajouter un commentaire et une note à la série
            $repository->addComment($userId, $serieId, $comment, $note);
            
            // Retour à la page de la série avec un message de succès
            return "<p>Votre commentaire a été ajouté avec succès!</p>";
        } catch (\Exception $e) {
            // En cas d'erreur, afficher un message
            return "<p>Une erreur est survenue lors de l'ajout de votre commentaire.</p>";
        }
    }
}
