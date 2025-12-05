class Circuito {
    constructor() {
        this.contenidoHTML = "";
        this.comprobarApiFile();
    }

    comprobarApiFile() {
        const p = document.createElement("p");

        if (window.File && window.FileReader && window.FileList && window.Blob) {
            p.textContent = "Este navegador soporta la API File";
        } else {
            p.textContent = "Este navegador NO soporta la API File y este programa no puede funcionar correctamente";
        }

        document.body.appendChild(p);
    }

    leerArchivoHTML(files) {
        for (let i = 0; i < files.length; i++) {
            const archivo = files[i];
            const lector = new FileReader();

            lector.onload = (evento) => {
                this.contenidoHTML = evento.target.result;
                console.log("Contenido del archivo cargado:");
                console.log(this.contenidoHTML);
                this.procesarHTMLCircuito();
            };

            lector.onerror = () => console.error("Error leyendo el archivo");
            lector.readAsText(archivo);
        }
    }


    procesarHTMLCircuito() {
        // 1. Comprobar que hay contenido HTML cargado
        if (!this.contenidoHTML || this.contenidoHTML.trim() === "") {
            console.error("No hay contenido HTML cargado en this.contenidoHTML");
            return;
        }

        // 2. Parsear el HTML del InfoCircuito.html
        const parser = new DOMParser();
        const docInfo = parser.parseFromString(this.contenidoHTML, "text/html");

        // 3. Obtener el <main> del InfoCircuito.html
        const mainInfo = docInfo.querySelector("main");
        if (!mainInfo) {
            console.error("El archivo InfoCircuito.html no contiene un <main>");
            return;
        }

        // 4. Seleccionar el <main> de circuito.html (el actual documento)
        const mainCircuito = document.querySelector("main");

        // Crear una sección para agrupar la info importada
        const seccion = document.createElement("section");
        seccion.classList.add("info-circuito-importada");

        // Opcional: título dentro de la sección
        const h2 = document.createElement("h2");
        h2.textContent = "Información del circuito";
        seccion.appendChild(h2);

        // 5. Copiar todos los hijos del <main> del InfoCircuito.html
        //    excepto su propio <h2> "Información del circuito" si no lo quieres duplicar
        const nodos = mainInfo.children;
        for (let i = 0; i < nodos.length; i++) {
            const nodo = nodos[i];

            // si no quieres repetir el <h2> original, puedes saltarlo:
            if (nodo.tagName.toLowerCase() === "h2") {
                continue;
            }

            const clon = nodo.cloneNode(true); // clonar con todo su contenido
            seccion.appendChild(clon);
        }

        // 6. Insertar la sección al final del <main> de circuito.html
        mainCircuito.appendChild(seccion);
    }

}

class CargadorSVG {
    constructor() {
        this.contenidoSVG = ""; // Aquí guardaremos el contenido del archivo
    }

    leerArchivoSVG(files) {
        if (!files || files.length === 0) {
            alert("No se seleccionó ningún archivo SVG");
            return;
        }

        for (let i = 0; i < files.length; i++) {
            const archivo = files[i];
            const lector = new FileReader();

            lector.onload = (evento) => {
                this.contenidoSVG = evento.target.result;
                console.log("Contenido del archivo SVG cargado:");
                console.log(this.contenidoSVG);

                this.insertarSVG();
            };

            lector.onerror = () => console.error("Error leyendo el archivo SVG");

            lector.readAsText(archivo);
        }
    }

    insertarSVG() {
        if (!this.contenidoSVG) {
            console.error("No hay contenido SVG para insertar");
            return;
        }

        const main = document.querySelector("main");
        const contenedor = document.createElement("div");
        contenedor.classList.add("grafico-altimetria");

        // Insertamos el contenido SVG
        contenedor.innerHTML = this.contenidoSVG;

        main.appendChild(contenedor);
    }
}

class CargadorKML
{
    constructor() {
        this.coordenadasOrigen = null;  // Para almacenar el punto inicial del circuito
        this.tramos = [];                // Array con los tramos del circuito
        this.contenidoKML = "";          // Contenido completo del KML
    }

