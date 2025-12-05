class Carrusel {
    constructor(busqueda) {
        this.busqueda = busqueda;
        this.fotos = [];
        this.actual = 0;
        this.maximo = 4;
        this.url = "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        this.contenedor = document.querySelector("body"); // primera section dentro de main
    }

    getFotografias() {
        return $.getJSON(this.url, {
            tags: this.busqueda,
            tagmode: "any",
            format: "json"
        })
        .done((datos) => {
            this.datos = datos;
            this.procesarJSONFotografias();
            this.mostrarFotografias();
        })
        .fail(() => {
            console.error("Error cargando las imágenes públicas de Flickr");
        });
    }

    procesarJSONFotografias() {
        this.fotos = [];
        for (let i = 0; i <= this.maximo && i < this.datos.items.length; i++) {
            let url = this.datos.items[i].media.m; 
            url = url.replace("_m.jpg", "_z.jpg"); // tamaño 640px
            this.fotos.push(url);
        }
    }

    mostrarFotografias() {
        const article = document.createElement("article");
        const h2 = document.createElement("h2");
        h2.textContent = "Imágenes del circuito de MotoGP";

        const img = document.createElement("img");
        img.src = this.fotos[0];

        article.appendChild(h2);
        article.appendChild(img);
        this.contenedor.appendChild(article);

        // Iniciar cambio automático cada 2 segundos
        setInterval(() => this.cambiarFotografia(), 2000);
    }

    cambiarFotografia() {
        this.actual = (this.actual + 1) % this.fotos.length;
        const img = this.contenedor.querySelector("img");
        img.src = this.fotos[this.actual];
    }
}
