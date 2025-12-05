// ciudad.js
class Ciudad {
    constructor(nombreCiudad, pais, gentilicio) {
        this.nombreCiudad = nombreCiudad;
        this.pais = pais;
        this.gentilicio = gentilicio;

        this.timezone = "Asia/Bangkok";  
        this.urlBase   = "https://archive-api.open-meteo.com/v1/archive";
    }

    rellenar(cantidadPoblacion, lat, lon) {
        this.cantidadPoblacion = cantidadPoblacion;
        this.lat = lat;
        this.lon = lon;
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
        const main = document.querySelector("main");

        const p = document.createElement("p");
        p.textContent = "Coordenadas: " + this.lat + "," + this.lon;
        main.appendChild(p);
    }

    getMeteorologiaCarrera() {
        const startDate = "2025-03-02";
        const endDate   = "2025-03-02";

        const url = this.urlBase
            + "?latitude=" + encodeURIComponent(this.lat)
            + "&longitude=" + encodeURIComponent(this.lon)
            + "&start_date=" + encodeURIComponent(startDate)
            + "&end_date=" + encodeURIComponent(endDate)
            + "&daily=sunrise,sunset"
            + "&hourly=temperature_2m,apparent_temperature,rain,relative_humidity_2m,wind_speed_10m,wind_direction_10m"
            + "&timezone=" + encodeURIComponent(this.timezone);

        $.ajax({
            dataType: "json",
            url: url,
            method: "GET",
            success: (datos) => {  
                this.procesarJSONCarrera(datos);
            },
            error: function() {
                console.error("Error al obtener los datos meteorológicos");
            }
        });
    }

    procesarJSONCarrera(datos) {
        if (!datos) return;

        const daily = datos.daily || {};
        const sunriseArr = daily.sunrise || [];
        const sunsetArr  = daily.sunset  || [];

        this.amanecer  = sunriseArr[0] ? sunriseArr[0].split("T")[1] : "";
        this.atardecer = sunsetArr[0] ? sunsetArr[0].split("T")[1] : "";

        const hourly = datos.hourly || {};

        const times      = hourly.time || [];
        const temps      = hourly.temperature_2m || [];
        const apparent   = hourly.apparent_temperature || [];
        const rains      = hourly.rain || [];
        const humidities = hourly.relative_humidity_2m || [];
        const winds      = hourly.wind_speed_10m || [];
        const windDirs   = hourly.wind_direction_10m || [];

        this.datosHorarios = [];

        for (let i = 0; i < times.length; i++) {
            this.datosHorarios.push({
                hora: times[i],                             
                temperatura: temps[i],
                sensacionTermica: apparent[i],
                lluvia: rains[i],
                humedad: humidities[i],
                velocidadViento: winds[i],
                direccionViento: windDirs[i]
            });
        }

        const $main = $("main");

        const $seccion = $("<section>").appendTo($main);

        $("<h2>").text("Meteorología el día de la carrera").appendTo($seccion);

        // Lista de datos diarios
        const $ulDiarios = $("<ul>").appendTo($seccion);
        $("<li>").text("Amanecer: " + this.amanecer).appendTo($ulDiarios);
        $("<li>").text("Atardecer: " + this.atardecer).appendTo($ulDiarios);

        $("<h3>").text("Evolución horaria a partir del inicio de la carrera").appendTo($seccion);

        const $listaHoras = $("<ul>").appendTo($seccion);

        this.datosHorarios
        .filter(hora => {
            if (!hora.hora || !hora.hora.includes("T")) return false;
            const horaSolo = hora.hora.split("T")[1]; 
            const horasNum = parseInt(horaSolo.split(":")[0], 10); 
            return horasNum >= 14;
        }).forEach(hora => {
            const horaSolo = hora.hora && hora.hora.includes("T")
                ? hora.hora.split("T")[1]
                : hora.hora;

        const $li = $("<li>").appendTo($listaHoras);

        $li.text(
            horaSolo +
            " - Temperatura: " + hora.temperatura + " °C" +
            ", Sensación: " + hora.sensacionTermica + " °C" +
            ", Lluvia: " + hora.lluvia + " mm" +
            ", Humedad: " + hora.humedad + " %" +
            ", Viento: " + hora.velocidadViento + " m/s" +
            ", Dirección viento: " + hora.direccionViento + "°"
        );
    });

    this.getMeteorologiaEntrenos();
}



