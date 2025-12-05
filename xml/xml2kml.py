# 02020-KML.py
# # -*- coding: utf-8 -*-
""""
Crea archivos KML con puntos y líneas

@version 1.1 19/Octubre/2024
@author: Juan Manuel Cueva Lovelle. Universidad de Oviedo
"""

import xml.etree.ElementTree as ET

class Kml(object):
    """
    Genera archivo KML con puntos y líneas
    @version 1.1 19/Octumbre/2024
    @author: Juan Manuel Cueva Lovelle. Universidad de Oviedo
    """
    def __init__(self):
        """
        Crea el elemento raíz y el espacio de nombres
        """
        self.raiz = ET.Element('kml', xmlns="http://www.opengis.net/kml/2.2")
        self.doc = ET.SubElement(self.raiz,'Document')

    def addPlacemark(self,nombre,descripcion,long,lat,alt, modoAltitud):
        """
        Añade un elemento <Placemark> con puntos <Point>
        """
        pm = ET.SubElement(self.doc,'Placemark')
        ET.SubElement(pm,'name').text = nombre
        ET.SubElement(pm,'description').text = descripcion
        punto = ET.SubElement(pm,'Point')
        ET.SubElement(punto,'coordinates').text = '{},{},{}'.format(long,lat,alt)
        ET.SubElement(punto,'altitudeMode').text = modoAltitud

    def addLineString(self,nombre,extrude,tesela, listaCoordenadas, modoAltitud, color, ancho):
        """
        Añade un elemento <Placemark> con líneas <LineString>
        """
        ET.SubElement(self.doc,'name').text = nombre
        pm = ET.SubElement(self.doc,'Placemark')
        ls = ET.SubElement(pm, 'LineString')
        ET.SubElement(ls,'extrude').text = extrude
        ET.SubElement(ls,'tessellation').text = tesela
        ET.SubElement(ls,'coordinates').text = listaCoordenadas
        ET.SubElement(ls,'altitudeMode').text = modoAltitud

        estilo = ET.SubElement(pm, 'Style')
        linea = ET.SubElement(estilo, 'LineStyle')
        ET.SubElement (linea, 'color').text = color
        ET.SubElement (linea, 'width').text = ancho

    def escribir(self,nombreArchivoKML):
        """
        Escribe el archivo KML con declaración y codificación
        """
        arbol = ET.ElementTree(self.raiz)
        """
        Introduce indentacióon y saltos de línea
        para generar XML en modo texto
        """
        ET.indent(arbol)
        arbol.write(nombreArchivoKML, encoding='utf-8', xml_declaration=True)

    def ver(self):
        """
        Muestra el archivo KML. Se utiliza para depurar
        """
        print("\nElemento raiz = ", self.raiz.tag)

        if self.raiz.text != None:
            print("Contenido = "    , self.raiz.text.strip('\n')) #strip() elimina los '\n' del string
        else:
            print("Contenido = "    , self.raiz.text)

        print("Atributos = "    , self.raiz.attrib)

        # Recorrido de los elementos del árbol
        for hijo in self.raiz.findall('.//'): # Expresión XPath
            print("\nElemento = " , hijo.tag)
            if hijo.text != None:
                print("Contenido = ", hijo.text.strip('\n')) #strip() elimina los '\n' del string
            else:
                print("Contenido = ", hijo.text)
            print("Atributos = ", hijo.attrib)

