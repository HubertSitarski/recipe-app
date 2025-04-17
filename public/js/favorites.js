const FAVORITES_STORAGE_KEY = 'recipe_app_favorites';

function getFavoriteRecipes() {
    const favorites = localStorage.getItem(FAVORITES_STORAGE_KEY);
    return favorites ? JSON.parse(favorites) : [];
}

function saveFavoriteRecipes(favorites) {
    localStorage.setItem(FAVORITES_STORAGE_KEY, JSON.stringify(favorites));
}

function isRecipeFavorite(recipeId) {
    const favorites = getFavoriteRecipes();
    return favorites.includes(Number(recipeId));
}

function addToFavorites(recipeId) {
    const favorites = getFavoriteRecipes();
    recipeId = Number(recipeId);

    if (!favorites.includes(recipeId)) {
        favorites.push(recipeId);
        saveFavoriteRecipes(favorites);
    }

    updateFavoriteButtons();
}

function removeFromFavorites(recipeId) {
    const favorites = getFavoriteRecipes();
    recipeId = Number(recipeId);

    const index = favorites.indexOf(recipeId);
    if (index !== -1) {
        favorites.splice(index, 1);
        saveFavoriteRecipes(favorites);
    }

    updateFavoriteButtons();

    const recipesContainer = document.getElementById('favorites-container');
    if (recipesContainer) {
        const recipeElement = document.querySelector(`.recipe-item[data-recipe-id="${recipeId}"]`);
        if (recipeElement) {
            recipeElement.style.display = 'none';
        }

        updateFavoritesMessage();
    }
}

function toggleFavorite(recipeId) {
    recipeId = Number(recipeId);

    if (isRecipeFavorite(recipeId)) {
        removeFromFavorites(recipeId);
    } else {
        addToFavorites(recipeId);
    }
}

function updateFavoriteButtons() {
    const favoriteButtons = document.querySelectorAll('.favorite-button');

    favoriteButtons.forEach(button => {
        const recipeId = Number(button.getAttribute('data-recipe-id'));

        if (isRecipeFavorite(recipeId)) {
            button.textContent = 'Remove from favorites';
            button.classList.add('is-favorite');
        } else {
            button.textContent = 'Add to favorites';
            button.classList.remove('is-favorite');
        }
    });
}

function initFavoritesPage() {
    const favoritesContainer = document.getElementById('favorites-container');
    if (!favoritesContainer) return; // Not on favorites page

    const favorites = getFavoriteRecipes();

    const recipeItems = document.querySelectorAll('.recipe-item');
    let visibleCount = 0;

    recipeItems.forEach(item => {
        const recipeId = Number(item.getAttribute('data-recipe-id'));

        if (favorites.includes(recipeId)) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    updateFavoritesMessage(visibleCount);
}

function updateFavoritesMessage(visibleCount = null) {
    const messageElement = document.getElementById('favorites-message');
    if (!messageElement) return;

    if (visibleCount === null) {
        const visibleRecipes = document.querySelectorAll('.recipe-item[style="display: block;"]');
        visibleCount = visibleRecipes.length;
    }

    if (visibleCount === 0) {
        messageElement.innerHTML = '<p>You don\'t have any favorite recipes yet. Browse the recipes and add some to your favorites!</p>';
        messageElement.style.display = 'block';
    } else {
        messageElement.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateFavoriteButtons();

    initFavoritesPage();
});