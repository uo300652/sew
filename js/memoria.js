class Memoria
{
    constructor()
    {
        this.tablero_bloqueado = false;
        this.primera_carta = null;
        this.segunda_carta = null;

        this.barajarCartas();

        this.cronometro = new Cronometro();
        this.cronometro.arrancar();
    }

    voltearCarta(carta) {
        if (this.tablero_bloqueado) return;

        const estado = carta.getAttribute("data-estado");
        if (estado === "volteada" || estado === "revelada") return;

        carta.setAttribute("data-estado", "volteada");

        if (!this.primera_carta) {
            this.primera_carta = carta;
            return;
        }

        this.segunda_carta = carta;

        this.tablero_bloqueado = true;

        this.comprobarPareja();
    }

    barajarCartas() {
        // 1. Seleccionar el contenedor principal
        const main = document.querySelector("main");

        // 2. Obtener todos los hijos del main
        const hijos = Array.from(main.children);

        // 3. Mantener los dos primeros elementos fijos (<p> y <h2>)
        const elementosFijos = hijos.slice(0, 3);
        const cartas = hijos.slice(3); // el resto son las cartas

        // 4. Barajar las cartas
        for (let i = cartas.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [cartas[i], cartas[j]] = [cartas[j], cartas[i]];
        }

        // 5. Limpiar el contenido del main
        main.innerHTML = "";

        // 6. Volver a insertar los elementos fijos
        elementosFijos.forEach(el => main.appendChild(el));

        // 7. Añadir las cartas barajadas
        cartas.forEach(carta => main.appendChild(carta));
    }

    reiniciarAtributos()
    {
        this.tablero_bloqueado = false;
        this.primera_carta = null;
        this.segunda_carta = null;
    }

    deshabilitarCartas()
    {
        this.primera_carta.setAttribute("data-estado", "revelada");
        this.segunda_carta.setAttribute("data-estado", "revelada");

        this.reiniciarAtributos();
        this.comprobarJuego();
    }

    comprobarJuego() {
    const main = document.querySelector("main");
    const cartas = Array.from(main.querySelectorAll("article"));

    let todasReveladas = true;

    for (let i = 0; i < cartas.length; i++) {
        if (cartas[i].getAttribute("data-estado") !== "revelada") {
            todasReveladas = false;
            break; // ya no hace falta seguir comprobando
        }
    }

    if (todasReveladas) {
        this.tablero_bloqueado = true;
        this.cronometro.parar();
    }
}


    cubrirCartas() {
    this.tablero_bloqueado = true;

    setTimeout(() => {
        if (this.primera_carta) this.primera_carta.removeAttribute("data-estado");
        if (this.segunda_carta) this.segunda_carta.removeAttribute("data-estado");

        this.reiniciarAtributos();
        }, 1500);
    }

    comprobarPareja()
    {
        const img1 = this.primera_carta.children[1];
        const src1 = img1.getAttribute("src");

        const img2 = this.segunda_carta.children[1];
        const src2 = img2.getAttribute("src");

        (src1 == src2) ? this.deshabilitarCartas() : this.cubrirCartas();
    }

}