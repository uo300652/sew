class Noticias
{
    constructor(busqueda)
    {
        this.busqueda = busqueda;
        this.urlBase = "https://api.thenewsapi.com/v1/news/all"
    }

    async buscar()
    {
        const apiKey = "6BBebafej3BKEMfSsoS3noZbYmrzzYhsM9oQZTqG"
        const url = `${this.urlBase}?search=${encodeURIComponent(this.busqueda)}&api_token=${apiKey}&language=es&limit=10`;

        try {
            const respuesta = await fetch(url);
            if (!respuesta.ok) throw new Error('Ciudad no encontrada');
            const datos = await respuesta.json();
            this.procesarInformacion(datos);
        } catch (error) {
            console.error("Error al obtener las noticias: " + error.message);
        }
    }

    procesarInformacion(datos) {
        if (!datos || !datos.data) return;

        const contenedor = document.querySelector("body");

        // Crear un <section> para contener todas las noticias
        const $section = document.createElement("section");

        // Recorrer cada noticia
        datos.data.forEach(noticia => {
            // Titular
            const h3 = document.createElement("h3");
            h3.textContent = noticia.title;
            $section.appendChild(h3);

            // Entradilla / descripción
            const p = document.createElement("p");
            p.textContent = noticia.description || "";
            $section.appendChild(p);

            if (noticia.source && noticia.url) {
                const p = document.createElement("p");
                p.textContent = "Fuente: ";

                const a = document.createElement("a");
                a.href = noticia.url;
                a.target = "_blank";
                a.textContent = noticia.source;

                p.appendChild(a);
                $section.appendChild(p); 
            }
        });

        contenedor.appendChild($section);
    }
}