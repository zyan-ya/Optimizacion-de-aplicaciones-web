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
            // AGREGAMOS UN TIMESTAMP (&t=...) PARA EVITAR EL CACHÉ DEL NAVEGADOR
            const response = await fetch(`get_news.php?search=${query}&sort=${sort}&t=${Date.now()}`);
            const html = await response.text();
            newsContainer.innerHTML = html;
        } catch (error) {
            console.error("Error cargando noticias:", error);
            newsContainer.innerHTML = "<div class='col-12 text-center'><p class='text-danger'>Error al conectar con el servidor.</p></div>";
        }
    };

    // Botón Actualizar
    btnUpdate.addEventListener('click', async () => {
        const icon = btnUpdate.querySelector('i');
        icon.classList.add('fa-spin'); // Usamos la clase estándar de FontAwesome

        try {
            // Llamamos al proceso de actualización
            const resp = await fetch(`update_rss.php?t=${Date.now()}`);
            const texto = await resp.text();
            console.log("Respuesta servidor:", texto);
            
            // Esperamos un segundo para que la DB se asiente y recargamos
            setTimeout(fetchNews, 500); 
        } catch (error) {
            alert("No se pudieron actualizar los feeds.");
        } finally {
            icon.classList.remove('fa-spin');
        }
    });

    // Búsqueda en tiempo real
    let timeout = null;
    searchInput.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(fetchNews, 400);
    });

    // Cambio de ordenamiento
    sortSelect.addEventListener('change', fetchNews);

    // Carga inicial
    fetchNews();

    // Añadir nuevo Feed
    document.getElementById('btnAddFeed').addEventListener('click', async () => {
        const input = document.getElementById('feedUrl');
        const url = input.value.trim();
        
        if (!url) return alert("Escribe una URL");

        const formData = new FormData();
        formData.append('url', url);

        try {
            await fetch('add_feed.php', { method: 'POST', body: formData });
            input.value = '';
            alert("Feed añadido con éxito.");
            // Actualizamos automáticamente después de añadir
            btnUpdate.click(); 
        } catch (e) {
            alert("Error al añadir el feed.");
        }
    });
});