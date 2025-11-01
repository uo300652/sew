# xml2html.py
# -*- coding: utf-8 -*-
"""
Genera InfoCircuito.html a partir de circuitoEsquema.xml
Incluye etiquetas <picture> para las fotografías, usando versiones
de imagen específicas para móvil, tablet y monitor.
Convierte duraciones ISO 8601 (ej. PT1M30S) a texto legible.
Y añade enlaces <a> clicables en las referencias.
"""

import xml.etree.ElementTree as ET
import os
import re

class Html:
    """
    Clase auxiliar para crear HTML estructurado con enlace a CSS.
    """
    def __init__(self, titulo="Información del Circuito"):
        self.titulo = titulo
        self.raiz = ET.Element("html", attrib={"lang": "es"})
        self.head = ET.SubElement(self.raiz, "head")
        ET.SubElement(self.head, "meta", attrib={"charset": "utf-8"})
        ET.SubElement(self.head, "meta", attrib={
            "name": "viewport", "content": "width=device-width, initial-scale=1.0"})
        ET.SubElement(self.head, "title").text = self.titulo
        ET.SubElement(self.head, "link", attrib={"rel": "stylesheet", "href": "estilo.css"})
        self.body = ET.SubElement(self.raiz, "body")
        ET.SubElement(self.body, "h1").text = self.titulo

    def add_parrafo(self, texto):
        ET.SubElement(self.body, "p").text = texto

    def add_lista(self, items, enlaces=False):
        """
        Si 'enlaces=True', los elementos se interpretan como (texto, url) y se crean <a>.
        """
        ul = ET.SubElement(self.body, "ul")
        for item in items:
            li = ET.SubElement(ul, "li")
            if enlaces and isinstance(item, tuple):
                texto, url = item
                a = ET.SubElement(li, "a", attrib={"href": url, "target": "_blank"})
                a.text = texto
            else:
                li.text = item

    def escribir(self, nombre_archivo="InfoCircuito.html"):
        arbol = ET.ElementTree(self.raiz)
        try:
            ET.indent(arbol)  # Disponible desde Python 3.9
        except AttributeError:
            pass
        arbol.write(nombre_archivo, encoding="utf-8", xml_declaration=True)
        print(f"Archivo HTML generado: {nombre_archivo}")


def construir_rutas_imagen(url_base):
    """
    Recibe una URL base (por ejemplo './multimedia/LineaDeMeta.jpg')
    y devuelve un diccionario con las versiones Movil, Tablet y Monitor.
    """
    raiz, ext = os.path.splitext(url_base)
    return {
        "movil": f"{raiz}Movil{ext}",
        "tablet": f"{raiz}Tablet{ext}",
        "monitor": f"{raiz}Monitor{ext}",
        "base": url_base
    }


def formato_duracion_iso8601(duracion):
    """
    Convierte una duración ISO 8601 (como 'PT1M30.637S') en texto legible:
    PT1M30S → '1 minuto 30 s'
    PT2H5M → '2 horas 5 minutos'
    """
    if not duracion or not duracion.startswith("P"):
        return duracion

    patron = re.compile(r'P(T(?:(\d+)H)?(?:(\d+)M)?(?:(\d+(?:\.\d+)?)S)?)')
    m = patron.match(duracion)
    if not m:
        return duracion

    horas = m.group(2)
    minutos = m.group(3)
    segundos = m.group(4)

    partes = []
    if horas:
        partes.append(f"{int(horas)} {'hora' if int(horas)==1 else 'horas'}")
    if minutos:
        partes.append(f"{int(minutos)} {'minuto' if int(minutos)==1 else 'minutos'}")
    if segundos:
        seg = float(segundos)
        seg = round(seg)
        partes.append(f"{seg} s")

    return " ".join(partes)


def main():
    xml_path = "circuito.xml"
    ns = {'u': 'http://www.uniovi.es'}

    try:
        raiz = ET.parse(xml_path).getroot()
    except (IOError, ET.ParseError) as e:
        print("Error al leer el XML:", e)
        return

    html = Html("Información del Circuito")

    # Campos básicos del circuito
    campos = ["nombre", "longitud", "anchuraMedia", "fecha", "horaInicio",
              "numeroVueltas", "localidadProxima", "pais", "patrocinador",
              "vencedor", "tiempoVencedor"]

    for campo in campos:
        elem = raiz.find(f"u:{campo}", ns)
        if elem is not None:
            if "cantidad" in elem.attrib:
                texto = f"{campo.capitalize()}: {elem.attrib.get('cantidad')} {elem.attrib.get('unidad', '')}"
            else:
                valor = elem.text.strip() if elem.text else ""
                if campo == "tiempoVencedor":
                    valor = formato_duracion_iso8601(valor)
                texto = f"{campo.capitalize()}: {valor}"
            html.add_parrafo(texto)

    # Referencias (ahora con <a href="...">)
    referencias = []
    for ref in raiz.findall("u:referencias/u:referencia", ns):
        fuente = ref.attrib.get("fuente", "Desconocida")
        enlace = ref.attrib.get("enlace", "#")
        descripcion = ref.text.strip() if ref.text else ""
        texto = f"{fuente}: {descripcion}"
        referencias.append((texto, enlace))  # Guardamos tupla (texto, url)

    if referencias:
        html.add_parrafo("Referencias:")
        html.add_lista(referencias, enlaces=True)

    # Fotografías (usando <picture> responsivo)
    fotos = raiz.findall("u:fotografias/u:fotografia", ns)
    if fotos:
        html.add_parrafo("Fotografías:")
        for foto in fotos:
            url = foto.attrib.get("url", "#")
            descripcion = foto.attrib.get("descripcion", "Foto").strip()
            versiones = construir_rutas_imagen(url)

            picture = ET.SubElement(html.body, "picture")

            # Versiones específicas por tamaño
            ET.SubElement(picture, "source", attrib={
                "media": "(max-width: 465px)",
                "srcset": versiones["movil"]
            })
            ET.SubElement(picture, "source", attrib={
                "media": "(max-width: 900px)",
                "srcset": versiones["tablet"]
            })
            ET.SubElement(picture, "source", attrib={
                "media": "(min-width: 901px)",
                "srcset": versiones["monitor"]
            })
            ET.SubElement(picture, "img", attrib={
                "src": versiones["base"],
                "alt": descripcion
            })

    # Videos (si existen)
    videos = []
    for video in raiz.findall("u:videos/u:video", ns):
        url = video.attrib.get("url", "")
        if url:
            videos.append(url)
    if videos:
        html.add_parrafo("Videos:")
        html.add_lista(videos)

    # Clasificados
    clasificados = [c.text for c in raiz.findall("u:clasificados/u:clasificado", ns)]
    if clasificados:
        html.add_parrafo("Clasificados:")
        html.add_lista(clasificados)

    html.escribir("../InfoCircuito.html")


if __name__ == "__main__":
    main()
