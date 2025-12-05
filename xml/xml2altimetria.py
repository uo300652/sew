# 02030-SVG_fix.py
# -*- coding: utf-8 -*-

import xml.etree.ElementTree as ET

SVG_NS = "http://www.w3.org/2000/svg"
ET.register_namespace("", SVG_NS)

class Svg(object):
    def __init__(self, width=1000, height=600):
        self.width = width
        self.height = height
        self.raiz = ET.Element(f"{{{SVG_NS}}}svg", {
            "version": "1.1",
            "width": str(width),
            "height": str(height),
            "viewBox": f"0 0 {width} {height}"
        })

    def addPolyline(self, points, stroke="black", stroke_width=2, fill="none"):
        ET.SubElement(self.raiz, f"{{{SVG_NS}}}polyline", {
            "points": points,
            "stroke": stroke,
            "stroke-width": str(stroke_width),
            "fill": fill,
            "stroke-linejoin": "round",
            "stroke-linecap": "round"
        })

    def addCircle(self, cx, cy, r, fill="red"):
        ET.SubElement(self.raiz, f"{{{SVG_NS}}}circle", {
            "cx": str(cx),
            "cy": str(cy),
            "r": str(r),
            "fill": fill
        })

    def addText(self, texto, x, y, font_family="Verdana", font_size="12", style=""):
        attrs = {
            "x": str(x),
            "y": str(y),
            "font-family": font_family,
            "font-size": str(font_size)
        }
        if style:
            attrs["style"] = style
        ET.SubElement(self.raiz, f"{{{SVG_NS}}}text", attrs).text = texto

    def escribir(self, nombreArchivoSVG):
        arbol = ET.ElementTree(self.raiz)
        # ET.indent requiere Python 3.9+; si usas 3.8, comenta la línea siguiente.
        try:
            ET.indent(arbol)
        except AttributeError:
            pass
        arbol.write(nombreArchivoSVG, encoding='utf-8', xml_declaration=True)

def main():
    archivo_xml = "circuito.xml"
    arbol = ET.parse(archivo_xml)
    raiz = arbol.getroot()

    # Ajusta este namespace a tu XML real
    ns = {'ns': 'http://www.uniovi.es'}

    coords = []

    # Origen
    origen = raiz.find('.//ns:puntoOrigen', ns)
    if origen is not None:
        lon_e = origen.find('ns:longitudPunto', ns)
        lat_e = origen.find('ns:latitud', ns)  # <-- verifica si es 'latitud' o 'latitudPunto'
        if lon_e is not None and lat_e is not None:
            lon = float(lon_e.attrib['cantidad'])
            lat = float(lat_e.attrib['cantidad'])
            coords.append((lon, lat))

    # Tramos
    for tramo in raiz.findall('.//ns:tramo', ns):
        lon_elem = tramo.find('ns:punto/ns:longitudPunto', ns)
        lat_elem = tramo.find('ns:punto/ns:latitud', ns)  # <-- idem
        if lon_elem is not None and lat_elem is not None:
            lon = float(lon_elem.attrib['cantidad'])
            lat = float(lat_elem.attrib['cantidad'])
            coords.append((lon, lat))

    if len(coords) < 2:
        print(f"Hay {len(coords)} punto(s). Con menos de 2 una polilínea no se ve.")
        # Aun así, genera un SVG con una marca si hay 1 punto para depurar
        svg = Svg()
        if coords:
            # Escala trivial a centro para ver la marca
            svg.addText("Sólo hay un punto", svg.width/2, 30, font_size=16, style="text-anchor: middle;")
            svg.addCircle(svg.width/2, svg.height/2, 5, fill="red")
        svg.escribir("circuito.svg")
        return

    # Escalado a SVG
    margin = 50
    width, height = 1000, 600

    lons = [c[0] for c in coords]
    lats = [c[1] for c in coords]
    min_lon, max_lon = min(lons), max(lons)
    min_lat, max_lat = min(lats), max(lats)

    # Evitar rangos cero
    lon_span = max(max_lon - min_lon, 1e-9)
    lat_span = max(max_lat - min_lat, 1e-9)

    points_svg = []
    for lon, lat in coords:
        x = margin + (lon - min_lon) / lon_span * (width - 2*margin)
        y = margin + (max_lat - lat) / lat_span * (height - 2*margin)  # invertir Y
        points_svg.append((x, y))

    # Cerrar polilínea
    if points_svg[0] != points_svg[-1]:
        points_svg.append(points_svg[0])

    points_str = " ".join(f"{x:.2f},{y:.2f}" for x, y in points_svg)

    svg = Svg(width=width, height=height)
    svg.addPolyline(points_str, stroke="blue", stroke_width=3, fill="none")
    svg.addText("Chang International Circuit (vista aérea)", width/2, 30,
                font_size=20, style="text-anchor: middle; fill:orange;")
    # Marca el inicio para depurar
    svg.addCircle(points_svg[0][0], points_svg[0][1], 3, fill="red")

    nombre_svg = "altimetria.svg"
    svg.escribir(nombre_svg)
    print(f"Archivo SVG creado: {nombre_svg} con {len(coords)} puntos")

if __name__ == "__main__":
    main()