def main():
    print(Kml.__doc__)

    # ---- Entradas (ajusta los nombres de archivo si lo necesitas) ----
    xsd_path = "circuito.xsd"   # Esquema con targetNamespace="http://www.uniovi.es"
    xml_path = "circuito.xml"   # Instancia que cumple el XSD
    nombreKML = "circuito.kml"  # Salida KML

    # ---- Namespaces para XPath ----
    XS = {'xs': 'http://www.w3.org/2001/XMLSchema'}
    U  = {'u' : 'http://www.uniovi.es'}

    # ---- Utilidades locales ----
    def dec(x):
        """Convierte string a float aceptando coma decimal (estilo 02000-XML.py)."""
        if x is None:
            return None
        return float(str(x).replace(',', '.'))

    # ---- 1) Recorrer el DOM del XSD con XPath para localizar metadatos de coordenadas ----
    try:
        esquema = ET.parse(xsd_path).getroot()
    except (IOError, ET.ParseError):
        print("Error procesando el archivo XSD = ", xsd_path)
        return

    # Función: dado un nombre de elemento (p.ej., 'longitudPunto'), extrae sus atributos definidos en el XSD
    def atributos_de_elemento(nombre_elem):
        ext = esquema.find(f".//xs:element[@name='{nombre_elem}']/xs:complexType/xs:simpleContent/xs:extension", XS)
        if ext is None:
            return set()
        return {a.get('name') for a in ext.findall("xs:attribute", XS)}

    # Elementos de coordenadas según el XSD: longitud, latitud, altitud
    atributos_longitudPunto = atributos_de_elemento('longitudPunto')
    atributos_latitud       = atributos_de_elemento('latitud')
    atributos_altitud       = atributos_de_elemento('altitud')

    # (Opcional) Muestra lo detectado en el XSD
    print("Atributos definidos en XSD:")
    print(" - longitudPunto:", atributos_longitudPunto)
    print(" - latitud      :", atributos_latitud)
    print(" - altitud      :", atributos_altitud)

    # ---- 2) Recorrer el DOM del XML de instancia con XPath para extraer coordenadas ----
    try:
        raiz = ET.parse(xml_path).getroot()
    except (IOError, ET.ParseError):
        print("Error procesando el archivo XML = ", xml_path)
        return

    coords = []       # lista de (lon, lat, alt)
    puntos_info = []  # lista de (nombre, descripcion)

    # 2.1 Punto de origen
    po = raiz.find('./u:puntoOrigen', U)
    if po is not None:
        el_lon = po.find('u:longitudPunto', U)
        el_lat = po.find('u:latitud', U)
        el_alt = po.find('u:altitud', U)

        lon = dec(el_lon.get('cantidad')) if el_lon is not None else None
        lat = dec(el_lat.get('cantidad')) if el_lat is not None else None
        alt = dec(el_alt.get('cantidad')) if el_alt is not None else 0.0

        if (lon is not None) and (lat is not None):
            coords.append((lon, lat, alt))
            puntos_info.append(("Origen", "Punto de origen"))

    # 2.2 Puntos de cada tramo
    for i, p in enumerate(raiz.findall('./u:tramos/u:tramo/u:punto', U), start=1):
        el_lon = p.find('u:longitudPunto', U)
        el_lat = p.find('u:latitud', U)
        el_alt = p.find('u:altitud', U)

        lon = dec(el_lon.get('cantidad')) if el_lon is not None else None
        lat = dec(el_lat.get('cantidad')) if el_lat is not None else None
        alt = dec(el_alt.get('cantidad')) if el_alt is not None else 0.0

        if (lon is None) or (lat is None):
            continue

        coords.append((lon, lat, alt))
        puntos_info.append((f"Tramo {i}", f"Punto de tramo {i}"))

    # 2.3 Cerrar el circuito (opcional)
    if len(coords) >= 2 and coords[0] != coords[-1]:
        coords.append(coords[0])

    # 2.4 Nombre de la ruta
    nombre_ruta = raiz.findtext('./u:nombre', default='Ruta', namespaces=U)

    # ---- 3) Generar KML ----
    nuevoKML = Kml()

    # Puntos individuales
    for (titulo, desc), (lon, lat, alt) in zip(puntos_info, coords):
        nuevoKML.addPlacemark(titulo, desc, lon, lat, alt, 'relativeToGround')

    # Línea con todos los puntos
    coordenadas_str = "\n".join(f"{lon},{lat},{alt}" for lon, lat, alt in coords)

    nuevoKML.addLineString(nombre_ruta, "1", "1",
                           coordenadas_str, 'relativeToGround',
                           '#ff0000ff', "5")

    # Visualizar y escribir archivo
    nuevoKML.ver()
    nuevoKML.escribir(nombreKML)
    print("Creado el archivo: ", nombreKML)

if __name__ == "__main__":
    main()
