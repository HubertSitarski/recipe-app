# Recipe App - TheMealDB Synchronizer

This application is a solution for synchronizing and displaying recipes from TheMealDB API. It provides a user-friendly interface to browse, search, and view detailed information about various recipes.

## System Requirements

- PHP 8.1 or higher
- Symfony 6.4
- MariaDB 11.1.2
- Docker & Docker Compose
- Composer

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/HubertSitarski/recipe-app.git
cd recipe-app
```

### 2. Configure Docker environment

The project comes with a Docker setup that includes:
- PHP service
- Nginx web server
- MariaDB database
- phpMyAdmin
- Test database

Run the Docker environment:

```bash
docker-compose up -d
```

### 3. Install dependencies

Enter the PHP container and install dependencies:

```bash
docker-compose exec php composer install
```

### 4. Set up the database

Copy the `.env` file to `.env.local` to create your local configuration:

```bash
cp .env .env.local
```

Make sure your database configuration in `.env.local` contains the following settings:

```
DATABASE_URL="mysql://app:asdf123@database:3306/app?serverVersion=11.1.2-MariaDB&charset=utf8mb4"
```

Run migrations to create the database schema:

```bash
docker-compose exec php bin/console doctrine:migrations:migrate
```

Run migrations to create the database schema for testing database:

```bash
docker-compose exec php bin/console doctrine:migrations:migrate --env=test
```

### 5. Run development server

The application should be accessible at:
- Web application: http://localhost:8080
- phpMyAdmin: http://localhost:8081

## Testing

Run PhpUnit tests:

```bash
docker-compose exec php bin/phpunit
```

## Recipe Synchronization

The application synchronizes recipes from TheMealDB API in time intervals. To start the synchronization process, run:

The first synchronization will happen after default interval - 1 hour

```bash
docker-compose exec php bin/console messenger:consume -vv scheduler_default async
```

This will process the async messages and scheduler tasks that fetch recipes from TheMealDB.

You can also trigger a manual synchronization with:

```bash
docker-compose exec php bin/console app:recipes:synchronize
```

Then:

```bash
docker-compose exec php bin/console messenger:consume -vv scheduler_default async
```

## Project Structure

- `src/Controller/` - Application controllers (Recipe, RecipeDetails, Favorite)
- `src/Entity/` - Database entities (Recipe, RecipeIngredient, Comment)
- `src/Repository/` - Data access layer
- `src/Service/` - Business logic services
- `src/Form/` - Form definitions
- `src/Command/` - CLI commands
- `src/Message/` & `src/MessageHandler/` - Async message processing
- `src/Provider/` - External API providers
- `templates/` - Twig templates
- `config/` - Application configuration
- `migrations/` - Database migrations
- `docker/` - Docker configuration files

## Features

### Recipe Synchronization
- Periodic synchronization with TheMealDB API
- Async processing using Symfony Messenger
- Scheduled tasks using Symfony Scheduler

### Recipe Browsing
- Paginated list of all recipes
- Search functionality by recipe name
- Category filtering

### Recipe Details
- Detailed view of each recipe
- Ingredients and measurements
- Preparation instructions
- Recipe image

### User Interaction
- Comment system for recipes
- Favorite recipes functionality
- User-friendly interface

## Usage Examples

### Browsing Recipes
Navigate to the homepage to see all available recipes. Use the search bar to find specific recipes.

### Viewing Recipe Details
Click on a recipe card to view detailed information about the recipe, including ingredients, instructions, and comments.

### Adding Comments
On the recipe details page, scroll down to the comments section to add your own comments about the recipe.

### Favorite Recipes
Click the "Add to Favorites" button on a recipe to save it to your favorites list.

## Development

The application is built with Symfony 6.4 and uses:
- Doctrine ORM for database operations
- Twig for templating
- Symfony Forms for form handling
- Symfony Messenger for asynchronous tasks
- Symfony Scheduler for recurring tasks