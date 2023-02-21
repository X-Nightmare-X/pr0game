<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

//SHORT NAMES FOR COMBAT REPORTS

$LNG['shortNames'] = [
    202 => 'Kl. Handelsschiff',
    203 => 'Gr. Handelsschiff',
    204 => 'L. Brig',
    205 => 'S. Brig',
    206 => 'Schoner',
    207 => 'Korvette',
    208 => 'Fregatte',
    209 => 'Wracktaucher',
    210 => 'Spähpapagei',
    211 => 'Kanonenschiff',
    212 => 'Fischerboot',
    213 => 'Linienschiff',
    214 => 'Schwimmende Festung',
    215 => 'Fregatte',
    216 => 'Lune Noire',
    217 => 'Evo. Transporter',
    218 => 'Avatar',
    219 => 'Gigarecycler',

    401 => '12-Pfünder',
    402 => 'Balliste',
    403 => 'Feuerballiste',
    404 => '30-Pfünder',
    405 => 'Kartätsche',
    406 => 'Mörser',
    407 => 'Holzwall',
    408 => 'Steinwall',
    409 => 'Gig. Schildkuppel',
    410 => 'Gravitonenkanone',
    411 => 'Orb. VerPla',
];

$LNG['bonus'] = [
    'Attack'            => 'Kanonen',
    'Defensive'         => 'Schiffsbau',
    'Shield'            => 'Holz',
    'BuildTime'         => 'Bauzeit',
    'ResearchTime'          => 'Forschungszeit',
    'ShipTime'          => 'Schiffbauzeit',
    'DefensiveTime'         => 'Verteidungsbauzeit',
    'Resource'          => 'Ressourcenertrag',
    'Energy'            => 'Nahrungserzeugung',
    'ResourceStorage'       => 'Speicher',
    'ShipStorage'           => 'Flottenkapazität',
    'FlyTime'           => 'Flugzeit',
    'FleetSlots'            => 'Flottenslots',
    'Planets'           => 'Hafen',
    'SpyPower'          => 'Spionagepower',
    'Expedition'            => 'Expeditionen',
    'GateCoolTime'          => 'Sprungstrudelaufladungszeit',
    'MoreFound'         => 'Expeditionsfund',
];

$LNG['tech'] = [
      0 => 'Gebäude',
      1 => 'Sägewerk',
      2 => 'Goldmine',
      3 => 'Brennerei',
      4 => 'Mühle',
      6 => 'Bibliothek',
     12 => 'Metzgerei',
     14 => 'Hafenunterkunft',
     15 => 'Sklavenmarkt',
     21 => 'Schiffwerft',
     22 => 'Holzlager',
     23 => 'Goldspeicher',
     24 => 'Rumlager',
     31 => 'Wissenschaftszentrum',
     33 => 'Pionierstation',
     34 => 'Anlegestelle',
     40 => 'Inselgebäude',
     41 => 'Piratenbasis',
     42 => 'Krähennest',
     43 => 'Sprungstrudel',
     44 => 'Bombenlager',

    100 => 'Forschungen',
    106 => 'Papageiendressur',
    108 => 'Admiralitätswissen',
    109 => 'Kanonentechnik',
    110 => 'Holzbeschaffenheit',
    111 => 'Schiffsbauwesen',
    113 => 'Nahrungsverwertung',
    114 => 'Strömungskunde',
    115 => 'Ruderwerk',
    117 => 'Segelkunde',
    118 => 'Stromlinienforschung',
    120 => 'Brandmunition',
    121 => 'Schießpulvertechnik',
    122 => 'Mörsertechnik',
    123 => 'Inselübergreifendes Wissenschaftsnetzwerk',
    124 => 'Kolonisierungskunde',
    131 => 'Sägewerkzeug',
    132 => 'Spitzhackenschmiede',
    133 => 'Destillationskunde',
    199 => 'Festungsforschung',

    200 => 'Schiffe',
    202 => 'Kleines Handelsschiff',
    203 => 'Großes Handelsschiff',
    204 => 'Leichte Brig',
    205 => 'Schwere Brig',
    206 => 'Schoner',
    207 => 'Korvette',
    208 => 'Pilgerschiff',
    209 => 'Wracktaucher',
    210 => 'Spähpapagei',
    211 => 'Kanonenschiff',
    212 => 'Fischerboot',
    213 => 'Linienschiff',
    214 => 'Schwimmende Festung',
    215 => 'Fregatte',
    216 => 'Lune Noire',
    217 => 'Evolution Transporter',
    218 => 'Avatar',
    219 => 'Gigarecycler',

    400 => 'Verteidigungsanlagen',
    401 => '12-Pfünder',
    402 => 'Balliste',
    403 => 'Feuerballiste',
    404 => '30-Pfünder',
    405 => 'Kartätsche',
    406 => 'Mörser',
    407 => 'Holzwall',
    408 => 'Steinwall',
    409 => 'Gigantische Schildkuppel',
    410 => 'Gravitonkanone',
    411 => 'Orbitale Verteidigungsplattform',

    500 => 'Bomben',
    502 => 'Löschvorrichtung',
    503 => 'Brandbombe',

    900 => 'Rohstoffe',
    901 => 'Holz',
    902 => 'Gold',
    903 => 'Rum',
    911 => 'Nahrung',
];

