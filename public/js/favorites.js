/**
 * JavaScript for handling favorite recipes with localStorage
 */

// LocalStorage key for favorites
const FAVORITES_STORAGE_KEY = 'recipe_app_favorites';

/**
 * Get favorite recipe IDs from localStorage
 * @returns {number[]} Array of recipe IDs
 */
function getFavoriteRecipes() {
    const favorites = localStorage.getItem(FAVORITES_STORAGE_KEY);
    return favorites ? JSON.parse(favorites) : [];
}

/**
 * Save favorite recipe IDs to localStorage
 * @param {number[]} favorites Array of recipe IDs
 */
function saveFavoriteRecipes(favorites) {
    localStorage.setItem(FAVORITES_STORAGE_KEY, JSON.stringify(favorites));
}

/**
 * Check if a recipe is in favorites
 * @param {number} recipeId Recipe ID to check
 * @returns {boolean} True if in favorites
 */
function isRecipeFavorite(recipeId) {
    const favorites = getFavoriteRecipes();
    return favorites.includes(Number(recipeId));
}

/**
 * Add a recipe to favorites
 * @param {number} recipeId Recipe ID to add
 */
function addToFavorites(recipeId) {
    const favorites = getFavoriteRecipes();
    recipeId = Number(recipeId);

    if (!favorites.includes(recipeId)) {
        favorites.push(recipeId);
        saveFavoriteRecipes(favorites);
    }

    // Update UI
    updateFavoriteButtons();
}

/**
 * Remove a recipe from favorites
 * @param {number} recipeId Recipe ID to remove
 */
function removeFromFavorites(recipeId) {
    const favorites = getFavoriteRecipes();
    recipeId = Number(recipeId);

    const index = favorites.indexOf(recipeId);
    if (index !== -1) {
        favorites.splice(index, 1);
        saveFavoriteRecipes(favorites);
    }

    // Update UI
    updateFavoriteButtons();

    // If on favorites page, hide the removed recipe
    const recipesContainer = document.getElementById('favorites-container');
    if (recipesContainer) {
        const recipeElement = document.querySelector(`.recipe-item[data-recipe-id="${recipeId}"]`);
        if (recipeElement) {
            recipeElement.style.display = 'none';
        }

        // Check if any favorites left
        updateFavoritesMessage();
    }
}

/**
 * Toggle favorite status for a recipe
 * @param {number} recipeId Recipe ID to toggle
 */
function toggleFavorite(recipeId) {
    recipeId = Number(recipeId);

    if (isRecipeFavorite(recipeId)) {
        removeFromFavorites(recipeId);
    } else {
        addToFavorites(recipeId);
    }
}

/**
 * Update all favorite buttons to reflect current state
 */
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

/**
 * Initialize favorites page
 */
function initFavoritesPage() {
    const favoritesContainer = document.getElementById('favorites-container');
    if (!favoritesContainer) return; // Not on favorites page

    const favorites = getFavoriteRecipes();

    // Show only favorite recipes
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

    // Update message
    updateFavoritesMessage(visibleCount);
}

/**
 * Update message on favorites page
 */
function updateFavoritesMessage(visibleCount = null) {
    const messageElement = document.getElementById('favorites-message');
    if (!messageElement) return;

    if (visibleCount === null) {
        // Count visible recipes
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update favorite buttons on recipe detail page
    updateFavoriteButtons();

    // Initialize favorites page if we're on that page
    initFavoritesPage();
});