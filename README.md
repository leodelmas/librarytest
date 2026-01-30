# Bibliothèque

Application web de gestion de bibliothèque personnelle développée avec Symfony 8.0 et PHP 8.4+. Recherchez des livres via l'API Open Library, ajoutez-les à votre collection, suivez votre progression de lecture et prenez des notes.

## Fonctionnalités

- **Inscription / Connexion** sécurisée avec hachage des mots de passe
- **Recherche de livres** via l'API Open Library (titre, auteur, couverture)
- **Collection personnelle** avec ajout/suppression de livres
- **Statut de lecture** : À lire, En lecture, Lu — changement en un clic, tri automatique
- **Notes** par livre avec éditeur riche (Quill)

## Stack technique

| Composant       | Technologie                              |
|-----------------|------------------------------------------|
| Framework       | Symfony 8.0                              |
| PHP             | 8.4+                                     |
| Base de données | MySQL 8.0 (Doctrine ORM 3.6)             |
| Frontend        | Stimulus + Turbo, Tailwind CSS v4        |
| Icônes          | Symfony UX Icons (Lucide)                |
| Email           | Symfony Mailer (Mailpit en dev)          |
| Assets          | Symfony Asset Mapper                     |

## Prérequis

- [Docker](https://www.docker.com/) et Docker Compose

## Installation

```bash
git clone <repository-url>
cd librarytest
docker compose up -d
```

L'application est accessible sur :

| Service      | URL                    |
|--------------|------------------------|
| Application  | http://localhost:8080  |
| phpMyAdmin   | http://localhost:8081  |
| Mailpit      | http://localhost:8025  |

## Commandes utiles

```bash
# Installer les dépendances PHP
docker compose exec app composer install

# Exécuter les migrations
docker compose exec app php bin/console doctrine:migrations:migrate

# Lancer les tests
docker compose exec app ./bin/phpunit

# Lancer un test spécifique
docker compose exec app ./bin/phpunit --filter testMethodName

# Console Symfony
docker compose exec app php bin/console <commande>
```

## Architecture

```
src/
├── Controller/          # BookController, BookSearchController, SecurityController, RegistrationController
├── Entity/              # Book, User, UserBook, Note
├── Enum/                # ReadingStatus (À lire, En lecture, Lu)
├── Repository/          # Repositories Doctrine
templates/               # Templates Twig
assets/
├── controllers/         # Contrôleurs Stimulus
├── styles/              # CSS (Tailwind v4)
migrations/              # Migrations Doctrine
config/                  # Configuration Symfony
```
