<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use stdClass;

final class Home extends Common
{
  public function homepage(Request $request, Response $response, $args): Response
  {
    global $basePath;

    $view = Twig::fromRequest($request);

    $myItem = new \App\Models\Home();

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);

    // table homes
    // vérifier si il y a des entrées pour l'utilisateur ($GLOBALS['user_id'])
    // si non, vérifier si il y a une entrée pour son profile ($GLOBALS['profile_id'])
    // si non, prendre les entrées avec user_id et profile_id = 0

    // L'affichage se fait sur plusieurs colonnes, on va en mettre 3 par défaut, mais si un utilisateur a un grand écran, il peut en mettre ce qu'il veut (limit de 10 quand même)

    // le but est d'avoir une affichage par défaut.
    // Possibilité d'en créer un pour un profile
    // chaque utilisateur pourra créer sa home page si il veut.

    // Modules :


    // mytickets : Mes tickets en cours
    // groupstickets : Les tickets de mon groupe
    // lastescaladedtickets : Mes derniers tickets escaladés
    // knowledgelink : Un lien vers la base de co
    // lastknowledgeitems : Les derniers articles publiés en base de co
    // lastproblems : Les problèmes
    // todayincidents : Le nombre d'incidents créés le jour J
    // linkedincidents : Le nombre d'incidents avec des incidents liés ou dupliqués
    // ????? : (voir avec Marie-Noëlle ce qu'elle veut dire) Ou le nombre d'incidents résultants d'un catégories ou d'un tag PBG
    // forms : la liste des formulaires, rangés par catégories, un peu comme notre portail (ce panel pourrait prendre 2 colonnes)

    // pour les last, peut être afficher les 8 derniers, pas trop sûr de ça encore


    return $view->render($response, 'home.html.twig', (array)$viewData);
  }
}
