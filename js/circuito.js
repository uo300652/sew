class Circuito {
    #contenidoHTML = "";

    constructor() {
        this.#comprobarApiFile();
    }

    #asignarEventoInputHTML()
    {
        const inputHTML = document.getElementById("archivoCircuito");

        if (inputHTML) {
            inputHTML.addEventListener("change", (e) => {
                this.leerArchivoHTML(e.target.files);
            });
        }
    }

    #comprobarApiFile() {
        const p = document.createElement("p");

        if (window.File && window.FileReader && window.FileList && window.Blob) {
            this.#asignarEventoInputHTML();
        } else {
            p.textContent = "Este navegador NO soporta la API File y este programa no puede abrir archivos HTML";
            document.body.appendChild(p);
        }
    }

    leerArchivoHTML(files) {
        for (let i = 0; i < files.length; i++) {
            const archivo = files[i];
            const lector = new FileReader();

            lector.onload = (evento) => {
                this.#contenidoHTML = evento.target.result;
                this.#insertarHTMLCircuito();
            };

            lector.onerror = () => console.error("Error leyendo el archivo");
            lector.readAsText(archivo);
        }
    }

    #procesarHTMLCircuito() {
        if (!this.#contenidoHTML || this.#contenidoHTML.trim() === "") {
            console.error("No hay contenido HTML cargado en this.contenidoHTML");
            return;
        }

        const parser = new DOMParser();
        const docInfo = parser.parseFromString(this.#contenidoHTML, "text/html");
        const p = [...docInfo.body.querySelectorAll("p")];

        if (!p.length) {
            console.error("No se encontraron <p>");
            return;
        }

        const seccion = document.createElement("section");
        const h4 = document.createElement("h4");
        h4.textContent = "Información del circuito";
        seccion.appendChild(h4);

        /* Datos básicos */
        for (let i = 0; i <= 10; i++) {
            const nuevoP = document.createElement("p");
            let texto = p[i].textContent;

            // Tiempo del vencedor (p[10])
            if (i === 10) {
                const dosPuntos = texto.indexOf(":");
                const etiqueta = texto.slice(0, dosPuntos + 1);
                const valorISO = texto.slice(dosPuntos + 1).trim();

                const mIndex = valorISO.indexOf("M");
                const sIndex = valorISO.indexOf("S");

                const minutos = valorISO.slice(2, mIndex);
                const segundos = valorISO.slice(mIndex + 1, sIndex);

                texto = `${etiqueta} ${minutos}:${segundos}`;
            }

            nuevoP.textContent = texto;
            seccion.appendChild(nuevoP);
        }


        /* Procesar Referecias.
        En el for se hace +2 ya que en un parrafo esta la descripcion y en otro la url */
        const h5Refs = document.createElement("h5");
        h5Refs.textContent = "Referencias";
        seccion.appendChild(h5Refs);

        const ul = document.createElement("ul");

        for (let i = 11; i <= 16; i += 2) {
            const li = document.createElement("li");

            const descripcion = p[i].textContent;
            const enlace = document.createElement("a");
            enlace.href = p[i + 1].textContent;
            enlace.textContent = "Leer más";
            enlace.target = "_blank";

            li.textContent = descripcion + " ";
            li.appendChild(enlace);
            ul.appendChild(li);
        }

        seccion.appendChild(ul);

        /* Procesar Imagenes. 
        En el for se hace +2 ya que en un parrafo esta la descripcion y en otro la url*/
        const h5Imgs = document.createElement("h5");
        h5Imgs.textContent = "Imagenes sobre el International Chang Circuit"
        seccion.appendChild(h5Imgs);
        for (let i = 17; i <= 22; i += 2) {
            const descripcion = p[i].textContent;
            const rutaBase = p[i + 1].textContent;

            const punto = rutaBase.lastIndexOf(".");
            const raiz = rutaBase.slice(0, punto);
            const ext = rutaBase.slice(punto);

            const picture = document.createElement("picture");

            const sourceMovil = document.createElement("source");
            sourceMovil.media = "(max-width: 465px)";
            sourceMovil.srcset = raiz + "Movil" + ext;
            picture.appendChild(sourceMovil);

            const sourceTablet = document.createElement("source");
            sourceTablet.media = "(max-width: 900px)";
            sourceTablet.srcset = raiz + "Tablet" + ext;
            picture.appendChild(sourceTablet);

            const sourceMonitor = document.createElement("source");
            sourceMonitor.media = "(min-width: 901px)";
            sourceMonitor.srcset = raiz + "Monitor" + ext;
            picture.appendChild(sourceMonitor);

            const img = document.createElement("img");
            img.src = rutaBase;
            img.alt = descripcion;

            picture.appendChild(img);
            seccion.appendChild(picture);
        }


        /* Procesar video */
        const h5Videos = document.createElement("h5");
        h5Videos.textContent = "Videos sobre el International Chang Circuit"
        const video = document.createElement("video");
        video.src = p[24].textContent;
        video.controls = true;
        video.preload = "auto";
        const textoFallback = document.createTextNode(p[23].textContent);
        video.appendChild(textoFallback);
        seccion.appendChild(h5Videos);
        seccion.appendChild(video);

        /* Procesar clasificaciones */
        const h5Clas = document.createElement("h5");
        h5Clas.textContent = "Clasificación";
        seccion.appendChild(h5Clas);

        const ol = document.createElement("ol");

        for (let i = 25; i <= 27; i++) {
            const li = document.createElement("li");
            li.textContent = p[i].textContent;
            ol.appendChild(li);
        }

        seccion.appendChild(ol);

        return seccion;
    }

    #insertarHTMLCircuito() {
        const seccion = this.#procesarHTMLCircuito();
        if (!seccion) return;

        const main = document.querySelector("main");
        if (!main) {
            console.error("No se encontró <main>");
            return;
        }

        main.appendChild(seccion);
    }


}