    // Método para leer el archivo KML usando API File
    leerArchivoKML(files) {
        for (let i = 0; i < files.length; i++) {
            const archivo = files[i];

            if (!archivo) {
                console.error("No se seleccionó ningún archivo KML");
                continue;
            }

            const lector = new FileReader();

            lector.onload = (evento) => {
                this.contenidoKML = evento.target.result;
                console.log("Contenido KML cargado:");
                console.log(this.contenidoKML);

                // Procesar las coordenadas
                this.insertarCapaKML();
            };

            lector.onerror = () => console.error("Error leyendo el archivo KML");

            lector.readAsText(archivo);
        }
    }

    insertarCapaKML() {
        // 1. Comprobar que hay contenido KML
        if (!this.contenidoKML || this.contenidoKML.trim() === "") {
            console.error("No hay contenido KML cargado en this.contenidoKML");
            return;
        }

        // 2. Parsear el KML como XML
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(this.contenidoKML, "application/xml");

        // 3. Extraer todos los <Placemark>
        const placemarks = xmlDoc.querySelectorAll("Placemark");

        let puntoOrigen = null;
        const tramos = [];

        placemarks.forEach(pm => {
            const nombre = pm.querySelector("name")?.textContent.trim();
            const coordsNode = pm.querySelector("Point coordinates");
            const lineStringNode = pm.querySelector("LineString coordinates");

            // Si es punto
            if (coordsNode) {
                const [lon, lat, alt] = coordsNode.textContent.trim().split(",");
                const punto = { lat: parseFloat(lat), lon: parseFloat(lon), alt: alt ? parseFloat(alt) : 0 };

                if (nombre === "Origen") {
                    puntoOrigen = punto; // Guardamos el punto origen
                } else {
                    tramos.push(punto); // Cada punto de tramo
                }
            }

            // Si es LineString
            if (lineStringNode) {
                const lineCoords = lineStringNode.textContent.trim().split(/\s+/).map(str => {
                    const [lon, lat, alt] = str.split(",");
                    return { lat: parseFloat(lat), lon: parseFloat(lon), alt: alt ? parseFloat(alt) : 0 };
                });
                tramos.push(...lineCoords); // agregamos todos los puntos del circuito
            }
            });

            console.log("Punto origen:", puntoOrigen);
            console.log("Tramos del circuito:", tramos);

            this.insertarCapaKMLEnMapbox(puntoOrigen, tramos);
    }

    insertarCapaKMLEnMapbox(puntoOrigen, tramos) {
        // 1. Crear un div para el mapa si no existe
        let body = document.querySelector("body");
        let mapaDiv = document.createElement("div");
        body.appendChild(mapaDiv)
        // 2. Inicializar Mapbox
        mapboxgl.accessToken = "pk.eyJ1IjoidW8zMDA2NTIiLCJhIjoiY21pOHdwdm1iMDg2MzJwc2FnNjBmZThhZyJ9.55uQ4usJDVJydRrOYvszgg";
        const map = new mapboxgl.Map({
            container: mapaDiv,
            center: [puntoOrigen.lon, puntoOrigen.lat],
            zoom: 15
        });

        // 3. Añadir marcador del punto origen
        new mapboxgl.Marker()
            .setLngLat([puntoOrigen.lon, puntoOrigen.lat])
            .setPopup(new mapboxgl.Popup().setText("Origen del circuito"))
            .addTo(map);

        // 4. Crear línea con los tramos del circuito
        const coordinates = tramos.map(p => [p.lon, p.lat]);

        map.on("load", () => {
            map.addSource("circuito", {
                type: "geojson",
                data: {
                    type: "Feature",
                    geometry: {
                        type: "LineString",
                        coordinates: coordinates
                    }
                }
            });

            map.addLayer({
                id: "circuito-linea",
                type: "line",
                source: "circuito",
                layout: {
                    "line-join": "round",
                    "line-cap": "round"
                },
                paint: {
                    "line-color": "#ff0000",
                    "line-width": 4
                }
            });
        });
    }
}