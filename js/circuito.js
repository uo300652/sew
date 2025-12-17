class Circuito {
    constructor() {
        this.contenidoHTML = "";
        this.comprobarApiFile();
    }

    asignarEventoInputHTML()
    {
        const inputHTML = document.getElementById("archivoCircuito");

        if (inputHTML) {
            inputHTML.addEventListener("change", (e) => {
                this.leerArchivoHTML(e.target.files);
            });
        }
    }

    comprobarApiFile() {
        const p = document.createElement("p");

        if (window.File && window.FileReader && window.FileList && window.Blob) {
            this.asignarEventoInputHTML();
        } else {
            p.textContent = "Este navegador NO soporta la API File y este programa no puede funcionar correctamente";
            document.body.appendChild(p);
        }
    }

    leerArchivoHTML(files) {
        for (let i = 0; i < files.length; i++) {
            const archivo = files[i];
            const lector = new FileReader();

            lector.onload = (evento) => {
                this.contenidoHTML = evento.target.result;
                console.log("Contenido del archivo cargado:");
                console.log(this.contenidoHTML);
                this.insertarHTMLCircuito();
            };

            lector.onerror = () => console.error("Error leyendo el archivo");
            lector.readAsText(archivo);
        }
    }

    procesarHTMLCircuito() {
        if (!this.contenidoHTML || this.contenidoHTML.trim() === "") {
            console.error("No hay contenido HTML cargado en this.contenidoHTML");
            return;
        }

        const parser = new DOMParser();
        const docInfo = parser.parseFromString(this.contenidoHTML, "text/html");
        const pElements = docInfo.body.querySelectorAll("p");

        if (!pElements.length) {
            console.error("No se encontraron <p> en el InfoCircuito.html");
            return;
        }

        const seccion = document.createElement("section");
        const h4 = document.createElement("h4");
        h4.textContent = "Información del circuito";
        seccion.appendChild(h4);

        const formatearCampo = (campo) => {
            return campo.replace(/([A-Z])/g, " $1")
                        .replace(/^./, c => c.toUpperCase());
        };

        pElements.forEach(p => {
            const texto = p.textContent.trim();

            if (texto.startsWith("Foto:")) {
                const partes = texto.slice(5).split(" - ");
                if (partes.length === 2) {
                    const descripcion = partes[0].trim();
                    const url = partes[1].trim();
                    const picture = document.createElement("picture");
                    const raiz = url.replace(/(\.[a-z]+)$/, '');
                    const ext = url.match(/(\.[a-z]+)$/)[1];

                    const versiones = {
                        movil: raiz + "Movil" + ext,
                        tablet: raiz + "Tablet" + ext,
                        monitor: raiz + "Monitor" + ext,
                        base: url
                    };

                    const sourceMovil = document.createElement("source");
                    sourceMovil.media = "(max-width: 465px)";
                    sourceMovil.srcset = versiones.movil;
                    picture.appendChild(sourceMovil);

                    const sourceTablet = document.createElement("source");
                    sourceTablet.media = "(max-width: 900px)";
                    sourceTablet.srcset = versiones.tablet;
                    picture.appendChild(sourceTablet);

                    const sourceMonitor = document.createElement("source");
                    sourceMonitor.media = "(min-width: 901px)";
                    sourceMonitor.srcset = versiones.monitor;
                    picture.appendChild(sourceMonitor);

                    const img = document.createElement("img");
                    img.src = versiones.base;
                    img.alt = descripcion;
                    picture.appendChild(img);

                    seccion.appendChild(picture);
                }
            } else if (texto.startsWith("Video:")) {
                const partes = texto.slice(6).split(" - ");
                if (partes.length === 2) {
                    const descripcion = partes[0].trim();
                    const url = partes[1].trim();
                    const video = document.createElement("video");
                    video.src = url;
                    video.controls = true;
                    video.preload = "auto";
                    video.textContent = descripcion;
                    seccion.appendChild(video);
                }
            } else if (texto.startsWith("Clasificado:")) {
                let ol = seccion.querySelector("ol");
                if (!ol) {
                    let h5 = document.createElement("h5");
                    h5.textContent = "Clasificación"
                    ol = document.createElement("ol");
                    seccion.appendChild(h5);
                    seccion.appendChild(ol);
                }
                const li = document.createElement("li");
                li.textContent = texto.replace("Clasificado:", "").trim();
                ol.appendChild(li);
            } else if (texto.startsWith("Referencia")) {
                // Extraemos fuente, descripción y enlace
                const partes = texto.match(/^Referencia\s*\((.+?)\):\s*(.+?)\s*-\s*(https?:\/\/\S+)$/);

                let ul = seccion.querySelector("ul");
                if (!ul) {
                    ul = document.createElement("ul");
                    seccion.appendChild(ul);
                }

                if (partes) {
                    const li = document.createElement("li");
                    const fuente = partes[1].trim();
                    const descripcion = partes[2].trim();
                    const enlace = partes[3].trim();

                    // Texto descriptivo
                    li.textContent = `${fuente}: ${descripcion} `;

                    // Enlace
                    const a = document.createElement("a");
                    a.href = enlace;
                    a.textContent = "Leer más";
                    a.target = "_blank";

                    li.appendChild(a);
                    ul.appendChild(li);
                }
            } else {
                const pNuevo = document.createElement("p");
                let [campo, valor] = texto.split(":").map(s => s.trim());

                if (campo && valor) {
                    campo = formatearCampo(campo);
                    if (campo.toLowerCase() === "tiempo vencedor") {
                        const match = valor.match(/PT(\d+)M([\d.]+)S/);
                        if (match) {
                            valor = `${parseInt(match[1],10)}:${match[2]}`;
                        }
                    }
                    pNuevo.textContent = `${campo}: ${valor}`;
                } else {
                    pNuevo.textContent = texto;
                }
                seccion.appendChild(pNuevo);
            }
        });

        return seccion;
    }

    insertarHTMLCircuito() {
        const seccion = this.procesarHTMLCircuito();
        if (!seccion) return;

        const mainCircuito = document.querySelector("main");
        if (!mainCircuito) {
            console.error("No se encontró <main> en el documento actual");
            return;
        }

        mainCircuito.appendChild(seccion);
    }


}



