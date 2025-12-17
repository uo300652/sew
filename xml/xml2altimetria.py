# xml2altimetria.py
# -*- coding: utf-8 -*-

import xml.etree.ElementTree as ET

class Svg(object):
    """
    Genera archivos SVG con polilíneas, líneas y texto
    """
    def __init__(self):
        self.raiz = ET.Element('svg', xmlns="http://www.w3.org/2000/svg")

    def addLine(self, x1, y1, x2, y2, stroke, strokeWith):
        line = ET.SubElement(self.raiz, 'line',
                             x1=str(x1), y1=str(y1),
                             x2=str(x2), y2=str(y2),
                             stroke=stroke)
        line.set("stroke-width", str(strokeWith))

    def addPolyline(self, points, stroke, strokeWith, fill):
        polyline = ET.SubElement(self.raiz, 'polyline',
                                 points=points,
                                 stroke=stroke,
                                 fill=fill)
        polyline.set("stroke-width", str(strokeWith))

    def addText(self, text, x, y, fontSize=14, style=""):
        attrs = {"x": str(x), "y": str(y), "font-size": str(fontSize)}
        if style:
            attrs["style"] = style
        ET.SubElement(self.raiz,'text', attrs).text = text

    def escribir(self, nombreArchivoSVG):
        arbol = ET.ElementTree(self.raiz)
        ET.indent(arbol)
        arbol.write(nombreArchivoSVG, encoding='utf-8', xml_declaration=True)

    def ver(self):
        print("\nElemento raiz =", self.raiz.tag)
        for hijo in self.raiz.findall('.//'):
            print("Elemento =", hijo.tag, "Atributos =", hijo.attrib)


def main():
    xml_file = "circuitoEsquema.xml"
    tree = ET.parse(xml_file)
    root = tree.getroot()
    ns = {'ns': 'http://www.uniovi.es'}

    distancias = []
    altitudes = []

    nodo_origen = root.find(".//ns:puntoOrigen/ns:altitud", ns)
    alt_origen = float(nodo_origen.attrib["cantidad"])
    distancias.append(0.0)
    altitudes.append(alt_origen)

    distancia_acumulada = 0.0
    tramos = root.findall(".//ns:tramos/ns:tramo", ns)
    for tramo in tramos:
        d = float(tramo.find("ns:distancia", ns).attrib["cantidad"])
        h = float(tramo.find("ns:punto/ns:altitud", ns).attrib["cantidad"])
        distancia_acumulada += d
        distancias.append(distancia_acumulada)
        altitudes.append(h)

    margin = 50
    width = 1000
    height = 600

    min_x = min(distancias)
    max_x = max(distancias)
    min_y = min(altitudes)
    max_y = max(altitudes)

    def sx(x):
        return margin + (x - min_x) / (max_x - min_x) * (width - 2 * margin)

    def sy(y):
        return margin + (max_y - y) / (max_y - min_y) * (height - 2 * margin)

    points = [f"{sx(x):.2f},{sy(y):.2f}" for x, y in zip(distancias, altitudes)]
    points_str = " ".join(points)

    svg = Svg()
    svg.addPolyline(points_str, stroke="red", strokeWith="3", fill="none")

    svg.addLine(sx(min_x), sy(min_y), sx(max_x), sy(min_y),
                stroke="black", strokeWith="2")

    svg.addLine(sx(min_x), sy(min_y), sx(min_x), sy(max_y),
                stroke="black", strokeWith="2")

    svg.addText("Distancia (m)", sx((min_x + max_x)/2), sy(min_y)+30, fontSize=16,
                style="text-anchor: middle;")

    svg.addText("Altitud (m)", sx(min_x)-40, sy((min_y + max_y)/2), fontSize=16,
                style="writing-mode: tb; glyph-orientation-vertical: 0;")

    svg.addText(f"{int(min_x)}", sx(min_x)-5, sy(min_y)+15, fontSize=12)
    svg.addText(f"{int(max_x)}", sx(max_x)-10, sy(min_y)+15, fontSize=12)
    svg.addText(f"{int(min_y)}", sx(min_x)-30, sy(min_y), fontSize=12)
    svg.addText(f"{int(max_y)}", sx(min_x)-30, sy(max_y), fontSize=12)

    svg.raiz.attrib["width"] = str(width)
    svg.raiz.attrib["height"] = str(height)
    svg.raiz.attrib["viewBox"] = f"0 0 {width} {height}"

    svg.escribir("altimetria.svg")
    print("Generado altimetria.svg correctamente")


if __name__ == "__main__":
    main()
