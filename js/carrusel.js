class Carrusel {

    #busqueda;
    #fotos = [];
    #actual = 0;
    #maximo = 4;
    #url = "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
    #contenedor;
    #datos;


    constructor(busqueda) {
        this.#busqueda = busqueda;
        this.#contenedor = document.querySelector("main");
    }

    getFotografias() {
        $.getJSON(this.#url, {
            tags: this.#busqueda,
            tagmode: "any",
            format: "json"
        })
        .done((datos) => {
            this.#datos = datos;
            this.#procesarJSONFotografias();
            this.#mostrarFotografias();
        })
        .fail(() => {
            console.error("Error cargando las imágenes públicas de Flickr");
        });
    }

    #procesarJSONFotografias() {
        this.#fotos = [];
        for (let i = 0; i <= this.#maximo && i < this.#datos.items.length; i++) {
            let url = this.#datos.items[i].media.m; 
            url = url.replace("_m.jpg", "_z.jpg"); // tamaño 640px
            this.#fotos.push(url);
        }
    }

    #mostrarFotografias() {
        const article = document.createElement("article");
        const h2 = document.createElement("h2");
        h2.textContent = "Imágenes del International Chang Circuit";

        const img = document.createElement("img");
        img.src = this.#fotos[0];
        img.alt = "Foto relacionada con el International Chang Circuit"

        article.appendChild(h2);
        article.appendChild(img);
        this.#contenedor.prepend(article);

        // Iniciar cambio automático cada 2 segundos
        setInterval(() => this.#cambiarFotografia(), 2000);
    }

    #cambiarFotografia() {
        this.#actual = (this.#actual + 1) % this.#fotos.length;
        const img = this.#contenedor.querySelector("img");
        img.src = this.#fotos[this.#actual];
    }
}
