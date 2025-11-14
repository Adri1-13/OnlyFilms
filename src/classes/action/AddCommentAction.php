<?php

declare(strict_types=1);

namespace iutnc\onlyfilms\action;

use iutnc\onlyfilms\repository\OnlyFilmsRepository;
use iutnc\onlyfilms\auth\AuthnProvider;
use iutnc\onlyfilms\exception\AuthnException;
use iutnc\onlyfilms\exception\OnlyFilmsRepositoryException;

class AddCommentAction extends Action {
    public function executeGet(): string {
        // verif connecté
        if (!isset($_SESSION['user'])) {
            return "<div class='alert alert-warning my-3'>Vous devez être connecté pour ajouter un commentaire.</div>";
        }

        // recup id série
        $serieId = (int)$_GET['serie_id'];

        try {
            $repo = OnlyFilmsRepository::getInstance();
            $serie = $repo->findSerieBySerieId($serieId);
            $serieTitle = $serie->getTitle();
        } catch (OnlyFilmsRepositoryException $e) {
            return "<div class='alert alert-warning my-3'>Série non trouvée.</div>";
        }

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
                                    <select class="form-select" name="note" id="note">
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
                                    <textarea class="form-control" name="comment" id="comment" rows="4" placeholder="Laissez votre avis ici..."></textarea>
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


    public function executePost(): string {
        // verif si connecté
        if (!isset($_SESSION['user'])) {
            return "<div class='alert alert-warning my-3'>Vous devez être connecté pour laisser un commentaire.</div>";
        }

        $userId = $_SESSION['user']->getId();
        $serieId = (int)$_POST['serie_id']; // ID de la série commentée
        $comment_raw = $_POST['comment']; // Le commentaire
        $comment = filter_var($comment_raw, FILTER_SANITIZE_SPECIAL_CHARS);
        $note = (int)$_POST['note']; // La note de 1 à 5

        // TODO FILTER VAR !!

        if ($note < 1 || $note > 5) {
            return "<div class='alert alert-warning my-3'>La note doit être entre 1 et 5.</div>";
        }

        $repository = OnlyFilmsRepository::getInstance();

        try {
            // ajout commentaire
            $repository->addComment($userId, $serieId, $comment, $note);

            return "<div class=' my-4 alert alert-success'>Votre commentaire a été ajouté avec succès !</div>";
        } catch (\Exception $e) {
            return "<div class='alert alert-warning my-3'>Une erreur est survenue lors de l'ajout de votre commentaire.</div>";
        }
    }
}