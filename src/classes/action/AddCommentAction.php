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
        // Vérification si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            return "<p>Vous devez être connecté pour ajouter un commentaire.</p>";
        }

        // Récupérer l'ID de la série à partir du GET
        $serieId = (int)$_GET['serie_id'];

        // Ici, on peut récupérer les informations de la série depuis la base de données si nécessaire
        // Pour l'exemple, on assume que la série a un titre que nous pouvons afficher
        $serieTitle = "Titre de la série";  // À remplacer par une vraie requête BDD pour récupérer le titre

        // Afficher le formulaire pour ajouter un commentaire
        return <<<HTML
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="card-title text-center mb-4">Noter la série</h2>
                            <h5 class="card-subtitle mb-3 text-muted text-center">{$serieTitle}</h5>
                            
                            <form action="?action=add-comment" method="POST">
                                <input type="hidden" name="serie_id" value="{$serieId}" />
                                
                                <div class="mb-3">
                                    <label for="note" class="form-label">Votre note :</label>
                                    <select class="form-select" name="note" id="note" required>
                                        <option value="" disabled selected>-- Choisir une note --</option>
                                        <option value="5">★★★★★ (5 étoiles)</option>
                                        <option value="4">★★★★☆ (4 étoiles)</option>
                                        <option value="3">★★★☆☆ (3 étoiles)</option>
                                        <option value="2">★★☆☆☆ (2 étoiles)</option>
                                        <option value="1">★☆☆☆☆ (1 étoile)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="comment" class="form-label">Votre commentaire :</label>
                                    <textarea class="form-control" name="comment" id="comment" rows="4" placeholder="Laissez votre avis ici..." required></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Envoyer</button>
                                    <a href="?action=display-serie&serie-id={$serieId}" class="btn btn-outline-secondary">Annuler</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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