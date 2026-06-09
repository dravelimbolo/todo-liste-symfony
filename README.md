<div align="center">

[![Typing SVG](https://readme-typing-svg.demolab.com?font=Inter&weight=900&size=40&duration=2800&pause=1200&color=FFFFFF&center=true&vCenter=true&width=700&height=90&lines=Je+vous+pr%C3%A9sente;Todo+Liste+Symfony)](https://github.com/dravelimbolo/todo-liste-symfony)

**`Projet Full Stack · Symfony 8 · PHP 8.4 · MySQL 8`**

_Une application de gestion de tâches avec authentification, priorités, statuts et pièces jointes._

<br/>

[![Portfolio](https://img.shields.io/badge/-dravelimbolo.com-111111?style=for-the-badge&logo=safari&logoColor=white)](https://dravelimbolo.com)
[![LinkedIn](https://img.shields.io/badge/-LinkedIn-111111?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/in/dravel-imbolo)
[![GitHub](https://img.shields.io/badge/-GitHub-111111?style=for-the-badge&logo=github&logoColor=white)](https://github.com/dravelimbolo)
[![Email](https://img.shields.io/badge/-contact@dravelimbolo.com-111111?style=for-the-badge&logo=gmail&logoColor=white)](mailto:contact@dravelimbolo.com)

<br/>

![Profile views](https://komarev.com/ghpvc/?username=dravelimbolo&color=111111&style=for-the-badge&label=PROFILE+VIEWS)
[![Wakatime](https://wakatime.com/badge/user/68d36f75-1b9c-4f4c-891b-a9e26415562e.svg?style=for-the-badge)](https://wakatime.com/@68d36f75-1b9c-4f4c-891b-a9e26415562e)

<br/>

[![CI](https://github.com/dravelimbolo/todo-liste-symfony/actions/workflows/ci.yml/badge.svg)](https://github.com/dravelimbolo/todo-liste-symfony/actions/workflows/ci.yml)
[![Deploy](https://github.com/dravelimbolo/todo-liste-symfony/actions/workflows/deploy.yml/badge.svg)](https://github.com/dravelimbolo/todo-liste-symfony/actions/workflows/deploy.yml)
[![codecov](https://codecov.io/gh/dravelimbolo/todo-liste-symfony/branch/main/graph/badge.svg)](https://codecov.io/gh/dravelimbolo/todo-liste-symfony)

</div>

---

<div align="center">

_Gérez vos tâches par priorité et statut, assignez-les à des membres de votre équipe et suivez leur avancement, avec un système d'authentification complet et une couverture de tests robuste._

</div>

---

## _Stack technique_

<table align="center">
<tr>
  <td align="center">
    <strong>Backend</strong><br/>
    <img src="https://skillicons.dev/icons?i=php,symfony" />
  </td>
  <td align="center">
    <strong>Frontend</strong><br/>
    <img src="https://skillicons.dev/icons?i=tailwind,js,html,css" />
  </td>
  <td align="center">
    <strong>Base de données</strong><br/>
    <img src="https://skillicons.dev/icons?i=mysql" />
  </td>
  <td align="center">
    <strong>DevOps & Outils</strong><br/>
    <img src="https://skillicons.dev/icons?i=docker,git,github,githubactions,nginx,linux" />
  </td>
</tr>
</table>

---

## _Aperçu_

<table align="center" style="border-spacing:8px;">
<tr>
  <td align="center">
    <img src="docs/screenshots/login.png" width="310" alt="Connexion" style="border-radius:6px;"/>
    <br/><sub>Connexion</sub>
  </td>
  <td align="center">
    <img src="docs/screenshots/register.png" width="310" alt="Inscription" style="border-radius:6px;"/>
    <br/><sub>Inscription</sub>
  </td>
  <td align="center">
    <img src="docs/screenshots/dashboard.png" width="310" alt="Dashboard" style="border-radius:6px;"/>
    <br/><sub>Dashboard</sub>
  </td>
</tr>
<tr>
  <td align="center">
    <img src="docs/screenshots/tasks.png" width="310" alt="Liste des tâches" style="border-radius:6px;"/>
    <br/><sub>Liste des tâches</sub>
  </td>
  <td align="center">
    <img src="docs/screenshots/task-new.png" width="310" alt="Nouvelle tâche" style="border-radius:6px;"/>
    <br/><sub>Nouvelle tâche</sub>
  </td>
  <td align="center">
    <img src="docs/screenshots/profile.png" width="310" alt="Profil" style="border-radius:6px;"/>
    <br/><sub>Profil</sub>
  </td>
</tr>
</table>

---

## _Architecture_

<table style="border-spacing:0; font-size:13px; width:100%;">
<tr>
  <th align="left" style="padding:8px 12px; width:22%;">Dossier</th>
  <th align="left" style="padding:8px 12px;">Contenu</th>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>src/Controller/</code></td>
  <td style="padding:8px 12px;"><code>DashboardController</code> · <code>TaskController</code> · <code>ProfileController</code> · <code>SecurityController</code></td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>src/Entity/</code></td>
  <td style="padding:8px 12px;"><code>User</code> · <code>Task</code> · <code>Attachment</code> : UUID BINARY(16), soft delete sur Task</td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>src/Service/</code></td>
  <td style="padding:8px 12px;"><code>AuthService</code> · <code>TaskService</code> · <code>UserService</code> · <code>DashboardService</code></td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>src/DTO/</code></td>
  <td style="padding:8px 12px;"><code>RegisterDTO</code> · <code>CreateTaskDTO</code> · <code>UpdateProfileDTO</code> · <code>ChangePasswordDTO</code></td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>src/Security/</code></td>
  <td style="padding:8px 12px;"><code>AppAuthenticator</code> · <code>Voter/TaskVoter</code></td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>src/Enum/</code></td>
  <td style="padding:8px 12px;"><code>TaskPriority</code> (LOW · MEDIUM · HIGH · URGENT) · <code>TaskStatus</code> (TODO · IN_PROGRESS · DONE) · <code>ProfileType</code></td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>tests/</code></td>
  <td style="padding:8px 12px;">Unit (<code>AuthServiceTest</code> · <code>TaskServiceTest</code> · <code>DashboardServiceTest</code>) · Functional (<code>SecurityControllerTest</code> · <code>TaskControllerTest</code>)</td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>docs/adr/</code></td>
  <td style="padding:8px 12px;"><code>001-uuid-binaire.md</code> · <code>002-soft-delete.md</code></td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>.github/</code></td>
  <td style="padding:8px 12px;"><code>workflows/ci.yml</code> · <code>workflows/deploy.yml</code> · <code>PULL_REQUEST_TEMPLATE.md</code></td>
</tr>
</table>

---

## _Installation_

<table align="center" style="border-spacing:0; font-size:13px; width:100%;">
<tr>
  <th align="left" style="padding:8px 12px;">Étape</th>
  <th align="left" style="padding:8px 12px;">Commande</th>
  <th align="left" style="padding:8px 12px;">Description</th>
</tr>
<tr>
  <td align="center" style="padding:8px 12px;"><code>1</code></td>
  <td style="padding:8px 12px;"><code>git clone https://github.com/dravelimbolo/todo-liste-symfony.git && cd todo-liste-symfony</code></td>
  <td style="padding:8px 12px;">Cloner le dépôt</td>
</tr>
<tr>
  <td align="center" style="padding:8px 12px;"><code>2</code></td>
  <td style="padding:8px 12px;"><code>cp .env.example .env</code></td>
  <td style="padding:8px 12px;">Créer le fichier d'environnement</td>
</tr>
<tr>
  <td align="center" style="padding:8px 12px;"><code>3</code></td>
  <td style="padding:8px 12px;"><code>docker compose up -d</code></td>
  <td style="padding:8px 12px;">Démarrer PHP, Nginx, MySQL, Mailpit</td>
</tr>
<tr>
  <td align="center" style="padding:8px 12px;"><code>4</code></td>
  <td style="padding:8px 12px;"><code>docker compose exec php composer install</code></td>
  <td style="padding:8px 12px;">Installer les dépendances PHP</td>
</tr>
<tr>
  <td align="center" style="padding:8px 12px;"><code>5</code></td>
  <td style="padding:8px 12px;"><code>docker compose exec php php bin/console doctrine:migrations:migrate</code></td>
  <td style="padding:8px 12px;">Appliquer les migrations</td>
</tr>
<tr>
  <td align="center" style="padding:8px 12px;"><code>6</code></td>
  <td style="padding:8px 12px;"><code>docker compose exec php php bin/console doctrine:fixtures:load</code></td>
  <td style="padding:8px 12px;">Charger les données de démonstration <em>(optionnel)</em></td>
</tr>
<tr>
  <td align="center" style="padding:8px 12px;"><code>7</code></td>
  <td style="padding:8px 12px;"><code>http://localhost</code></td>
  <td style="padding:8px 12px;">Ouvrir l'application dans le navigateur</td>
</tr>
</table>

### Tests

<table align="center" style="border-spacing:0; font-size:13px; width:100%;">
<tr>
  <th align="left" style="padding:8px 12px;">Commande</th>
  <th align="left" style="padding:8px 12px;">Description</th>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>vendor/bin/phpunit</code></td>
  <td style="padding:8px 12px;">Lancer toute la suite de tests</td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>vendor/bin/phpunit --testsuite Unit</code></td>
  <td style="padding:8px 12px;">Tests unitaires uniquement</td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>vendor/bin/phpunit --testsuite Functional</code></td>
  <td style="padding:8px 12px;">Tests fonctionnels uniquement</td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>vendor/bin/phpunit --coverage-html coverage/</code></td>
  <td style="padding:8px 12px;">Rapport de couverture HTML</td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>vendor/bin/phpstan analyse --configuration=phpstan.dist.neon</code></td>
  <td style="padding:8px 12px;">Analyse statique niveau 8</td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>vendor/bin/php-cs-fixer fix --dry-run</code></td>
  <td style="padding:8px 12px;">Vérifier le style du code</td>
</tr>
<tr>
  <td style="padding:8px 12px;"><code>composer audit</code></td>
  <td style="padding:8px 12px;">Audit des vulnérabilités</td>
</tr>
</table>

---

<div align="center">

<br/>

_"Transformons des idées en applications fonctionnelles, robustes et scalables."_

<br/>

</div>
