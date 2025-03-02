<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Tickettemplate
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name' => $translator->translate('Name'),
      'comment' => $translator->translate('Comments'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(4, $t['comment'], 'textarea', 'comment', fillable: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Ticket template', 'Ticket templates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Mandatory field', 'Mandatory fields', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/mandatoryfields',
      ],
      /*
Array
(
    [1] => Titre
    [21] => Description
    [12] => Statut
    [10] => Urgence
    [11] => Impact
    [3] => Priorité
    [15] => Date d'ouverture
    [4] => Demandeur
    [71] => Groupe demandeur
    [5] => Technicien
    [8] => Groupe de techniciens
    [6] => Assigné à un fournisseur
    [66] => Observateur
    [65] => Groupe observateur
    [7] => Catégorie
    [13] => Éléments associés
    [-2] => Demande de validation
    [142] => Documents
    [9] => Source de la demande
    [83] => Lieu
    [37] => SLAs TTO
    [30] => SLAs TTR
    [190] => OLA TTO interne
    [191] => OLA TTR interne
    [18] => TTR
    [155] => TTO
    [180] => TTR interne
    [185] => TTO interne
    [45] => Durée totale
    [52] => Validation
    [14] => Type
)
Array
(
    [0] => 1
    [1] => 21
    [2] => 10
    [3] => 83
    [4] => -1
    [5] => 7
    [6] => 14
    [7] => -1
    [8] => 142
    [9] => 66
)
      */
      [
        'title' => $translator->translatePlural('Predefined field', 'Predefined fields', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/predefinedfields',
      ],
      /*
Array
(
    [1] => Titre
    [21] => Description
    [12] => Statut
    [10] => Urgence
    [11] => Impact
    [3] => Priorité
    [15] => Date d'ouverture
    [4] => Demandeur
    [71] => Groupe demandeur
    [5] => Technicien
    [8] => Groupe de techniciens
    [6] => Assigné à un fournisseur
    [66] => Observateur
    [65] => Groupe observateur
    [7] => Catégorie
    [131] => Types d'élément associé
    [13] => Éléments associés
    [142] => Documents
    [175] => Tâche
    [9] => Source de la demande
    [83] => Lieu
    [37] => SLAs TTO
    [30] => SLAs TTR
    [190] => OLA TTO interne
    [191] => OLA TTR interne
    [18] => TTR
    [155] => TTO
    [180] => TTR interne
    [185] => TTO interne
    [45] => Durée totale
    [52] => Validation
    [14] => Type
)
      */
      [
        'title' => $translator->translatePlural('Hidden field', 'Hidden fields', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/hiddenfields',
      ],
      /*
Array
(
    [1] => Titre
    [21] => Description
    [12] => Statut
    [10] => Urgence
    [11] => Impact
    [3] => Priorité
    [15] => Date d'ouverture
    [4] => Demandeur
    [71] => Groupe demandeur
    [5] => Technicien
    [8] => Groupe de techniciens
    [6] => Assigné à un fournisseur
    [66] => Observateur
    [65] => Groupe observateur
    [13] => Éléments associés
    [-2] => Demande de validation
    [142] => Documents
    [9] => Source de la demande
    [83] => Lieu
    [37] => SLAs TTO
    [30] => SLAs TTR
    [190] => OLA TTO interne
    [191] => OLA TTR interne
    [18] => TTR
    [155] => TTO
    [180] => TTR interne
    [185] => TTO interne
    [45] => Durée totale
    [52] => Validation
)
      */
      [
        'title' => $translator->translate('Standard interface'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Simplified interface'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('ITIL category', 'ITIL categories', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
