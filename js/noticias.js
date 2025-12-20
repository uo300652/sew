class Noticias
{
    #busqueda;
    #urlBase = "https://api.thenewsapi.com/v1/news/all";
    #apiKey = "6BBebafej3BKEMfSsoS3noZbYmrzzYhsM9oQZTqG"

    constructor(busqueda)
    {
        this.#busqueda = busqueda;
    }

    async buscar()
    {
        const url = `${this.#urlBase}?search=${encodeURIComponent(this.#busqueda)}&api_token=${this.#apiKey}&language=es&limit=10`;

        try {
            const respuesta = await fetch(url);
            if (!respuesta.ok) throw new Error('Ciudad no encontrada');
            const datos = await respuesta.json();
            this.#procesarInformacion(datos);
        } catch (error) {
            console.error("Error al obtener las noticias: " + error.message);
        }
    }

    #procesarInformacion(datos) {
        if (!datos || !datos.data) return;

        const contenedor = document.querySelector("main");

        // Crear un <section> para contener todas las noticias
        const $section = document.createElement("section");

        const h2 = document.createElement("h2");
        h2.textContent = "Noticias relacionadas con el International Chang Circuit";

        $section.appendChild(h2); 

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