$LNG['shortDescription'] = [
      1 => 'Hauptrohstofflieferanten für den Bau tragender Strukturen von Bauwerken und Schiffen.',
      2 => 'Hauptrohstofflieferanten für elektronische Bauteile und Legierungen.',
      3 => 'Entziehen dem Wasser eines Planeten den geringen Deuteriumanteil.',
      4 => 'Solarkraftwerke gewinnen Energie aus Sonneneinstrahlung. Einige Gebäude benötigen Energie für ihren'
          . ' Betrieb.',
      6 => 'Sie verkürzt pro Stufe die Forschungszeit um 8%.',
     12 => 'Das Fusionskraftwerk gewinnt Energie aus Brennstäben die aus Deuterium gefertigt werden.',
     14 => 'Roboterfabriken stellen einfache Arbeitskräfte zur Verfügung, die beim Bau der planetaren Infrastruktur'
         . ' eingesetzt werden. Jede Stufe erhöht damit die Geschwindigkeit des Ausbaus von Gebäuden.',
     15 => 'Stellt die Krönung der Robotertechnik dar. Jede Stufe halbiert die Bauzeit von Gebäuden, Schiffen und'
         . ' Verteidigung.',
     21 => 'In der planetaren Werft werden alle Arten von Schiffen und Verteidigungsanlagen gebaut.',
     22 => 'Lagerstätte für unbearbeitete Metallerze bevor sie weiter verarbeitet werden.',
     23 => 'Lagerstätte für unbearbeitetes Kristall bevor es weiter verarbeitet wird.',
     24 => 'Riesige Tanks zur Lagerung des neu gewonnenen Deuteriums.',
     31 => 'Um neue Technologien zu erforschen, ist der Betrieb einer Forschungsstation notwendig.',
     33 => 'Der Terraformer vergrößert die nutzbare Fläche auf Planeten.',
     34 => 'Das Allianzdepot bietet die Möglichkeit, befreundete Flotten, die bei der Verteidigung helfen und im Orbit'
         . ' stehen, mit Treibstoff zu versorgen.',
     41 => 'Ein Mond verfügt über keinerlei Atmosphäre, deshalb muss vor der Besiedlung eine Mondbasis errichtet'
         . ' werden.',
     42 => 'Die Sensorphalanx erlaubt es, Flottenbewegungen zu beobachten. Je höher die Ausbaustufe, desto größer ist'
         . ' die Reichweite der Phalanx.',
     43 => 'Sprungtore sind riesige Transmitter, die in der Lage sind, selbst riesige Flotten ohne Zeitverlust durch'
         . ' das Universum zu versenden.',
     44 => 'Raketensilos dienen zum Einlagern von Raketen.',

    106 => 'Mit Hilfe dieser Technik lassen sich Informationen über andere Planeten und Monde gewinnen.',
    108 => 'Mit der Erhöhung der Computerkapazitäten lassen sich immer mehr Flotten befehligen. Jede Stufe'
        . ' Computertechnik erhöht dabei die maximale Flottenanzahl um eins.',
    109 => 'Waffentechnik macht Waffensysteme effizienter. Jede Stufe der Waffentechnik erhöht die Waffenstärke der'
        . ' Einheiten um 10% des Grundwertes.',
    110 => 'Schildtechnik macht die Schilde der Schiffe und Verteidigungsanlagen effizienter. Jede Stufe der'
        . ' Schildtechnik steigert die Effizienz der Schilde um 10% des Grundwertes.',
    111 => 'Spezielle Legierungen machen die Panzerung der Raumschiffe immer besser. Die Wirksamkeit der Panzerung kann'
        . ' so pro Stufe um 10% gesteigert werden.',
    113 => 'Die Beherrschung der unterschiedlichen Arten von Energie ist für viele neue Technologien notwendig.',
    114 => 'Durch die Einbindung der 4. und 5. Dimension ist es nun möglich einen neuartigen Antrieb zu erforschen,'
        . ' welcher sparsamer und leistungsfähiger ist.',
    115 => 'Die Weiterentwicklung dieser Triebwerke macht einige Schiffe schneller, allerdings steigert jede Stufe die'
        . ' Geschwindigkeit nur um 10% des Grundwertes.',
    117 => 'Das Impulstriebwerk basiert auf dem Rückstoßprinzip. Die Weiterentwicklung dieser Triebwerke macht einige'
        . ' Schiffe schneller und steigert jede Stufe die Geschwindigkeit um 20% des Grundwertes.',
    118 => 'Krümmt den Raum um ein Schiff. Die Weiterentwicklung dieser Triebwerke macht einige Schiffe schneller,'
        . ' allerdings steigert jede Stufe die Geschwindigkeit nur um 30% des Grundwertes.',
    120 => 'Durch Bündelung des Lichtes entsteht ein Strahl der beim Auftreffen auf ein Objekt Schaden anrichtet.',
    121 => 'Wahrhaft tödlicher Richtstrahl aus beschleunigten Ionen. Diese richten beim Auftreffen auf ein Objekt einen'
        . ' riesigen Schaden an.',
    122 => 'Eine Weiterentwicklung der Ionentechnik, die nicht Ionen beschleunigt, sondern hochenergetisches Plasma.'
        . ' Dieses hat eine verheerende Wirkung beim Auftreffen auf ein Objekt.',
    123 => 'Forscher verschiedener Planeten kommunizieren über dieses Netzwerk miteinander. Durch das Zusammenschalten'
        . ' der Labore wird die Forschungszeit verkürzt, jede Stufe schaltet die Labore eines Planeten dazu.',
    124 => 'Weitere Erkenntnisse in der Astrophysik ermöglichen den Bau von Laboren, mit denen immer mehr Schiffe'
        . ' ausgestattet werden können.',
    131 => 'Erhöht die Produktion der Metallmine um 2%',
    132 => 'Erhöht die Produktion der Kristallmine um 2%',
    133 => 'Erhöht die Produktion der Deuteriumsynthetisierer um 2%',
    199 => 'Durch Abschuss einer konzentrierten Ladung von Gravitonpartikeln kann ein künstliches Gravitationsfeld'
        . ' errichtet werden, wodurch Schiffe oder auch Monde vernichtet werden können.',

    202 => 'Der kleine Transporter ist ein wendiges Schiff, welches Rohstoffe schnell zu anderen Planeten'
        . ' transportieren kann.',
    203 => 'Die Weiterentwicklung des kleinen Transporters hat ein größeres Ladevermögen und kann sich dank'
        . ' weiterentwickeltem Antrieb noch schneller fortbewegen als der kleine Transporter.',
    204 => 'Der leichte Jäger ist ein wendiges Schiff, das auf fast jedem Planeten vorgefunden wird. Die Kosten sind'
        . ' nicht besonders hoch, Schildstärke und Ladekapazität sind allerdings sehr gering.',
    205 => 'Die Weiterentwicklung des leichten Jägers ist besser gepanzert und hat eine höhere Angriffsstärke.',
    206 => 'Kreuzer sind fast dreimal so stark gepanzert wie schwere Jäger und verfügen über mehr als die doppelte'
        . ' Schusskraft. Zudem sind sie sehr schnell.',
    207 => 'Schlachtschiffe bilden meist das Rückgrat einer Flotte. Ihre schweren Geschütze, die hohe Geschwindigkeit'
        . ' und der große Frachtraum machen sie zu ernst zu nehmenden Gegnern.',
    208 => 'Fremde Planeten können mit diesem Schiff kolonisiert werden.',
    209 => 'Mit dem Recycler lassen sich Rohstoffe aus Trümmerfeldern gewinnen.',
    210 => 'Spionagesonden sind kleine wendige Drohnen, welche über weite Entfernungen hinweg Daten über Flotten und'
        . ' Planeten liefern.',
    211 => 'Der Bomber wurde extra entwickelt, um die Verteidigung eines Planeten zu zerstören.',
    212 => 'Solarsatelliten sind einfache Plattformen aus Solarzellen, die sich in einem hohen stationären Orbit'
        . ' befinden. Sie sammeln das Sonnenlicht und geben es per Laser an die Bodenstation weiter.',
    213 => 'Der Zerstörer ist der König unter den Kriegsschiffen.',
    214 => 'Die Zerstörungskraft des Todessterns ist unübertroffen und er kann als einziges Schiff Monde zerstören.',
    215 => 'Der Schlachtkreuzer ist auf das Abfangen feindlicher Flotten spezialisiert.',
    216 => 'Der Nachfolger des beliebten Todessterns, etwas schneller und stärker.',
    217 => 'Ist der eine Weiterentwicklung des großen Transporters. Er hat mehr Ladevermögen und fliegt schneller.',
    218 => 'Der Supergau schlechthin, allerdings sehr langsam.',
    219 => 'Ist eine gigantische Weltraumrecycleanlage und hyperschnell.',

    401 => 'Der Raketenwerfer ist eine einfache aber kostengünstige Verteidigungsmöglichkeit.',
    402 => 'Durch den konzentrierten Beschuss eines Ziels mit Photonen kann eine wesentlich größere Schadenswirkung'
        . ' erzielt werden, als mit gewöhnlichen ballistischen Waffen.',
    403 => 'Der schwere Laser stellt die konsequente Weiterentwicklung des leichten Lasers dar.',
    404 => 'Die Gaußkanone beschleunigt tonnenschwere Geschosse unter gigantischem elektrischen Aufwand.',
    405 => 'Das Ionengeschütz schleudert eine Welle von Ionen auf das Ziel, welche Schilde destabilisiert und die'
        . ' Elektronik beschädigt.',
    406 => 'Plasmageschütze setzen die Kraft einer Sonneneruption frei und übertreffen in ihrer zerstörerischen Wirkung'
        . ' sogar den Zerstörer.',
    407 => 'Die kleine Schildkuppel umhüllt den ganzen Planeten mit einem Feld, welches ungeheure Mengen an Energie'
        . ' absorbieren kann.',
    408 => 'Die Weiterentwicklung der Kleinen Schildkuppel kann wesentlich mehr Energie einsetzen um Angriffe'
        . ' abzuhalten.',
    409 => 'Die Weiterentwicklung der Großen Schildkuppel ist die Krönung der Schildtechnik sie kann wesentlich mehr'
        . ' Energie einsetzen um Angriffe abzuhalten als alle anderen Schildkuppeln.',
    410 => 'Nach jahrelangen forschen an der Gravitationskraft ist es Forschern gelungen, eine Gravitonenkanone zu'
        . ' entwickeln, die kleine konzentrierte Gravitationsfelder erzeugen kann und sie auf Gegner schießen lässt.',
    411 => 'Es ist eine unbewegliche defensive Plattform. Sie besitzt keinen direkten Antrieb und wird durch'
        . ' Gravitonforschung in einer stabilen Umlaufbahn des Planeten gehalten. Das starten dieses Vorgangs erfordert'
        . ' hohe Massen an Energie.',

    502 => 'Abfangraketen zerstören angreifende Interplanetarraketen.',
    503 => 'Interplanetarraketen zerstören die gegnerische Verteidigung.',

    901 => 'Hauptrohstoff für den Bau tragender Strukturen von Bauwerken und Schiffen. Metall ist der billigste'
        . ' Rohstoff, dafür wird er mehr benötigt als die anderen. Metall braucht zur Herstellung am wenigsten'
        . ' Energie.',
    902 => 'Kristall wird für die Herstellung feinelektronischer Komponente benötigt, und wird in Minen unter der Erde'
        . ' abgebaut.',
    903 => 'Deuterium ist schwerer Wasserstoff. Für die Gewinnung von Deuterium großen Mengen Energie benötigt.'
        . ' Deuterium wird unter anderem als Treibstoff für Raumschiffe benötigt.',
    911 => 'Energie wird für das gewinnnen jeglicher Rohstoffe benötigt.',
];

