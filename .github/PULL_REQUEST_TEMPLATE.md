## Description

<!-- Décrivez brièvement les changements apportés et leur motivation -->

## Type de changement

- [ ] `feat` — Nouvelle fonctionnalité
- [ ] `fix` — Correction de bug
- [ ] `test` — Ajout ou correction de tests
- [ ] `ci` — Pipeline CI/CD
- [ ] `docs` — Documentation
- [ ] `refactor` — Refactoring sans changement fonctionnel
- [ ] `chore` — Maintenance (dépendances, config)

## Issue liée

Ferme #<!-- numéro de l'issue -->

## Checklist

- [ ] Le code respecte PHP-CS-Fixer (`@Symfony`) — `vendor/bin/php-cs-fixer fix --dry-run`
- [ ] PHPStan niveau 8 : zéro erreur — `vendor/bin/phpstan analyse --configuration=phpstan.dist.neon`
- [ ] Les tests passent — `vendor/bin/phpunit`
- [ ] La couverture de tests est maintenue ou améliorée (> 70 %)
- [ ] Aucune vulnérabilité — `composer audit`
- [ ] `.env.example` mis à jour si de nouvelles variables ont été ajoutées
