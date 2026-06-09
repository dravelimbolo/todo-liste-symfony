# Guide de contribution

## Branches

| Branche | Rôle |
|---------|------|
| `main` | Production — merge squash depuis `develop` uniquement |
| `develop` | Intégration — cible par défaut des PR |
| `feature/<slug>` | Nouvelle fonctionnalité (`feature/task-attachments`) |
| `fix/<slug>` | Correction de bug (`fix/csrf-token-session`) |
| `docs/<slug>` | Documentation (`docs/adr-soft-delete`) |
| `ci/<slug>` | Pipeline CI/CD (`ci/phpstan-level8`) |

## Conventions de commit

Format : `<type>(<scope>): <sujet> #<issue>`

| Type | Usage |
|------|-------|
| `feat` | Nouvelle fonctionnalité |
| `fix` | Correction de bug |
| `test` | Ajout ou correction de tests |
| `ci` | Modifications du pipeline CI/CD |
| `docs` | Documentation uniquement |
| `refactor` | Refactoring sans ajout de fonctionnalité |
| `perf` | Amélioration de performance |
| `chore` | Maintenance (dépendances, config) |

**Règles :**
- Sujet en minuscules, sans point final
- Verbe à l'infinitif (`ajouter`, `corriger`, `supprimer`)
- Référencer l'issue avec `#<n>` en fin de ligne

**Exemples :**
```
feat(task): ajouter la gestion des pièces jointes #6
fix(auth): corriger la redirection après déconnexion #3
test(task): couvrir TaskService.delete avec soft delete #8
ci: ajouter PHPStan niveau 8 au pipeline #9
docs: rédiger ADR pour UUID binaire #10
```

## Processus de contribution

1. **Créer une branche** depuis `develop`
   ```bash
   git checkout develop && git pull
   git checkout -b feature/<slug>
   ```

2. **Développer** en respectant les standards :
   - PHP-CS-Fixer : `vendor/bin/php-cs-fixer fix`
   - PHPStan niveau 8 : `vendor/bin/phpstan analyse --configuration=phpstan.dist.neon`
   - Tests : `vendor/bin/phpunit`
   - Audit : `composer audit`

3. **Pousser** la branche et ouvrir une Pull Request vers `develop`
   ```bash
   git push -u origin feature/<slug>
   gh pr create --base develop
   ```

4. **Merge squash** après validation CI — la branche est supprimée automatiquement.

## Standards de code

- **PHP-CS-Fixer** : ruleset `@Symfony` défini dans `.php-cs-fixer.dist.php`
- **PHPStan** : niveau 8, zéro erreur (`phpstan.dist.neon`)
- **Types stricts** : `declare(strict_types=1)` dans chaque fichier
- **DTOs** pour tous les formulaires (pas d'entités directement liées)
- **Services** pour la logique métier (contrôleurs fins)
- **Voters** pour le contrôle d'accès aux ressources

## Tests

- Couverture minimale : **70 %** sur les services
- Tests unitaires dans `tests/Unit/Service/` — pas de base de données (mocks PHPUnit)
- Tests fonctionnels dans `tests/Functional/Controller/` — base de données `todo_liste_test`
- Chaque test crée ses propres fixtures et nettoie via `tearDown()`

## Variables d'environnement

Copier `.env.example` en `.env` et remplir les valeurs. Ne jamais commiter `.env`.