$LNG['longDescription'] = [
      1 => 'Hauptrohstofflieferanten für den Bau tragender Strukturen von Bauwerken und Schiffen. Metall ist der'
          . ' billigste Rohstoff, dafür wird er mehr benötigt als die anderen. Metall braucht zur Herstellung am'
          . ' wenigsten Energie. Je größer die Minen ausgebaut sind, desto tiefer sind sie. Bei den meisten Planeten'
          . ' befindet sich das Metall in großer Tiefe, durch diese tieferen Minen können mehr Metalle abgebaut werden,'
          . ' die Produktion steigt. Gleichzeitig muss für die größere Metallmine mehr Energie zur Verfügung gestellt'
          . ' werden.',
      2 => 'Baut Mineralien ab, die für die Feinelektronik benötigt werden. Sie benötigt jedoch mehr Energie, da sie'
          . ' die Mineralien gleich in nötige Legierungen verarbeitet.',
      3 => 'Deuterium ist schwerer Wasserstoff. Daher sind ähnlich wie bei den Minen die größten Vorräte auf dem Grund'
          . ' des Meeres. Der Ausbau des Synthetisierers sorgt ebenfalls für die Erschließung dieser'
          . ' Deuterium-Tiefenlagerstätten. Deuterium wird als Treibstoff für die Schiffe, für fast alle Forschungen,'
          . ' für einen Blick in die Galaxie sowie für den Sensorphalanx-Scan benötigt.',
      4 => 'Um die Energie zur Versorgung der Minen und Synthetisierern zu gewährleisten, sind riesige'
          . ' Solarkraftwerkanlagen vonnöten. Je größer die Anlagen ausgebaut sind, desto mehr Oberfläche ist mit'
          . ' photovoltaischen Zellen bedeckt, welche Lichtenergie in elektrische Energie umwandeln. Solarkraftwerke'
          . ' stellen den Grundstock der planetaren Energieversorgung dar.',
      6 => 'Aufgrund der immer zeitaufwendigeren Forschungen, haben sich die klügsten Köpfe der intergalaktischen'
          . ' Forschungsnetzwerke zusammengetan und den TechnoDome entwickelt. Er verkürzt die Forschungszeiten um 8%',
     12 => 'Im Fusionskraftwerk werden Atome miteinander fusioniert, um so mehr Energie zu erzeugen als in dem'
         . ' Solarkraftwerk, allerdings ist es teurer im Bau.',
     14 => 'Roboterfabriken stellen einfache Arbeitskräfte zur Verfügung, die beim Bau der planetaren Infrastruktur'
         . ' eingesetzt werden können. Jede Stufe erhöht damit die Geschwindigkeit des Ausbaus von Gebäuden.',
     15 => 'Die Nanitenfabrik ist die Krönung der Robotertechnik. Naniten sind nanometergroße Roboter, die durch'
         . ' Vernetzung zu außerordentlichen Leistungen im Stande sind. Einmal erforscht erhöhen sie die Produktivität'
         . ' in fast allen Bereichen. Die Nanitenfabrik halbiert pro Stufe die Bauzeit von Gebäuden, Schiffen und'
         . ' Verteidigungsanlagen.',
     21 => 'In der planetaren Werft werden alle Arten von Schiffen und Verteidigungsanlagen gebaut. Je größer sie ist,'
         . ' desto schneller können aufwendigere und größere Schiffe und Verteidigungsanlagen gebaut werden. Durch'
         . ' Anbau einer Nanitenfabrik werden winzige Roboter erstellt, die den Arbeitern helfen, schneller zu'
         . ' arbeiten.',
     22 => 'Riesige Lagerstätte für abgebautes Metallerz. Je größer der Speicher, desto mehr Metall kann in ihm'
         . ' gelagert werden. Ist das Lager voll, wird kein Metall mehr abgebaut.',
     23 => 'Das noch unbearbeitete Kristall wird in diesen riesigen Lagerhallen zwischengespeichert. Je größer das'
         . ' Lager, desto mehr Kristall kann in ihm eingelagert werden. Sind die Kristalllager voll, wird kein weiteres'
         . ' Kristall abgebaut.',
     24 => 'Riesige Tanks zur Lagerung des neu gewonnenen Deuteriums. Diese Lager findet man meistens in der Nähe von'
         . ' Raumhäfen. Je größer sie sind, desto mehr Deuterium kann in ihnen gelagert werden. Sind sie gefüllt, wird'
         . ' kein Deuterium mehr abgebaut.',
     31 => 'Um neue Technologien zu erforschen, ist der Betrieb einer Forschungsstation notwendig. Die Ausbaustufe'
         . ' einer Forschungsstation ist ausschlaggebend dafür, wie schnell eine neue Technologie erforscht werden'
         . ' kann. Je höher die Ausbaustufe des Labors, umso mehr neue Technologien können erforscht werden. Um die'
         . ' Forschungsarbeiten möglichst schnell zum Abschluss zu bringen, werden, wenn auf einem Planeten geforscht'
         . ' wird, automatisch alle verfügbaren Forscher in diese Forschungsstation geschickt und stehen somit auf'
         . ' anderen Planeten nicht mehr zur Verfügung. Sobald eine Technologie einmal erforscht ist, kehren die'
         . ' Forscher auf ihre Heimatplaneten zurück und bringen das Wissen um sie mit. So kann man die Technologie auf'
         . ' all seinen Planeten einsetzen.',
     33 => 'Mit zunehmendem Ausbau der Planeten, wurde die Frage des begrenzten Lebensraums auf Kolonien immer'
         . ' wichtiger. Traditionelle Methoden wie Hoch- und Tiefbau erwiesen sich zunehmend als unzureichend. Eine'
         . ' kleine Gruppe von Hochenergiephysikern und Nanotechnikern fand schließlich die Lösung: Das Terraforming.'
         . ' Unter Aufwand riesiger Energiemengen kann der Terraformer ganze Landstriche oder gar Kontinente urbar'
         . ' machen. In diesem Gebäude werden fortwährend eigens dafür konstruierte Naniten produziert, die für eine'
         . ' konstante Qualität des Bodens sorgen. Einmal gebaut kann der Terraformer nicht wieder abgerissen werden.',
     34 => 'Das Allianzdepot bietet die Möglichkeit, befreundete Flotten, die bei der Verteidigung helfen und im Orbit'
         . ' stehen, mit Treibstoff zu versorgen. Für jeden Ausbaulevel des Allianzdepots können 10.000 Einheiten'
         . ' Deuterium pro Stunde an die zu versorgenden Flotten im Orbit geschickt werden.',
     41 => 'Ein Mond verfügt über keinerlei Atmosphäre, deshalb muss vor der Besiedlung eine Mondbasis errichtet'
         . ' werden. Diese sorgt für die nötige Atemluft, Gravitation und Wärme. Je höher die Ausbaustufe der Mondbasis'
         . ' ist, umso größer ist die Fläche die mit einer Biosphäre versorgt wird. Pro Mondbasislevel können 3 Felder'
         . ' bebaut werden bis zum Maximum der Mondgröße. Diese beträgt (Durchmesser des Mondes/1000)^2, wobei jede'
         . ' Stufe der Mondbasis selbst auch ein Feld belegt Einmal gebaut kann die Mondbasis nicht wieder abgerissen'
         . ' werden.',
     42 => 'Hochauflösende Sensoren scannen das vollständige Frequenzspektrum aller auf die Phalanx auftreffenden'
         . ' Strahlungen. Hochleistungscomputer kombinieren winzige Energieschwankungen und gewinnen so Informationen'
         . ' über Schiffsbewegungen auf entfernten Planeten. Für den Scan muss Energie in Form von Deuterium (5.000)'
         . ' auf dem Mond bereitgestellt werden. Man scannt, indem man vom Mond aus ins Galaxiemenü wechselt und auf'
         . ' einen feindlichen Planeten in Sensorenreichweite (Phalanxstufe)^2 - 1 klickt.',
     43 => 'Sprungtore sind riesige Transmitter, die in der Lage sind, selbst große Flotten ohne Zeitverlust durch das'
         . ' Universum zu versenden. Diese Transmitter verbrauchen kein Deuterium, jedoch muss zwischen 2 Sprüngen eine'
         . ' Stunde vergehen, da sich die Tore sonst überhitzten. Auch ist ein Mitschicken von Ressourcen nicht'
         . ' möglich. Der ganze Vorgang erfordert eine ungeheuer hoch entwickelte Technologie.',
     44 => 'Raketensilos dienen zum Einlagern von Raketen. Pro ausgebauter Stufe kann man fünf Interplanetar- oder'
         . ' zehn Abfangraketen einlagern. Eine Interplanetarrakete benötigt so viel Platz wie zwei Abfangraketen.'
         . ' Unterschiedliche Raketentypen können beliebig kombiniert werden.',

    106 => 'Die Spionagetechnik befasst sich in erster Linie mit der Erforschung neuer und effizienterer Sensoren.'
        . ' Je höher diese Technik entwickelt ist, um so mehr Informationen stehen dem Nutzer über Vorgänge in seiner'
        . ' Umgebung zur Verfügung. Für Sonden ist die Differenz des eigenen und des gegnerischen Spionagelevels'
        . ' entscheidend. Je weiter die eigene Spionagetechnik erforscht ist, desto mehr Informationen enthält der'
        . ' Bericht und um so kleiner ist die Chance, dass man beim Spionieren entdeckt wird. Je mehr Sonden man'
        . ' schickt, desto mehr Details erfährt man von seinem Gegner, gleichzeitig steigt aber auch die Gefahr einer'
        . ' Entdeckung. Die Spionagetechnik verbessert ebenfalls die Ortung fremder Flotten. Dabei ist nur der eigene'
        . ' Spionagelevel entscheidend. Ab Level 2 wird zusätzlich zur reinen Angriffsmeldung auch die Gesamtanzahl der'
        . ' angreifenden Schiffe angezeigt. Ab Level 4 sieht man die Art der angreifenden Schiffe, sowie die'
        . ' Gesamtanzahl und ab Level 8 die genaue Anzahl der verschiedenen Schiffstypen. Für Raider ist diese Technik'
        . ' unverzichtbar, da sie Auskunft darüber gibt, ob das Opfer Flotte und/oder Verteidigung stationiert hat oder'
        . ' nicht. Deshalb sollte diese Technik schon sehr früh erforscht werden. Am besten sofort nach der Erforschung'
        . ' von kleinen Transportern.',
    108 => 'Die Computertechnik befasst sich mit der Erweiterung der vorhandenen Computerkapazitäten. Immer'
        . ' leistungsfähigere und effizientere Computersysteme werden entwickelt. Die Rechenleistung steigt immer'
        . ' weiter und die Geschwindigkeit, mit denen Rechenprozesse ablaufen, wird ebenfalls erhöht. Mit der Erhöhung'
        . ' der Computerkapazitäten lassen sich immer mehr Flotten gleichzeitig befehligen. Jede Stufe Computertechnik'
        . ' erhöht dabei die maximale Flottenanzahl um eins. Je mehr Flotten man gleichzeitig verschicken kann, desto'
        . ' mehr kann man raiden und desto mehr Rohstoffe kann man einnehmen. Natürlich nutzt diese Technik auch'
        . ' Händlern, denn sie können dann ebenfalls mehr Handelsflotten gleichzeitig losschicken. Aus diesem Grund'
        . ' sollte die Computertechnik kontinuierlich über das gesamte Spiel hinweg ausgebaut werden.',
    109 => 'Die Waffentechnik beschäftigt sich vor allem mit der Weiterentwicklung bestehender Waffensysteme. Dabei'
        . ' wird insbesondere darauf Wert gelegt, die vorhandenen Systeme mit mehr Energie auszustatten und diese'
        . ' Energie punktgenau zu kanalisieren. Dadurch werden die Waffensysteme effizienter und Waffen richten mehr'
        . ' Schaden an. Jede Stufe der Waffentechnik erhöht die Waffenstärke der Einheiten um 10% des Grundwertes. Die'
        . ' Waffentechnik ist wichtig, um später die eigenen Einheiten konkurrenzfähig zu halten. Deshalb sollte sie'
        . ' kontinuierlich das ganze Spiel hindurch entwickelt werden.',
    110 => 'Die Schildtechnik beschäftigt sich mit der Erforschung immer neuer Möglichkeiten, die Schilde mit mehr'
        . ' Energie zu versorgen und sie so effizienter und belastbarer zu machen. Dadurch steigt mit jeder erforschten'
        . ' Stufe die Effizienz der Schilde um 10% des Grundwertes.',
    111 => 'Spezielle Legierungen machen die Panzerung der Raumschiffe immer besser. Ist einmal eine sehr'
        . ' widerstandsfähige Legierung gefunden, wird durch spezielle Strahlungen die molekulare Struktur des'
        . ' Raumschiffes verändert und auf den Stand der besten erforschten Legierung gebracht. Die Wirksamkeit der'
        . ' Panzerung kann so pro Stufe um 10% des Grundwertes gesteigert werden.',
    113 => 'Die Energietechnik beschäftigt sich mit der Weiterentwicklung der Energieleitsysteme und Energiespeicher,'
        . ' welche für viele neue Technologien benötigt wird.',
    114 => 'Durch die Einbindung der 4. und 5. Dimension ist es nun möglich einen neuartigen Antrieb zu erforschen,'
        . ' welcher sparsamer und leistungsfähiger ist.',
    115 => 'Verbrennungstriebwerke basieren auf dem uralten Prinzip des Rückstoßes. Hocherhitzte Materie wird'
        . ' weggeschleudert und treibt das Schiff in die entgegengesetzte Richtung. Der Wirkungsgrad dieser Triebwerke'
        . ' ist eher gering, aber sie sind billig und zuverlässig und benötigen kaum Wartung. Außerdem verbrauchen sie'
        . ' weniger Raum und sind deshalb gerade auf kleineren Schiffen immer wieder zu finden. Da'
        . ' Verbrennungstriebwerke die Grundlage jeder Raumfahrt sind, sollten sie so früh wie möglich erforscht'
        . ' werden. Die Weiterentwicklung dieser Triebwerke macht folgende Schiffe um 10% des Grundwertes pro Stufe'
        . ' schneller: Kleine und große Transporter, Leichte Jäger, Recycler und Spionagesonden.',
    117 => 'Das Impulstriebwerk basiert auf dem Rückstoßprinzip, wobei die Strahlmasse zum Großteil als Abfallprodukt'
        . ' der zur Energiegewinnung verwendeten Kernfusion entsteht. Zusätzlich kann weitere Masse eingespritzt'
        . ' werden. Die Weiterentwicklung dieser Triebwerke macht folgende Schiffe um 20% des Grundwertes pro Stufe'
        . ' schneller: Bomber, Kreuzer, Schwere Jäger und Kolonieschiffe. Interplanetarraketen können pro Stufe weiter'
        . ' fliegen.',
    118 => 'Durch eine Raumzeitverkrümmung wird in unmittelbarer Umgebung eines Schiffes der Raum komprimiert, womit'
        . ' sich weite Strecken sehr schnell zurücklegen lassen. Je höher der Hyperraumantrieb entwickelt ist, desto'
        . ' höher wird die Kompression des Raumes, wodurch sich pro Stufe die Geschwindigkeit der Schiffe die damit'
        . ' ausgestattet sind (Schlachtkreuzer, Schlachtschiffe, Zerstörer und Todessterne) um 30% erhöht.'
        . ' Voraussetzungen: Hyperraumtechnik (Stufe 3) Forschungslabor (Stufe 7).',
    120 => 'Laser (Lichtverstärkung durch induzierte Strahlungsemission) erzeugen einen intensiven, energiereichen'
        . ' Strahl von kohärentem Licht. Diese Geräte finden in allen möglichen Bereichen ihre Bewerbung, von optischen'
        . ' Computern bis hin zu schweren Laserwaffen, die mühelos durch Raumschiffpanzerungen schneiden. Die'
        . ' Lasertechnik bildet einen wichtigen Grundstein für die Erforschung weiterer Waffentechnologien.'
        . ' Voraussetzungen: Forschungslabor (Stufe 1) Energietechnik (Stufe 2).',
    121 => 'Wahrhaft tödlicher Richtstrahl aus beschleunigten Ionen. Die beschleunigten Ionen richten beim Auftreffen'
        . ' auf ein Objekt einen riesigen Schaden an.',
    122 => 'Eine Weiterentwicklung der Ionentechnik, die nicht Ionen beschleunigt, sondern hochenergetisches Plasma.'
        . ' Das hochenergetische Plasma hat eine verheerende Wirkung beim Auftreffen auf ein Objekt.',
    123 => 'Forscher verschiedener Planeten kommunizieren über dieses Netzwerk miteinander. Pro erforschtes Level,'
        . ' wird ein Forschungslabor vernetzt. Dabei werden immer die Labors der höchsten Stufe dazugeschaltet. Das'
        . ' vernetzte Labor muss ausreichend ausgebaut sein um die anstehende Forschung selbständig durchführen zu'
        . ' können. Die Ausbaustufen aller beteiligten Labors werden im intergalaktischen Forschungsnetzwerk zusammen'
        . ' gezählt.',
    124 => 'Weitere Erkenntnisse in der Astrophysik ermöglichen den Bau von Laboren, mit denen immer mehr Schiffe'
        . ' ausgestattet werden können. Dadurch werden weite Expeditionsreisen in noch unerforschte Gebiete möglich.'
        . ' Zudem erlauben die Fortschritte die weitere Kolonisation des Weltraumes. Pro zwei Stufen dieser Technologie'
        . ' kann so ein weiterer Planet nutzbar gemacht werden.',
    131 => 'Erhöht die Produktion der Metallmine um 2%',
    132 => 'Erhöht die Produktion der Kristallmine um 2%',
    133 => 'Erhöht die Produktion der Deuteriumsynthetisierer um 2%',
    199 => 'Ein Graviton ist ein Partikel, das keine Masse und keine Ladung besitzt, welche die Gravitationskraft'
        . ' bestimmt. Durch Abschuss einer konzentrierten Ladung von Gravitonen kann ein künstliches Gravitationsfeld'
        . ' errichtet werden, welches ähnlich einem schwarzen Loch, Masse in sich hineinzieht, wodurch Schiffe oder'
        . ' auch Monde vernichtet werden können. Um eine ausreichende Menge Gravitonen herzustellen benötigt es riesige'
        . ' Mengen an Energie. Voraussetzungen: Forschungslabor (Stufe 12).',

    202 => 'Transporter haben ungefähr die gleiche Größe wie Jäger, verzichten aber auf leistungsfähige Antriebe und'
        . ' Bordwaffen, um Platz für Frachtraum zu schaffen. Der kleine Transporter verfügt über eine Ladekapazität'
        . ' von 5.000 Ressourceneinheiten. Aufgrund ihrer geringen Feuerkraft werden Transporter oft von anderen'
        . ' Schiffen eskortiert.',
    203 => 'Dieses Schiff hat kaum Waffen oder andere Technologien an Bord. Aus diesem Grunde sollten sie nie alleine'
        . ' losgeschickt werden. Der große Transporter dient durch sein hochentwickeltes Verbrennungstriebwerk als'
        . ' schneller Ressourcenlieferant zwischen den Planeten und natürlich begleitet er die Flotten auf ihren'
        . ' Überfällen feindlicher Planeten, um möglichst viele Ressourcen zu erobern, der große Transporter verfügt'
        . ' über eine Ladekapazität von 25.000 Ressourceneinheiten.',
    204 => 'Der leichte Jäger ist ein wendiges Schiff, das auf fast jedem Planeten vorgefunden wird. Die Kosten sind'
        . ' nicht besonders hoch, Schildstärke und Ladekapazität sind allerdings sehr gering.',
    205 => 'Bei der Weiterentwicklung des leichten Jägers kamen die Forscher zu einem Punkt, bei welchem der'
        . ' konventionelle Antrieb nicht mehr ausreichend Leistungen erbrachte. Um das neue Schiff optimal fortbewegen'
        . ' zu können wurde zum ersten Mal der Impulsantrieb genutzt. Dieses erhöhte zwar die Kosten, eröffnete aber'
        . ' auch neue Möglichkeiten. Durch die Einsetzung dieses Antriebes blieb mehr Energie für Waffen und Schilde'
        . ' übrig, ausserdem wurden für diese neue Jägergattung auch qualitativ hochwertigere Materialien verwendet.'
        . ' Dies führte zu einer verbesserten strukturellen Integrität und einer höheren Feuerkraft, was ihn im Kampf'
        . ' zu einer immens größeren Bedrohung macht als sein leichtes Pendant. Durch diese Änderungen stellt der'
        . ' schwere Jäger eine neue Ära der Schiffstechnologie dar, welche die Grundlage für die Kreuzertechnologie'
        . ' ist.',
    206 => 'Mit der Entwicklung der schweren Laser und der Ionenkanonen kamen die Jäger immer mehr in Bedrängnis. Trotz'
        . ' vieler Modifikationen konnte die Waffenstärke und die Panzerung nicht so weit gesteigert werden, um diesen'
        . ' Verteidigungsgeschützen wirksam begegnen zu können. Deshalb entschied man sich, eine neue Schiffsklasse zu'
        . ' konstruieren, die mehr Panzerung und mehr Feuerkraft in sich vereinte. Der Kreuzer war geboren. Kreuzer'
        . ' sind fast dreimal so stark gepanzert wie schwere Jäger und verfügen über mehr als die doppelte Schusskraft.'
        . ' Zudem sind sie sehr schnell. Gegen mittlere Verteidigung gibt es keine bessere Waffe. Kreuzer beherrschten'
        . ' fast ein Jahrhundert lang unumschränkt das All. Mit dem Aufkommen der Gaußgeschütze und Plasmawerfer endete'
        . ' ihre Vorherrschaft. Jedoch werden sie auch heute noch gerne gegen Jägerverbände eingesetzt.',
    207 => 'Schlachtschiffe bilden meist das Rückrat einer Flotte. Ihre schweren Geschütze, die hohe Geschwindigkeit'
        . ' und der große Frachtraum, machen sie zu ernst zu nehmenden Gegnern.',
    208 => 'Dieses gut gepanzerte Schiff dient der Eroberung neuer Planeten, was für ein aufstrebendes Imperium'
        . ' unerlässlich ist. Das Schiff wird auf der neuen Kolonie als Rohstofflieferant genutzt, in dem es wieder'
        . ' auseinander gebaut wird und alles wiederverwertbare Material für die Erschliessung der neuen Welt genutzt'
        . ' wird. Die Anzahl der kolonisierbaren Planeten je Imperium ist direkt abhängig von der Astrophysik-Forschung.'
        . ' Die Erste, sowie jede weitere zweite Stufe ermöglichen eine weitere Besiedelung.',
    209 => 'Die Weltraumgefechte nahmen immer größere Ausmaße an. Tausende Schiffe wurden zerstört, aber die dadurch'
        . ' entstehenden Trümmerfelder schienen für immer verloren. Normale Transporter konnten sich nicht nahe genug'
        . ' an diese Felder heran bewegen, ohne durch kleinere Trümmer riesigen Schaden zu nehmen. Mit einer neuen'
        . ' Entwicklung im Bereich der Schildtechnologie konnte dieses Problem effizient beseitigt werden. Es entstand'
        . ' eine neue Schiffsklasse, ähnlich dem großen Transporter, der Recycler. Mit dessen Hilfe konnten die'
        . ' scheinbar verlorenen Ressourcen doch noch verwertet werden. Die kleinen Trümmer stellten aufgrund der neuen'
        . ' Schilde auch keine Gefahr mehr dar. Durch spezielle mehrdimensionale Ladefelder konnte seine Ladekapazität'
        . ' auf 20.000 erweitert werden. Ab Impulsantrieb Stufe 17 bzw. Hyperraumantrieb Stufe 15 wird der schnellere'
        . ' und kostengünstigere Antrieb genutzt.',
    210 => 'Spionagesonden sind kleine wendige Drohnen, welche über weite Entfernungen hinweg Daten über Flotten und'
        . ' Planeten liefern. Ihr Hochleistungstriebwerk ermöglicht ihnen weite Strecken in wenigen Sekunden zurück zu'
        . ' legen. Einmal in der Umlaufbahn eines Planeten angekommen verweilen sie dort kurz um Daten zu sammeln.'
        . ' Während dieser Zeit sind sie vom Feind relativ leicht entdeck- und angreifbar. Um Platz zu sparen wurde auf'
        . ' Panzerung, Schilde und Waffen verzichtet, was die Sonden, wenn sie einmal entdeckt wurden, zu leichten'
        . ' Zielen macht.',
    211 => 'Der Bomber wurde speziell entwickelt um die Verteidigung eines Planeten zu zerstören. Mit Hilfe einer'
        . ' lasergesteuerten Zielvorrichtung wirft er zielgenau Plasmabomben auf die Planetenoberfläche und richtet so'
        . ' einen verheerenden Schaden bei Verteidigungsanlagen an.',
    212 => 'Solarsatelliten werden in eine geostationäre Umlaufbahn um einen Planeten geschossen. Sie bündeln'
        . ' Sonnenenergie und transferieren sie zu einer Bodenstation. Die Effizienz der Solarsatelliten hängt von der'
        . ' Stärke der Sonneneinstrahlung ab. Grundsätzlich ist die Energieausbeute auf sonnennahen Orbits größer als'
        . ' auf Planeten mit sonnenfernen Orbit. Durch ihr gutes Preis-Leistungs-Verhältnis lösen Solarsatelliten die'
        . ' Energieprobleme vieler Welten. Aber Vorsicht: Solarsatelliten können im Kampf zerstört werden.',
    213 => 'Der Zerstörer ist der König unter den Kriegsschiffen. Seine Multiphalanx Ionen-, Plasma- und'
        . ' Gaußgeschütztürme können durch ihre verbesserten Anpeilungssensoren fast 99% der verteidigenden leichten'
        . ' Laser treffen. Da der Zerstörer sehr groß ist, ist seine Manövrierfähigkeit stark eingeschränkt, wodurch er'
        . ' im Kampf eher einer Kampfstation gleicht, als einem Kampfschiff. So hoch wie seine Kampfkraft ist auch sein'
        . ' Verbrauch an Deuterium.',
    214 => 'Der Todesstern ist mit einer riesigen Gravitonkanone bewaffnet, die Schiffe so groß wie Zerstörer oder'
        . ' sogar Monde zerstören kann. Da dafür eine hohe Menge an Energie benötigt wird, besteht er fast nur aus'
        . ' Generatoren. Lediglich riesige Sternenreiche können überhaupt die Ressourcen und Arbeiter aufbringen, um'
        . ' dieses mondgroße Schiff zu Bauen.',
    215 => 'Dieses filigrane Schiff eignet sich hervorragend zum Zerstören feindlicher Flottenverbände. Mit seinen'
        . ' hochentwickelten Lasergeschützen ist es in der Lage, eine große Zahl angreifender Schiffe gleichzeitig zu'
        . ' bekämpfen. Durch seine schlanke Bauform und die starken Bewaffnung ist die Ladekapazität begrenzt. Dies'
        . ' wird jedoch durch den verbrauchsarmen Hyperraumantrieb wieder ausgeglichen.',
    216 => 'Dieses monströse Schiff ist eine Weiterentwicklung des Todessternes, die an Geschwindigkeit zugenommen hat,'
        . ' doch an Stärke verloren.	',
    217 => 'Dieser Transporter ist zwar langsamer aber dafür kann er jetzt mehr aufladen. Doch wenn man die richtige'
        . ' Forschung hat ist er fast so schnell wie der große Transporter.',
    218 => 'Dieses Schiff ist eine Verbesserung mehrerer Schiffe gleichzeitig und der Kaiser der Kampfsterne.',
    219 => 'Dieses Schiff ist eine wahrhaft gigantische Recycelanlage im Weltraum mit Atemberaubender Geschwindigkeit'
        . ' und riesigem Lagerraum! Geschaffen für große Imperien.',

    401 => 'Der Raketenwerfer ist eine einfache aber kostengünstige Verteidigungsmöglichkeit. Da er nur eine'
        . ' Weiterentwicklung gewöhnlicher ballistischer Feuerwaffen ist, benötigt er keine weitere Forschung. Seine'
        . ' geringen Herstellungskosten rechtfertigen seinen Einsatz gegen kleinere Flotten, er verliert aber mit der'
        . ' Zeit an Bedeutung. Später wird er nur noch als Schussfang hinter großen Geschützen eingesetzt.'
        . ' Verteidigungsanlagen deaktivieren sich, sobald sie zu stark beschädigt sind. Nach einer Schlacht beträgt'
        . ' die Chance bis zu 70%, dass sich ausgefallene Verteidigungsanlagen wieder Instand setzen lassen.',
    402 => 'Um die enormen Fortschritte im Bereich der Raumschifftechnologie kompensieren zu können, mussten die'
        . ' Forscher eine Verteidigungsanlage entwickeln, welche auch mit größeren und besser ausgerüsteten Schiffen'
        . ' bzw. Flotten zurechtkommt. Dies war die Geburtsstunde des leichten Lasers. Durch den konzentrierten'
        . ' Beschuss eines Ziels mit Photonen konnte eine wesentlich größere Schadenswirkung erzielt werden als mit'
        . ' gewöhnlichen ballistischen Waffen. Um der größeren Feuerkraft der neuen Schiffstypen widerstehen zu können'
        . ' wurde er ausserdem mit verbesserten Schilden ausgestattet. Damit die Produktionskosten dennoch gering'
        . ' gehalten werden konnten wurde die Struktur nicht weiter verstärkt. Der leichte Laser besitzt das beste'
        . ' Preis-Leistungs-Verhältnis überhaupt und ist dadurch auch für weiter fortgeschrittene Zivilisationen'
        . ' interessant. Verteidigungsanlagen deaktivieren sich, sobald sie zu stark beschädigt sind. Nach einer'
        . ' Schlacht beträgt die Chance bis zu 70%, dass sich die zerstörten Verteidigungsanlagen wieder Instand'
        . ' setzen lassen.',
    403 => 'Der schwere Laser stellt die konsequente Weiterentwicklung des Designs des leichten Lasers dar. Die'
        . ' Struktur wurde verstärkt und mit neuen Materialien verbessert. Die Hülle konnte so wesentlich'
        . ' widerstandsfähiger gemacht werden. Gleichzeitig wurden auch das Energiesystem und der Zielcomputer'
        . ' verbessert, so dass ein schwerer Laser wesentlich mehr Energie auf ein Ziel konzentrieren kann.'
        . ' Verteidigungsanlagen deaktivieren sich, sobald sie zu stark beschädigt sind. Nach einer Schlacht beträgt'
        . ' die Chance bis zu 70%, dass sich ausgefallene Verteidigungsanlagen wieder Instand setzen lassen.',
    404 => 'Projektilwaffen galten lange Zeit neben der moderneren Kernfusions- und Energietechnik, der Entwicklung des'
        . ' Hyperraumantriebs und immer besserer Panzerungen als antiquiert, bis eben genau die Energietechnik, die sie'
        . ' einst verdrängt hatte, ihr wieder zu ihrem angestammten Platz verhalf. Das Prinzip war eigentlich schon aus'
        . ' dem 20. und 21. Jahrhundert der Erde bekannt - der Teilchenbeschleuniger. Eine Gaußkanone ist eigentlich'
        . ' nichts anderes als eine erheblich größere Version dieser Konstruktion. Tonnenschwere Geschosse werden unter'
        . ' gigantischem elektrischem Aufwand magnetisch beschleunigt und haben Mündungsgeschwindigkeiten, die die'
        . ' Schmutzteilchen in der Luft um das Geschoss verbrennen lassen und der Rückstoß bringt die Erde zum Beben.'
        . ' Dieser Durchschlagskraft können auch aktuelle Panzerungen und Schilde nur schwer widerstehen, und es kommt'
        . ' nicht selten vor, dass das Ziel einfach durchschlagen wird. Verteidigungsanlagen deaktivieren sich, sobald'
        . ' sie zu stark beschädigt sind. Nach einer Schlacht beträgt die Chance bis zu 70%, dass sich ausgefallene'
        . ' Verteidigungsanlagen wieder Instand setzen lassen.',
    405 => 'Im 21. Jahrhundert der Erde gab es etwas, das allgemein als EMP bekannt war. EMP steht für'
        . ' Elektromagnetischer Puls, der die Eigenschaft hat, in alle Schaltkreise zusätzliche Spannungen zu'
        . ' induzieren und somit massenhafte Störungen zu verursachen, die alle empfindlichen Geräte zerstören können.'
        . ' Damals waren EMP-Waffen meistens noch auf Raketen- und Bombenbasis, auch in Verbindung mit Nuklearwaffen.'
        . ' Mittlerweile wurde der EMP ständig weiterentwickelt, da man in ihm ein großes Potential sah, Ziele nicht zu'
        . ' zerstören, aber kampf- und manövrierunfähig zu machen, so dass einer Übernahme nichts mehr im Wege stand.'
        . ' Die bisher höchste Form einer EMP-Waffe stellt das Ionengeschütz dar. Es schleudert eine Welle von Ionen'
        . ' (elektrisch geladene Teilchen) auf das Ziel, welche die Schilde destabilisiert und alle Elektronik - sofern'
        . ' diese nicht massiv abgeschirmt ist - beschädigt, was nicht selten einer völligen Zerstörung gleichkommt.'
        . ' Die kinetische Durchschlagskraft kann vernachlässigt werden. Die Ionentechnik wird auch auf Kreuzern'
        . ' eingesetzt, jedoch auf keinem anderen Schiffstyp, da der Energieverbrauch der Ionengeschütze enorm ist und'
        . ' es in einem Gefecht häufig darauf ankommt, das Ziel zu zerstören und nicht nur zu paralysieren.'
        . ' Verteidigungsanlagen deaktivieren sich, sobald sie zu stark beschädigt sind. Nach einer Schlacht beträgt'
        . ' die Chance bis zu 70%, dass sich ausgefallene Verteidigungsanlagen wieder Instand setzen lassen.',
    406 => 'Die Lasertechnik war mittlerweile nahezu perfektioniert, die Ionentechnik hatte ein Endstadium erreicht und'
        . ' es galt mittlerweile als praktisch unmöglich, auch aus nur einem Waffensystem qualitativ gesehen noch mehr'
        . ' Effektivität herauszubekommen. Doch all dies sollte sich ändern, als man auf die Idee kam, beide Systeme'
        . ' miteinander zu kombinieren. Schon aus der Kernfusionstechnik bekannt, erhitzen Laser Teilchen ( meistens'
        . ' Deuterium ) auf extrem hohe Temperaturen, die schon einmal in die Millionen Grad gehen. Die Ionentechnik'
        . ' trägt ihren Teil in Form von elektrischer Aufladung, Stabilisierungsfeldern und Beschleunigern bei. Wird'
        . ' die abzufeuernde Ladung genügend erhitzt, unter Druck gesetzt und ionisiert, jagt man sie mittels'
        . ' Beschleunigern in die Weiten des Alls Richtung Ziel hinaus. Der grünlich glühende Plasmastrahl bietet einen'
        . ' imposanten Anblick, es fragt sich aber, ob die Crew des Zielschiffes lange an ihm Gefallen haben wird, wenn'
        . ' in wenigen Sekunden die Hülle zerfetzt und die Elektronik geschmort wird... Der Plasmawerfer gilt als eine'
        . ' der gefürchtetsten Waffen überhaupt, und diese Technik hat auch ihren Preis. Verteidigungsanlagen'
        . ' deaktivieren sich, sobald sie zu stark beschädigt sind. Nach einer Schlacht beträgt die Chance bis zu 70%,'
        . ' dass sich ausgefallene Verteidigungsanlagen wieder Instand setzen lassen.',
    407 => 'Lange bevor die Schildgeneratoren klein genug waren, um auf Schiffen Einsatz zu finden, existierten bereits'
        . ' riesige Generatoren auf der Oberfläche von Planeten. Diese umhüllen den ganzen Planeten mit einem'
        . ' Kraftfeld, welches ungeheure Mengen an Energie absorbieren kann, bevor es zusammenbricht. Kleinere'
        . ' Angriffsflotten scheitern immer wieder an diesen Schildkuppeln. Mit zunehmender technologischer Entwicklung'
        . ' können diese Schilde noch verstärkt werden. Später kann man sogar eine große Schildkuppel bauen, die noch'
        . ' stärker ist. Pro Planet kann nur eine einzige kleine Schildkuppel gebaut werden.',
    408 => 'Die Weiterentwicklung der kleinen Schildkuppel. Sie basiert auf den gleichen Technologien kann aber'
        . ' wesentlich mehr Energie einsetzen um feindliche Angriffe abzuhalten.',
    409 => 'Die Weiterentwicklung der Großen Schildkuppel. Sie basiert auf den gleichen Technologien kann aber'
        . ' wesentlich mehr Energie einsetzen um feindliche Angriffe abzuhalten.',
    410 => 'Sie basiert, wie der Name schon sagt, auf einer Gravitonkraft, bekannt aus dem Todesstern und aus besseren'
        . ' Schiffen.',
    411 => 'Diese Plattform, mit gigantischem Ausmaß, ist das Größte was das Universum je gesehen hat. Es ist eine'
        . ' unbewegliche defensive Plattform. Sie besitzt keinen direkten Antrieb und wird durch Gravitonforschung in'
        . ' einer stabilen Umlaufbahn des Planeten gehalten. Das Starten dieses Vorgangs erfordert hohe Massen an'
        . ' Energie. Die Forscher arbeiten an einer Möglichkeit, auf dieser Plattform Schiffe zu bauen, um sie als'
        . ' einen äusseren Verteidigungsring zu nutzen, der es dem Gegner erschwert zur Planetaren Verteidigung'
        . ' durchzubrechen. Durch das gigantische Ausmaß ist es nur möglich einer dieser Monster zu besitzen.',
    502 => 'Abfangraketen zerstören angreifende Interplanetarraketen. Jede Boden-Luft-Rakete zerstört eine'
        . ' Interplanetarrakete.',
    503 => 'Interplanetarraketen zerstören die gegnerische Verteidigung, können allerdings durch Abfangraketen zerstört'
        . ' werden! Von Interplanetarraketen zerstörte Verteidigungsanlagen bauen sich nicht wieder auf.',

    901 => 'Hauptrohstoff für den Bau tragender Strukturen von Bauwerken und Schiffen. Metall ist der billigste'
        . ' Rohstoff, dafür wird er mehr benötigt als die anderen. Metall braucht zur Herstellung am wenigsten Energie.'
        . ' Je größer die Minen ausgebaut sind, desto tiefer sind sie. Bei den meisten Planeten befindet sich das'
        . ' Metall in großer Tiefe, durch diese tieferen Minen können mehr Metalle abgebaut werden, die Produktion'
        . ' steigt. Trotz seiner Häufigkeit ist Metall eine der gefragtesten Ressourcen im Universum.',
    902 => 'Kristall wird für die Herstellung feinelektronischer Komponente benötigt, und wird in Minen unter der Erde'
        . ' abgebaut.',
    903 => 'Deuterium ist schwerer Wasserstoff. Die größten Vorräte sind am Grund des Meeres. Deshalb werden für die'
        . ' Gewinnung von Deuterium großen Mengen Energie benötigt. Deuterium wird als Treibstoff für Raumschiffe, für'
        . ' fast alle Forschungen, für einen Blick in die Galaxie sowie für den Sensorphalanx-Scan benötigt.',
    911 => 'Energie wird für das gewinnnen jeglicher Rohstoffe benötigt. Man sagt, man könne mit unmengen an Energie,'
        . ' die Gravitation beeinflussen und dadurch zerstörerische Waffen bauen. Allerdings ist dies bis heute noch'
        . ' kaum jemanden gelungen.',
];
