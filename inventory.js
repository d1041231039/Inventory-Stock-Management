const searchInput = document.querySelector('input[name="search"]');
const filterSelect = document.querySelector('select[name="filter"]');
const cards = document.querySelectorAll('.stock-card');

function filterCards() {
    const search = searchInput.value.toLowerCase();
    const filter = filterSelect.value.toLowerCase();

    cards.forEach(card => {
        const name = card.querySelector('h3').textContent.toLowerCase();
        const category = card.querySelector('.category').textContent.toLowerCase();

        const matchesSearch = name.includes(search);
        const matchesFilter = !filter || category === filter;

        if (matchesSearch && matchesFilter) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

searchInput.addEventListener('input', filterCards);
filterSelect.addEventListener('change', filterCards);