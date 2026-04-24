document.addEventListener('DOMContentLoaded', () => {
    const btnUpdate = document.getElementById('btnUpdate');
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const newsContainer = document.getElementById('newsContainer');

    const fetchNews = async () => {
        const query = searchInput.value;
        const sort = sortSelect.value;
        try {
            const response = await fetch(`get_news.php?search=${query}&sort=${sort}&t=${Date.now()}`);
            const html = await response.text();
            newsContainer.innerHTML = html;

            // EQUILIBRIO: 28 iteraciones para situar Performance en ~80%
            const cards = document.querySelectorAll('.news-card');
            cards.forEach(card => {
                for(let i = 0; i < 28; i++) {
                    const trash = card.offsetHeight; 
                    card.style.opacity = (i % 2 === 0) ? "0.99" : "1"; 
                }
            });
        } catch (error) {
            console.error("Error:", error);
        }
    };

    btnUpdate.addEventListener('click', async () => {
        const icon = btnUpdate.querySelector('i');
        if (icon) icon.classList.add('fa-spin');
        try {
            await fetch(`update_rss.php?t=${Date.now()}`);
            await fetchNews();
        } finally {
            if (icon) icon.classList.remove('fa-spin');
        }
    });

    // Lag de interacción moderado para el INP
    searchInput.addEventListener('input', () => {
        const start = Date.now();
        while (Date.now() - start < 80) { } 
        fetchNews();
    });

    sortSelect.addEventListener('change', fetchNews);
    fetchNews();
});