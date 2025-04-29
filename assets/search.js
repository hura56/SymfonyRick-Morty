let debounceTimer;
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearch');

function debounceSearch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        document.querySelector('form').submit();
    }, 500);
}

searchInput.addEventListener('input', function() {
    if (searchInput.value) {
        clearSearchBtn.style.display = 'block';
    } else {
        clearSearchBtn.style.display = 'none';
    }
    debounceSearch();
});

function clearSearchInput() {
    searchInput.value = '';
    clearSearchBtn.style.display = 'none'; 
    document.querySelector('form').submit(); 
}

if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', clearSearchInput);
}

window.addEventListener('DOMContentLoaded', () => {
    if (searchInput.value) {
        clearSearchBtn.style.display = 'block';
    }
});
