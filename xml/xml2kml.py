# 02020-KML-Circuito.py
# -*- coding: utf-8 -*-
"""
Generación de archivo KML a partir de un XML validado por XSD
usando expresiones XPath (ElementTree, Uniovi)

@author: Matias Valle Trapiella
Universidad de Oviedo
"""

import xml.etree.ElementTree as ET

class Kml(object):

    def __init__(self):
        self.raiz = ET.Element('kml', xmlns="http://www.opengis.net/kml/2.2")
        self.doc = ET.SubElement(self.raiz, 'Document')

    def addPlacemark(self, nombre, descripcion, lon, lat, alt, modoAltitud):
        pm = ET.SubElement(self.doc, 'Placemark')
        ET.SubElement(pm, 'name').text = nombre
        ET.SubElement(pm, 'description').text = descripcion
        punto = ET.SubElement(pm, 'Point')
        ET.SubElement(punto, 'coordinates').text = f"{lon},{lat},{alt}"
        ET.SubElement(punto, 'altitudeMode').text = modoAltitud

    def addLineString(self, nombre, extrude, tesela,
                      listaCoordenadas, modoAltitud, color, ancho):

        ET.SubElement(self.doc, 'name').text = nombre
        pm = ET.SubElement(self.doc, 'Placemark')
        ls = ET.SubElement(pm, 'LineString')
        ET.SubElement(ls, 'extrude').text = extrude
        ET.SubElement(ls, 'tessellation').text = tesela
        ET.SubElement(ls, 'coordinates').text = listaCoordenadas
        ET.SubElement(ls, 'altitudeMode').text = modoAltitud

        estilo = ET.SubElement(pm, 'Style')
        linea = ET.SubElement(estilo, 'LineStyle')
        ET.SubElement(linea, 'color').text = color
        ET.SubElement(linea, 'width').text = ancho

    def escribir(self, nombreArchivoKML):
        arbol = ET.ElementTree(self.raiz)
        ET.indent(arbol)
        arbol.write(nombreArchivoKML, encoding='utf-8', xml_declaration=True)

    def ver(self):
        print("\nElemento raíz =", self.raiz.tag)
        for hijo in self.raiz.findall('.//'): 
            print("\nElemento =", hijo.tag)
            print("Contenido =", hijo.text)
            print("Atributos =", hijo.attrib)


def main():

    xml_path = "circuitoEsquema.xml"
    nombreKML = "circuito.kml"

    # Namespace del XML
    NS = {'u': 'http://www.uniovi.es'}

    # 1) Cargar XML
    try:
        raiz = ET.parse(xml_path).getroot()
    except (IOError, ET.ParseError):
        print("Error procesando el archivo XML")
        return

    # 2) Nombre del circuito 
    nombre_circuito = raiz.findtext('./u:nombre', default='Circuito', namespaces=NS)

    # 3) Punto de origen 
    coords = []
    puntos_info = []

    po = raiz.find('./u:puntoOrigen', NS)

    if po is not None:
        el_lon = po.find('./u:longitudPunto', NS)
        el_lat = po.find('./u:latitud', NS)
        el_alt = po.find('./u:altitud', NS)

        lon = el_lon.get('cantidad') if el_lon is not None else None
        lat = el_lat.get('cantidad') if el_lat is not None else None
        alt = el_alt.get('cantidad') if el_alt is not None else '0'

        if lon is not None and lat is not None:
            coords.append((lon, lat, alt))
            puntos_info.append(("Origen", "Punto de origen del circuito"))

    # 4) Puntos finales de los tramos (XPath)
    tramos = raiz.findall('./u:tramos/u:tramo/u:punto', NS)

    for i, p in enumerate(tramos, start=1):

        el_lon = p.find('./u:longitudPunto', NS)
        el_lat = p.find('./u:latitud', NS)
        el_alt = p.find('./u:altitud', NS)

        lon = el_lon.get('cantidad') if el_lon is not None else None
        lat = el_lat.get('cantidad') if el_lat is not None else None
        alt = el_alt.get('cantidad') if el_alt is not None else '0'

        if lon is None or lat is None:
            continue

        coords.append((lon, lat, alt))
        puntos_info.append((f"Tramo {i}", f"Punto final del tramo {i}"))

    # 5) Cerrar el circuito
    if len(coords) > 1 and coords[0] != coords[-1]:
        coords.append(coords[0])

    # 6) Generar KML
    nuevoKML = Kml()

    for (titulo, desc), (lon, lat, alt) in zip(puntos_info, coords):
        nuevoKML.addPlacemark(
            titulo,
            desc,
            lon, lat, alt,
            'relativeToGround'
        )

    coordenadas_str = "\n".join(
        f"{lon},{lat},{alt}" for lon, lat, alt in coords
    )

    nuevoKML.addLineString(
        nombre_circuito,
        "1", "1",
        coordenadas_str,
        'relativeToGround',
        '#ff0000ff', "5"
    )

    nuevoKML.ver()
    nuevoKML.escribir(nombreKML)

    print("Creado el archivo:", nombreKML)


if __name__ == "__main__":
    main()