class CargadorSVG {
    constructor() {
        this.contenidoSVG = ""; // Aquí guardaremos el contenido del archivo
        this.comprobarApiFile();
    }

    comprobarApiFile() {
        const p = document.createElement("p");

        if (window.File && window.FileReader && window.FileList && window.Blob) {
            this.asignarEventoInputSVG();
        } else {
            p.textContent = "Este navegador NO soporta la API File y este programa no puede funcionar correctamente";
            document.body.appendChild(p);
        }
    }

    asignarEventoInputSVG()
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
            const h4 = document.createElement("h4");
            h4.textContent = "Gráfico con la altímetria del International Chang Circuit"
            const section = document.createElement("section");

            section.appendChild(h4);

            // Insertamos el contenido SVG
            section.innerHTML = this.contenidoSVG;

            section.prepend(h4);
            main.appendChild(section);
        }
    }

class CargadorKML
{
    constructor() {
        this.coordenadasOrigen = null;  // Para almacenar el punto inicial del circuito
        this.tramos = [];                // Array con los tramos del circuito
        this.contenidoKML = "";          // Contenido completo del KML
        this.comprobarApiFile();
    }

    comprobarApiFile() {
        const p = document.createElement("p");

        if (window.File && window.FileReader && window.FileList && window.Blob) {
            this.asignarEventoInputKML();
        } else {
            p.textContent = "Este navegador NO soporta la API File y este programa no puede funcionar correctamente";
            document.body.appendChild(p);
        }
    }

    asignarEventoInputKML()
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