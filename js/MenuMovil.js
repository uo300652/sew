class MenuMovil {
    constructor() {
        this.toggleButton = document.querySelector("header > button");
        this.menu = document.querySelector("header > nav");
        this.closeMenu();

        this.initEventListeners();
    }

    initEventListeners() {
        // Alternar menú al pulsar el botón
        this.toggleButton.addEventListener('click', () => this.toggleMenu());
    }

    toggleMenu() {
        if (this.menu.hasAttribute("hidden")) {
            this.menu.removeAttribute("hidden");
        } else {
            this.menu.setAttribute("hidden", "");
        }
    }

    closeMenu() {
        this.menu.setAttribute("hidden", "");
    }

}