    getMeteorologiaEntrenos() {
        const startDate = "2025-02-27";
        const endDate   = "2025-03-01";

        const url = this.urlBase
            + "?latitude=" + encodeURIComponent(this.lat)
            + "&longitude=" + encodeURIComponent(this.lon)
            + "&start_date=" + encodeURIComponent(startDate)
            + "&end_date=" + encodeURIComponent(endDate)
            + "&hourly=temperature_2m,rain,relative_humidity_2m,wind_speed_10m"
            + "&timezone=" + encodeURIComponent(this.timezone);


        $.ajax({
            dataType: "json",
            url: url,
            method: "GET",
            success: (datos) => {
                this.procesarJSONEntrenos(datos);
            },
            error: () => {
                console.error("Error al obtener los datos meteorológicos de entrenos");
            }
        });
    }

    procesarJSONEntrenos(datos) {
        if (!datos || !datos.hourly) return;

        const hourly = datos.hourly;

        const times       = hourly.time || [];
        const temps       = hourly.temperature_2m || [];
        const rains       = hourly.rain || [];
        const winds       = hourly.wind_speed_10m || [];
        const humidities  = hourly.relative_humidity_2m || [];

        const dias = {}; 

        for (let i = 0; i < times.length; i++) {
            const fechaCompleta = times[i];           // "2025-02-27T13:00"
            const dia = fechaCompleta.split("T")[0];  // "2025-02-27"

            if (!dias[dia]) {
                dias[dia] = {
                    sumTemp: 0,
                    sumLluvia: 0,
                    sumViento: 0,
                    sumHumedad: 0,
                    count: 0
                };
            }

            const t  = Number(temps[i])      || 0;
            const r  = Number(rains[i])      || 0;
            const v  = Number(winds[i])      || 0;
            const h  = Number(humidities[i]) || 0;

            dias[dia].sumTemp    += t;
            dias[dia].sumLluvia  += r;
            dias[dia].sumViento  += v;
            dias[dia].sumHumedad += h;
            dias[dia].count++;
        }

        const medias = [];
        for (const dia in dias) {
            const info = dias[dia];
            const count = info.count || 1;

            medias.push({
                dia: dia,
                tempMedia:    (info.sumTemp    / count).toFixed(2),
                lluviaMedia:  (info.sumLluvia  / count).toFixed(2),
                vientoMedia:  (info.sumViento  / count).toFixed(2),
                humedadMedia: (info.sumHumedad / count).toFixed(2)
            });
        }

        // Opcional: ordenar días
        medias.sort((a, b) => a.dia.localeCompare(b.dia));

        // Pintar en el DOM como UL
        const $main = $("main");
        const $seccion = $("<section>").appendTo($main);

        $("<h2>")
            .text("Meteorología entrenamientos (medias diarias)")
            .appendTo($seccion);

        // Lista de días (ul, el orden no es tan crítico como en horas concretas)
        const $listaDias = $("<ul>").appendTo($seccion);

        medias.forEach(dia => {
            const $li = $("<li>").appendTo($listaDias);

            // Puedes maquetarlo en una sola línea:
            // "2025-02-27 - Temp media: X°C, Lluvia media: Y mm..."
            $li.text(
                dia.dia +
                " - Temp media: " + dia.tempMedia + " °C" +
                ", Lluvia media: " + dia.lluviaMedia + " mm" +
                ", Viento medio: " + dia.vientoMedia + " m/s" +
                ", Humedad media: " + dia.humedadMedia + " %"
            );
        });
    }


}
