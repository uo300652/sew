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
        const main = document.querySelector("h2 + section");

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
            const horaCompleta = times[i];
            const horaSolo = horaCompleta.includes("T") ? horaCompleta.split("T")[1] : horaCompleta;
            const horaNum = parseInt(horaSolo.split(":")[0], 10);

            // Filtrar solo horas entre 09:00 y 10:00
            if (horaNum >= 9 && horaNum <= 10) {
                this.datosHorarios.push({
                    hora: horaSolo,
                    temperatura: temps[i],
                    sensacionTermica: apparent[i],
                    lluvia: rains[i],
                    humedad: humidities[i],
                    velocidadViento: winds[i],
                    direccionViento: windDirs[i]
                });
            }
        }

        this.insertarMeteorologia();
    }

    insertarMeteorologia() {
        const $main = $("main");
        const $seccion = $("<section>").appendTo($main);

        $("<h2>").text("Meteorología el día de la carrera").appendTo($seccion);

        // Datos diarios
        const $ulDiarios = $("<ul>").appendTo($seccion);
        $("<li>").text("Amanecer: " + this.amanecer).appendTo($ulDiarios);
        $("<li>").text("Atardecer: " + this.atardecer).appendTo($ulDiarios);

        $("<h3>").text("Evolución horaria durante la carrera").appendTo($seccion);

        const $listaHoras = $("<ul>").appendTo($seccion);

        this.datosHorarios.forEach(hora => {
            const $liHora = $("<li>").appendTo($listaHoras);
            $("<strong>").text(hora.hora).appendTo($liHora);

            const $subLista = $("<ul>").appendTo($liHora);
            $("<li>").text("Temperatura: " + hora.temperatura + " °C").appendTo($subLista);
            $("<li>").text("Sensación térmica: " + hora.sensacionTermica + " °C").appendTo($subLista);
            $("<li>").text("Lluvia: " + hora.lluvia + " mm").appendTo($subLista);
            $("<li>").text("Humedad: " + hora.humedad + " %").appendTo($subLista);
            $("<li>").text("Viento: " + hora.velocidadViento + " m/s").appendTo($subLista);
            $("<li>").text("Dirección del viento: " + hora.direccionViento + "°").appendTo($subLista);
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

    // Procesa los datos JSON para calcular medias por día
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

            const t = Number(temps[i])      || 0;
            const r = Number(rains[i])      || 0;
            const v = Number(winds[i])      || 0;
            const h = Number(humidities[i]) || 0;

            dias[dia].sumTemp    += t;
            dias[dia].sumLluvia  += r;
            dias[dia].sumViento  += v;
            dias[dia].sumHumedad += h;
            dias[dia].count++;
        }

        this.mediasDiarias = [];

        for (const dia in dias) {
            const info = dias[dia];
            const count = info.count || 1;

            this.mediasDiarias.push({
                dia,
                tempMedia:    (info.sumTemp    / count).toFixed(2),
                lluviaMedia:  (info.sumLluvia  / count).toFixed(2),
                vientoMedia:  (info.sumViento  / count).toFixed(2),
                humedadMedia: (info.sumHumedad / count).toFixed(2)
            });
        }

        // Ordenar días cronológicamente
        this.mediasDiarias.sort((a, b) => a.dia.localeCompare(b.dia));

        // Insertar directamente en el DOM
        this.insertarEntrenos();
    }

    // Inserta las medias diarias en el DOM con lista principal y lista secundaria
    insertarEntrenos() {
        if (!this.mediasDiarias || !this.mediasDiarias.length) return;

        const main = document.querySelector("main");
        if (!main) {
            console.error("No se encontró el elemento <main>");
            return;
        }

        const $seccion = $("<section>").appendTo(main);
        $("<h2>").text("Meteorología entrenamientos (medias diarias)").appendTo($seccion);

        const $listaDias = $("<ul>").appendTo($seccion);

        this.mediasDiarias.forEach(dia => {
            const [yyyy, mm, dd] = dia.dia.split("-");
            const fechaES = `${dd}/${mm}/${yyyy}`;

            const $liDia = $("<li>").appendTo($listaDias);
            $("<strong>").text(fechaES).appendTo($liDia);

            const $subLista = $("<ul>").appendTo($liDia);
            $("<li>").text("Temperatura media: " + dia.tempMedia + " °C").appendTo($subLista);
            $("<li>").text("Lluvia media: " + dia.lluviaMedia + " mm").appendTo($subLista);
            $("<li>").text("Viento medio: " + dia.vientoMedia + " m/s").appendTo($subLista);
            $("<li>").text("Humedad media: " + dia.humedadMedia + " %").appendTo($subLista);
        });
    }
}