class CargadorSVG {

    #contenidoSVG = "";

    constructor() {
        this.#comprobarApiFile();
    }

    #comprobarApiFile() {
        const p = document.createElement("p");

        if (window.File && window.FileReader && window.FileList && window.Blob) {
            this.#asignarEventoInputSVG();
        } else {
            p.textContent = "Este navegador NO soporta la API File y este programa no puede abrir archivos SVG";
            document.body.appendChild(p);
        }
    }

    #asignarEventoInputSVG()
    {
        const inputSVG = document.getElementById("SVGCircuito");

        if (inputSVG) {
            inputSVG.addEventListener("change", (e) => {
                this.leerArchivoSVG(e.target.files);
            });
        }
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
                this.#contenidoSVG = evento.target.result;
                this.#insertarSVG();
            };

            lector.onerror = () => console.error("Error leyendo el archivo SVG");

            lector.readAsText(archivo);
        }
    }

    #insertarSVG() {
        if (!this.#contenidoSVG) {
            console.error("No hay contenido SVG para insertar");
            return;
        }

        const main = document.querySelector("main");
        const h4 = document.createElement("h4");
        h4.textContent = "Gráfico con la altímetria del International Chang Circuit"
        const section = document.createElement("section");

        section.appendChild(h4);

        // Insertamos el contenido SVG
        section.innerHTML = this.#contenidoSVG;

        section.prepend(h4);
        main.appendChild(section);
    }
}

class CargadorKML
{
    #contenidoKML = "";

    constructor() {
        this.#comprobarApiFile();
    }

    #comprobarApiFile() {
        const p = document.createElement("p");

        if (window.File && window.FileReader && window.FileList && window.Blob) {
            this.#asignarEventoInputKML();
        } else {
            p.textContent = "Este navegador NO soporta la API File y este programa no puede abrir archivos KML";
            document.body.appendChild(p);
        }
    }

    #asignarEventoInputKML()
    {
        const inputKML = document.getElementById("KMLCircuito");

        if (inputKML) {
            inputKML.addEventListener("change", (e) => {
                this.leerArchivoKML(e.target.files);
            });
        }
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
                this.#contenidoKML = evento.target.result;

                // Procesar las coordenadas
                this.#insertarCapaKML();
            };

            lector.onerror = () => console.error("Error leyendo el archivo KML");

            lector.readAsText(archivo);
        }
    }

    #insertarCapaKML() {
        // 1. Comprobar que hay contenido KML
        if (!this.#contenidoKML || this.#contenidoKML.trim() === "") {
            console.error("No hay contenido KML cargado en this.contenidoKML");
            return;
        }

        // 2. Parsear el KML como XML
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(this.#contenidoKML, "application/xml");

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

            this.#insertarCapaKMLEnMapbox(puntoOrigen, tramos);
    }

    #insertarCapaKMLEnMapbox(puntoOrigen, tramos) {
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