// ciudad.js
// Clase para gestionar información de una ciudad

class Ciudad {
    constructor(nombreCiudad, pais, gentilicio) {
        this.nombreCiudad = nombreCiudad;
        this.pais = pais;
        this.gentilicio = gentilicio;
    }

    rellenar(cantidadPoblacion, coordenadasCentro) {
        this.cantidadPoblacion = cantidadPoblacion;
        this.coordenadasCentro = coordenadasCentro;
    }

    ciudadTexto() {
        return "Nombre de la ciudad: " + this.nombreCiudad;
    }

    paisTexto() {
        return " Pais: " + this.pais;
    }

    infoSecundariaTexto() {
        return "<ul>" +
            "<li>Gentilicio: " + this.gentilicio + "</li>" +
            "<li>Población: " + this.cantidadPoblacion + "</li>" +
            "</ul>";
    }

    escribirCoordenadas() {
        return "<p>Coordenadas: " + this.coordenadasCentro + "</p>";
    }
}
