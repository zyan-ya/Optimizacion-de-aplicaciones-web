document.addEventListener('DOMContentLoaded', () => {
    const btnUpdate = document.getElementById('btnUpdate');
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const newsContainer = document.getElementById('newsContainer');

    // Función para cargar noticias desde el backend
    const fetchNews = async () => {
        const query = searchInput.value;
        const sort = sortSelect.value;

        try {
            // Llamamos a un archivo PHP que nos devuelva el HTML o JSON
            const response = await fetch(`get_news.php?search=${query}&sort=${sort}`);
            const html = await response.text();
            newsContainer.innerHTML = html;
        } catch (error) {
            console.error("Error cargando noticias:", error);
            newsContainer.innerHTML = "<p class='text-danger'>Error al conectar con el servidor.</p>";
        }
    };

    // Botón Actualizar (Activa el proceso de scraping RSS en el servidor)
    btnUpdate.addEventListener('click', async () => {
        const icon = btnUpdate.querySelector('i');
        icon.classList.add('spin'); // Animación de carga

        try {
            await fetch('update_rss.php'); // Script PHP que usa SimplePie
            await fetchNews(); // Recargar la vista
        } catch (error) {
            alert("No se pudieron actualizar los feeds.");
        } finally {
            icon.classList.remove('spin');
        }
    });

    // Búsqueda en tiempo real (con un pequeño delay)
    let timeout = null;
    searchInput.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(fetchNews, 500);
    });

    // Cambio de ordenamiento
    sortSelect.addEventListener('change', fetchNews);

    // Carga inicial
    fetchNews();

    document.getElementById('btnAddFeed').addEventListener('click', async () => {
        const url = document.getElementById('feedUrl').value;
        if (!url) return alert("Escribe una URL");

        const formData = new FormData();
        formData.append('url', url);

        await fetch('add_feed.php', { method: 'POST', body: formData });
        document.getElementById('feedUrl').value = '';
        alert("Feed añadido. ¡Haz clic en Actualizar para traer las noticias!");
    });
});