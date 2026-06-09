# ADR-002 — Soft Delete sur l'entité Task

**Date :** 2026-05-01  
**Statut :** Accepté

---

## Contexte

Les utilisateurs doivent pouvoir supprimer des tâches. Deux approches sont possibles :

- **Hard delete** : `DELETE FROM task WHERE id = ?` — suppression définitive
- **Soft delete** : ajout d'un champ `deleted_at DATETIME NULL` — suppression logique

## Décision

Utilisation du **soft delete** via un champ `deletedAt` sur `Task`.

```php
#[ORM\Column(nullable: true)]
private ?\DateTimeImmutable $deletedAt = null;

public function softDelete(): void
{
    $this->deletedAt = new \DateTimeImmutable();
}

public function isDeleted(): bool
{
    return $this->deletedAt !== null;
}
```

Toutes les requêtes du `TaskRepository` filtrent automatiquement les tâches supprimées via un `QueryBuilder` partagé :

```php
private function createActiveQB(): QueryBuilder
{
    return $this->createQueryBuilder('t')
        ->where('t.deletedAt IS NULL');
}
```

## Conséquences

**Avantages :**
- Traçabilité : on sait quand une tâche a été supprimée et par qui
- Restauration possible sans intervention en base de données
- Intégrité référentielle : les `Attachment` liés restent cohérents
- Protection contre les suppressions accidentelles

**Inconvénients / points d'attention :**
- Les tables grossissent avec le temps ; une purge périodique des entrées anciennes est à prévoir
- Chaque requête doit filtrer `deletedAt IS NULL` — géré centralement par `createActiveQB()`
- Les tests fonctionnels doivent vérifier que les tâches soft-deletées n'apparaissent plus dans les listes

## Alternatives rejetées

- **Hard delete** : perte définitive des données, pas de traçabilité, risque de violer des contraintes FK si des pièces jointes sont encore référencées
- **Doctrine Extensions SoftDeletable** : surcharge de dépendance pour un comportement trivial à implémenter manuellement
