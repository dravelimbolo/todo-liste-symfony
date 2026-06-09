# Todo Liste Symfony

[![CI](https://github.com/dravelimbolo/todo-liste-symfony/actions/workflows/ci.yml/badge.svg)](https://github.com/dravelimbolo/todo-liste-symfony/actions/workflows/ci.yml)
[![Deploy](https://github.com/dravelimbolo/todo-liste-symfony/actions/workflows/deploy.yml/badge.svg)](https://github.com/dravelimbolo/todo-liste-symfony/actions/workflows/deploy.yml)
[![codecov](https://codecov.io/gh/dravelimbolo/todo-liste-symfony/branch/main/graph/badge.svg)](https://codecov.io/gh/dravelimbolo/todo-liste-symfony)

## Structure du Projet

todo-liste-symfony/
├── .github/
│   ├── workflows/
│   │   ├── ci.yml              ← lint, tests, security
│   │   └── deploy.yml          ← déploiement
│   └── PULL_REQUEST_TEMPLATE.md
├── config/
│   ├── packages/
│   │   ├── doctrine.yaml
│   │   ├── security.yaml
│   │   ├── twig.yaml
│   │   └── validator.yaml
│   └── services.yaml
├── docker/
│   ├── nginx/default.conf
│   └── php/Dockerfile
├── migrations/
├── public/
│   └── uploads/attachments/    ← fichiers uploadés
├── src/
│   ├── Controller/
│   │   ├── DashboardController.php
│   │   ├── ProfileController.php
│   │   ├── SecurityController.php
│   │   └── TaskController.php
│   ├── DTO/
│   │   ├── ChangePasswordDTO.php
│   │   ├── CreateTaskDTO.php
│   │   ├── RegisterDTO.php
│   │   └── UpdateProfileDTO.php
│   ├── Entity/
│   │   ├── Attachment.php
│   │   ├── Task.php
│   │   └── User.php
│   ├── Enum/
│   │   ├── ProfileType.php
│   │   ├── TaskPriority.php
│   │   └── TaskStatus.php
│   ├── Form/
│   │   ├── ChangePasswordFormType.php
│   │   ├── ProfileFormType.php
│   │   ├── RegistrationFormType.php
│   │   └── TaskFormType.php
│   ├── Repository/
│   │   ├── AttachmentRepository.php
│   │   ├── TaskRepository.php
│   │   └── UserRepository.php
│   ├── Security/
│   │   ├── AppAuthenticator.php
│   │   └── Voter/
│   │       └── TaskVoter.php
│   ├── Service/
│   │   ├── AuthService.php
│   │   ├── DashboardService.php
│   │   ├── TaskService.php
│   │   └── UserService.php
│   └── DataFixtures/
│       └── AppFixtures.php
├── templates/
│   ├── base.html.twig
│   ├── layout/
│   │   └── app.html.twig
│   ├── dashboard/
│   │   └── index.html.twig
│   ├── profile/
│   │   ├── change_password.html.twig
│   │   ├── edit.html.twig
│   │   └── show.html.twig
│   ├── security/
│   │   ├── login.html.twig
│   │   └── register.html.twig
│   └── task/
│       ├── edit.html.twig
│       ├── index.html.twig
│       ├── new.html.twig
│       └── show.html.twig
├── tests/
│   ├── Unit/Service/
│   │   ├── AuthServiceTest.php
│   │   ├── DashboardServiceTest.php
│   │   └── TaskServiceTest.php
│   └── Functional/Controller/
│       ├── SecurityControllerTest.php
│       └── TaskControllerTest.php
├── .env
├── .env.example
├── .php-cs-fixer.php
├── docker-compose.yml
├── phpstan.neon
└── phpunit.xml.dist