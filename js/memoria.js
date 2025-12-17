class Memoria
{
    constructor()
    {
        this.tablero_bloqueado = false;
        this.primera_carta = null;
        this.segunda_carta = null;

        this.#barajarCartas();

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

        this.#comprobarPareja();
    }

    #barajarCartas() {
        const main = document.querySelector("main");
        const hijos = Array.from(main.children);

        // Mantiene h2, p (y posibles elementos estáticos)
        const elementosFijos = hijos.slice(0, 2);
        const cartas = hijos.slice(2);

        // Barajar
        for (let i = cartas.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [cartas[i], cartas[j]] = [cartas[j], cartas[i]];
        }

        main.innerHTML = "";
        elementosFijos.forEach(el => main.appendChild(el));
        cartas.forEach(carta => main.appendChild(carta));

        cartas.forEach(carta => {
            carta.addEventListener("click", () => this.voltearCarta(carta));
        });
    }


    #reiniciarAtributos()
    {
        this.tablero_bloqueado = false;
        this.primera_carta = null;
        this.segunda_carta = null;
    }

    #deshabilitarCartas()
    {
        this.primera_carta.setAttribute("data-estado", "revelada");
        this.segunda_carta.setAttribute("data-estado", "revelada");

        this.#reiniciarAtributos();
        this.#comprobarJuego();
    }

    #comprobarJuego() {
    const main = document.querySelector("main");
    const cartas = Array.from(main.querySelectorAll("article"));

    let todasReveladas = true;

    for (let i = 0; i < cartas.length; i++) {
        if (cartas[i].getAttribute("data-estado") !== "revelada") {
            todasReveladas = false;
            break;
        }
    }

    if(todasReveladas)
    {
        this.tablero_bloqueado = true;
        this.cronometro.parar();
    }         
}


    #cubrirCartas() {
        this.tablero_bloqueado = true;

        setTimeout(() => {
            if (this.primera_carta) this.primera_carta.removeAttribute("data-estado");
            if (this.segunda_carta) this.segunda_carta.removeAttribute("data-estado");

            this.#reiniciarAtributos();
            }, 1500);
    }

    #comprobarPareja()
    {
        const img1 = this.primera_carta.children[1];
        const src1 = img1.getAttribute("src");

        const img2 = this.segunda_carta.children[1];
        const src2 = img2.getAttribute("src");

        (src1 == src2) ? this.#deshabilitarCartas() : this.#cubrirCartas();
    }

}