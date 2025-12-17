class Cronometro {
    constructor() {
        this.tiempo = 0;      // Tiempo transcurrido en milisegundos
        this.inicio = null;    // Momento de inicio
        this.corriendo = null; // Intervalo activo
        this.parrafo = document.querySelector("main h2 + p")
        this.#addListeners();
        
    }

    #addListeners()
    {
        const buttons = document.querySelectorAll("main button")
        if(buttons.length > 0)
        {
            buttons[0].addEventListener("click", () => this.arrancar());
            buttons[1].addEventListener("click", () => this.parar());
            buttons[2].addEventListener("click", () => this.reiniciar());
        }
    }

    arrancar() {
        try {
            this.inicio = Temporal.Now.instant();
        } catch (e) {
            this.inicio = new Date();
        }

        // Inicia la actualización cada décima de segundo
        this.corriendo = setInterval(this.actualizar.bind(this), 100);
    }

    actualizar() {
        let ahora;

        try {
            ahora = Temporal.Now.instant();
            this.tiempo = parseInt(Number(ahora.epochMilliseconds) - Number(this.inicio.epochMilliseconds));
        } catch (e) {
            ahora = new Date();
            this.tiempo = parseInt(ahora - this.inicio);
        }

        this.mostrar(); // Actualizar el párrafo con el tiempo formateado
    }

    mostrar() {
        // Calcular minutos, segundos y décimas
        const minutos = parseInt(this.tiempo / 60000);
        const segundos = parseInt((this.tiempo % 60000) / 1000);
        const decimas = parseInt((this.tiempo % 1000) / 100);

        // Formatear con ceros a la izquierda
        const mm = String(minutos).padStart(2, "0");
        const ss = String(segundos).padStart(2, "0");

        const tiempoFormateado = `${mm}:${ss}.${decimas}`;

        // Buscar primer párrafo dentro de main
        if (this.parrafo) {
            this.parrafo.textContent = tiempoFormateado;
        }
    }

    parar() {
        if (this.corriendo) {
            clearInterval(this.corriendo);
            this.corriendo = null;
        }
    }

    reiniciar() {
        this.parar();
        this.tiempo = 0;
        this.mostrar();
    }
}
