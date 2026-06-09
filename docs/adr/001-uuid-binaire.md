# ADR-001 — UUID BINARY(16) pour les clés primaires

**Date :** 2026-05-01  
**Statut :** Accepté

---

## Contexte

Le projet nécessite des identifiants uniques pour les entités `User`, `Task` et `Attachment`. Les options envisagées sont :

- **Auto-increment INT** : simple, séquentiel, prévisible en URL
- **UUID v4 stocké en VARCHAR(36)** : unique mais verbeux (36 octets par champ, index lents)
- **UUID v4 stocké en BINARY(16)** : unique, compact (16 octets), performant sur les index

## Décision

Utilisation d'UUID v4 stockés en **BINARY(16)** via `symfony/uid` et Doctrine.

```php
#[ORM\Id]
#[ORM\Column(type: 'uuid', unique: true)]
#[ORM\GeneratedValue(strategy: 'CUSTOM')]
#[ORM\CustomIdGenerator(class: UuidGenerator::class)]
private ?Uuid $id = null;
```

Le type Doctrine `uuid` mappe automatiquement sur `BINARY(16)` en MySQL 8.

## Conséquences

**Avantages :**
- Identifiants non prédictibles → sécurité accrue (pas d'enumération d'IDs en URL)
- Index B-tree sur 16 octets → performance comparable à un INT sur les volumétries attendues
- Génération côté application → pas de round-trip vers la base pour obtenir l'ID

**Inconvénients / points d'attention :**
- Les requêtes DQL avec Doctrine ORM 3 nécessitent de passer l'UUID explicitement avec son type :
  ```php
  // Incorrect avec Doctrine ORM 3 (renvoie 0 résultat)
  ->setParameter('user', $user)
  
  // Correct
  ->setParameter('user', $user->getId(), UuidType::NAME)
  ```
- Les logs et debugs affichent des UUIDs en base64, moins lisibles qu'un INT

## Alternatives rejetées

- **Auto-increment** : IDs prédictibles, couplage fort à la séquence MySQL
- **VARCHAR(36)** : 2,25× plus lourd que BINARY(16) sur chaque index, jointures plus lentes
