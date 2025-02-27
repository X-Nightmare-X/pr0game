<?php

// Traduction Française by BigTwoProduction (KickXAss4Ever & Apocalypto2202) - All rights reserved (C) 2016
// Web : http://www.big-two.tk
// Version 1.0 - Initial release
// Version 1.1 - Decode accent HTML to UTF-8 format & small spellchecking

//SHORT NAMES FOR COMBAT REPORTS
$LNG['shortNames'] = [
202 => 'Petit transporteur',
203 => 'Grand transporteur',
204 => 'Chasseur léger',
205 => 'Chasseur lourd',
206 => 'Croiseur',
207 => 'Vaisseau de bataille',
208 => 'Vaisseau de colonisation',
209 => 'Recycleur',
210 => 'Sonde d\'espionnage',
211 => 'Bombardier',
212 => 'Satellite solaire',
213 => 'Destructeur',
214 => 'Étoile de la mort',
215 => 'Traqueur',
216 => 'Lune Noire',
217 => 'Transporteur ultime',
218 => 'Avatar',
219 => 'Recycleur ultime',

401 => 'Lanceur de missiles',
402 => 'Canon laser léger',
403 => 'Canon laser lourd',
404 => 'Canon de Gauss',
405 => 'Artillerie à Ions',
406 => 'Lanceur de Plasma',
407 => 'Petit Bouclier',
408 => 'Grand Bouclier',
409 => 'Dôme de Protection',
410 => 'Canon à Gravitons',
411 => 'Plateforme Orbitale',
];

$LNG['bonus'] = [
    'Attack'            => 'Attaque',
    'Defensive'         => 'Défense',
    'Shield'            => 'Bouclier',
    'BuildTime'         => 'Temps de construction',
    'ResearchTime'      => 'Temps de recherche',
    'ShipTime'          => 'Temps d\'achat vaisseaux',
    'DefensiveTime'     => 'Temps d\'achat défense',
    'Resource'          => 'Production de ressources',
    'Energy'            => 'Production d\'énergie',
    'ResourceStorage'   => 'Stockage de ressources',
    'ShipStorage'       => 'Stockage de vaisseaux',
    'FlyTime'           => 'Temps de vol',
    'FleetSlots'        => 'Slot vaisseaux',
    'Planets'           => 'Planéte',
    'SpyPower'          => 'Niveaux d\'espionnage',
    'Expedition'        => 'Expéditions',
    'GateCoolTime'      => 'Temps de chargement du portail de saut',
    'MoreFound'         => 'Expédition trouvée',
];

$LNG['tech'] = [
0 => 'Bâtiments',
1 => 'Mine de Métal',
2 => 'Mine de Cristal',
3 => 'Synthétiseur de Deutérium',
4 => 'Centrale éléctrique Solaire',
6 => 'Université',
12 => 'Centrale de fusion',
14 => 'Usine de Robots',
15 => 'Usine de Nanites',
21 => 'Chantier Spatial',
22 => 'Hangar de Métal',
23 => 'Hangar de Cristal',
24 => 'Réservoir de Deutérium',
31 => 'Laboratoire de Recherche',
33 => 'Terraformeur',
34 => 'Dépôt d\'Alliance',
35 => 'Dock de réparation',
40 => 'Bâtiments lunaires',
41 => 'Base Lunaire',
42 => 'Phalange de capteurs',
43 => 'Porte de Saut',
44 => 'Silo de Missiles',
// Technologies
100 => 'Recherche',
106 => 'Technologie d\'Espionnage',
108 => 'Technologie Ordinateur',
109 => 'Technologie Armes',
110 => 'Technologie Bouclier',
111 => 'Technologie Blindage',
113 => 'Technologie Énergie',
114 => 'Technologie Hyperespace',
115 => 'Réacteur à Combustion',
117 => 'Réacteur à Impulsion',
118 => 'Propulsion Hyperespace',
120 => 'Technologie Laser',
121 => 'Technologie Ion',
122 => 'Technologie Plasma',
123 => 'Réseau de Recherche Intergalactique',
124 => 'Astrophysique',
131 => 'Optimisation de la production en Métal',
132 => 'Optimisation de la production en Cristal',
133 => 'Optimisation de la production en Deutérium',
199 => 'Technologie Graviton',

200 => 'Vaisseaux',
202 => 'Petit transporteur',
203 => 'Grand transporteur',
204 => 'Chasseur léger',
205 => 'Chasseur lourd',
206 => 'Croiseur',
207 => 'Vaisseau de bataille',
208 => 'Vaisseau de colonisation',
209 => 'Recycleur',
210 => 'Sonde d\'espionnage',
211 => 'Bombardier',
212 => 'Satellite solaire',
213 => 'Destructeur',
214 => 'Étoile de la mort',
215 => 'Traqueur',
216 => 'Lune Noire',
217 => 'Transporteur ultime',
218 => 'Avatar',
219 => 'Recycleur ultime',

400 => 'Systèmes de Défense',
401 => 'Lanceur de missiles',
402 => 'Canon laser léger',
403 => 'Canon laser puissant',
404 => 'Canon de Gauss',
405 => 'Artillerie à Ions',
406 => 'Lanceur de Plasma',
407 => 'Petit Bouclier',
408 => 'Grand Bouclier',
409 => 'Dôme de Protection',
410 => 'Canon à Gravitons',
411 => 'Plateforme Orbitale',

500 => 'Missiles',
502 => 'Missiles d\'Interception',
503 => 'Missiles Interplanétaires',

900 => 'Ressources',
901 => 'Métal',
902 => 'Crystal',
903 => 'Deuterium',
911 => 'Énergie',
];

$LNG['shortDescription'] = [
1 => 'Principal fournisseur de matières premières pour la construction des structures porteuses des bâtiments et des'
    . ' vaisseaux.',
2 => 'Principal fournisseur de matières premières pour les composants électroniques et les alliages.',
3 => 'Extrait la petite quantité de deutérium de l\'aportium d\'une planète..',
4 => 'Les centrales électriques solaires transforment les rayons des étoiles en énergie. Presque tous les bâtiments ont'
    . ' besoin d\'énergie pour fonctionner.',
6 => 'Chaque niveau réduit le temps de recherche de 8%.',
12 => 'La centrale de fusion produit de l\'énergie à partir de barres de combustible en deutérium.',
14 => 'L\usine de robots produit des travailleurs automatisés qualifiés, qui sont utilisés dans la construction de'
    . ' l\'infrastructure planétaire. Chaque niveau augmente la vitesse de l\'expansion des bâtiments.',
15 => 'La taille microscopique des nanomachines se traduit par une vitesse de fonctionnement plus élevée. Cette usine'
    . ' produit des nanomachines qui sont l\'ultime évolution de la technologie robotique. Une fois construit, chaque'
    . ' niveau diminue de manière significative le temps de production pour les bâtiments, des vaisseaux et des'
    . ' structures défensives.',
21 => 'Le chantier spatial permet la construction de vaisseaux spatiaux et de défenses. Plus le niveau de cette'
    . ' structure augmente, plus elle peut produire une grande variété de vaisseaux et défenses à un rythme beaucoup'
    . ' plus élevé. Si une usine de nanites est présente sur la planète, la vitesse à laquelle les vaisseaux et'
    . ' défenses sont construits est décuplé.',
22 => 'Immense hangar de stockage de minerais métalliques brutes avant traitement.',
23 => 'Immense hangar de stockage de cristal brut avant traitement.',
24 => 'Énormes réservoirs pour le stockage du deutérium nouvellement acquis.',
31 => 'Le laboratoire de recherche est nécessaire pour développer de nouvelles technologies.',
33 => 'Le terraformeur augmente la surface utilisable de la planète.',
34 => 'Le dépôt d\'alliance offre la possibilité de fournir du carburant aux flottes amicales en orbite.',
35 => 'Le dock de réparation permet de restaurer des navires à partir d\'un champ d\'épaves.',
41 => 'Une lune n\'a pas d\'atmosphère donc une base lunaire doit être construite avant le développement des autres'
    . ' bâtiments.',
42 => 'Le réseau de capteurs permet de surveiller les mouvements de la flotte. Plus le stade est élevé, plus la gamme'
    . ' de la phalange.',
43 => 'Les portes de sauts sont des émetteurs géants qui sont en mesure d\'envoyer de grandes flottes, sans perte de'
    . ' temps, d\'une lune à une autre à travers l\univers.',
44 => 'Silos de missiles utilisés pour le stockage des missiles.',

106 => 'En utilisant cette technologie, des informations sont acquises sur d\'autres planètes ou lunes.',
108 => 'Avec l\'augmentation de la capacité des ordinateur vous pouvez commander de plus en plus de flottes. Chaque'
    . ' niveau de technologie informatique augmentate le nombre maximum de flotte par un.',
109 => 'La technologie armes rend plus efficaces les systèmes d\'armements. Chaque niveau de technologie armes augmente'
    . ' la résistance des vaisseaux et des unités de défense de 10% de sa valeur de base.',
110 => 'la technologie bouclier augmente l\'efficacité des boucliers des vaisseaux et des défenses. Chaque niveau de'
    . ' technologie bouclier l\'augmente de 10% de sa valeur de base.',
111 => 'Des alliages spéciaux confèrent aux vaisseaux un blindage de plus en plus efficace. L\'efficacité du blindage'
    . ' peut donc être augmentée de 10% par niveau.',
113 => 'Le contrôle des différents types d\'énergie est indispensable pour de nombreuses nouvelles technologies.',
114 => 'En intégrant les dimensions 4 et 5, il est désormais possible, grâce à cette technologie, de se déplacer de'
    . ' façon plus économique et efficace.',
115 => 'Le développement de ces moteurs permet aux vaisseaux d\'être plus rapides, mais chaque niveau n\'augmente leur'
    . ' vitesse que de 10% de leur valeur de base.',
117 => 'Le réacteur à impulsion est essentiellement une fusion augmentée, habituellement constitué d\'un réacteur de'
    . ' fusion, un accélérateur-générateur, un ensemble de bobines conductrices et une buse d\'échappement à poussée'
    . ' vectorielle directe du plasma. Le développement de ces moteurs permet aux vaisseaux d\'être plus rapides, mais'
    . ' chaque niveau n\'augmente leur vitesse que de 20% de leur valeur de base.',
118 => 'Le développement de ces moteurs permet aux vaisseaux d\'être plus rapides, mais chaque niveau n\'augmente leur'
    . ' vitesse que de 30% de leur valeur de base.',
120 => 'En combinant les rayons de lumière concentrée, Le faisceau créé cause des dommages lors de la frappe d\'un'
    . ' objet.',
121 => 'Faisceau d\'ions accélérés, lorsqu\'il frappe un objet, il cause des dommages énormes.',
122 => 'C\'est le prolongement de la technologie ions. Il ne s\'agit pas ici que d\'accélerer des ions, mais du plasma'
    . ' à haute énergie. Cela a un effet dévastateur.',
123 => 'Pour évoluer, chaque colonie doit être en mesure de procéder à la recherche de façon autonome. Avec le RRI, les'
    . ' temps de recherche sont plus rapides en associant le plus grand des laboratoires de recherche à un niveau égal'
    . ' au niveau du RRI développé.',
124 => 'D\'autres découvertes dans l\'astrophysique permettent la construction de laboratoires, dont plusieurs'
    . ' vaisseaux peuvent être équipés. De longues expéditions sont possibles dans des les espaces inexplorés. En'
    . ' outre, les progrès permettent la colonisation d\'autres planètes de l\'Univers. Deux niveaux développés de'
    . ' cette technologie peuvent donc être mis à profit pour coloniser une autre planète.',
131 => 'Augmente la production de métal de 2%',
132 => 'Augmente la production de cristal de 2%',
133 => 'Augmente la production de deutérium de 2%',
199 => 'En lançant une charge concentrée de particules Gravitons un champ de pesanteur artificielle peut être'
    . ' construit, où les vaisseaux ou même les lunes peuvent être anéantis.',

202 => 'Le petit transporteur est un vaisseau très maniable qui peut transporter des matériaux rapidement sur d\'autres'
    . ' planètes.',
203 => 'Le grand transporteur a une plus grande capacité de chargement et est encore plus rapide que le chasseur'
    . ' léger.',
204 => 'Très agile, vaisseau de combat léger qui se trouve sur presque toutes les planètes. Les coûts ne sont pas'
    . ' particulièrement élevés, la force d\'attaque et le blindage sont très faibles.',
205 => 'Développement du chasseur léger, le chasseur lourd est mieux blindé et a une puissance supérieure en attaque.',
206 => 'Le blindage des croiseurs est presque trois fois plus important que celui des chasseurs lourds et leur'
    . ' puissance d\'attaque est deux fois supérieure. En outre, ils sont très rapides.',
207 => 'Les vaisseaux de bataille forment l\'épine dorsale de la flotte. Leur artillerie lourde, leur grande vitesse et'
    . ' leur espace de chargement important, font de ces vaisseaux de sérieux rivaux.',
208 => 'Les autres planètes peuvent être colonisées par ce vaisseau.',
209 => 'Le recycleur peut recycler des matières premières à partir des champs de débris.',
210 => 'Les sondes d\'espionnage sont de petits drones agiles, qui fournissent des données sur de longues distances sur'
    . ' les flottes et les planètes.',
211 => 'Le bombardier a été spécialement conçu pour détruire les défenses planétaires.',
212 => 'Les satellites solaires sont des plates-formes simples de cellules solaires qui sont situés sur une orbite'
    . ' élevée et stationnaire. Ils recueillent la lumière de l\'étoile et la transmettent à la station au sol par'
    . ' laser.',
213 => 'Le destructeur est le roi des vaisseau de guerre.',
214 => 'La puissance destructrice de l\'étoile de la mort est sans pareil et peut détruire des lunes.',
215 => 'Le traqueur est spécialisé dans l\'interception des flottes ennemies.',
216 => 'Le successeur de l\'étoile de la mort est très populaire, plus rapide, mais pas aussi forte.',
217 => 'Le transporteur ultime est un développement du transporteur ultime, il a une capacité de chargement très'
    . ' supérieure et se déplace plus vite grâce aux technologies appropriées.',
218 => 'Il vous procurera le pire des scénarios, bien que très lent.',
219 => 'Le recycleur ultime dispose d\'une capacité de chargement énorme et de nouveaux moteurs qui lui permettent de'
    . ' voler plus vite et de recueillir davantage dans les champs de débris.',

401 => 'Le lanceur de missiles est un moyen de défense simple mais rentable.',
402 => 'Le canon laser léger est une arme au sol simple qui utilise des systèmes spéciaux de ciblage pour suivre'
    . ' l\'ennemi et tirer un faisceau laser de haute intensité conçu pour percer la coque de la cible.',
403 => 'Le canon laser lourd est une version améliorée du canon laser léger.',
404 => 'Le canon de Gauss tire des projectiles en métal de haute densité à très grande vitesse.',
405 => 'Un canon à ions est une arme qui tire des faisceaux d\'ions (particules chargées positivement ou'
    . ' négativement).',
406 => 'Le lanceur de plasma allie une grande cellule de combustible d\'un réacteur nucléaire à la puissance d\'un'
    . ' accélérateur électromagnétique qui déclenche une impulsion, ou tore, de plasma.',
407 => 'Le petit bouclier enveloppe toute la planète d\'un dôme, qui peuvent absorber des quantités énormes'
    . ' d\'énergie.',
408 => 'Le développement du petit bouclier a besoin de plus d\'énergie pour endurer les attaques.',
409 => 'C\'est l\'évolution du grand bouclier. Il utilise beaucoup plus d\'énergie mais peut endurer les attaques de'
    . ' davantage de vaisseaux.',
410 => 'Après des années de recherche sur la force gravitationnelle, les chercheurs sont en mesure de développer un'
    . ' canon de gravitons qui génère un petit champ gravitionnel concentré qui peut viser l\'ennemi.',
411 => 'Il s\'agit une plateforme défensive. Elle n\'a aucun pouvoir direct, et elle est maintenue par gravité dans une'
    . ' orbite stable de la planète. Ce processus exige une quantité élevée d\'énergie. ',

502 => 'Les missiles d\'interception sont utilisés pour détruire les missiles interplanétaires assaillants.',
503 => 'Les missiles interplanétaires sont utilisés pour détruire les défenses adversaires.',

901 => 'Le métal est la principale ressource nécessaire à la construction des bâtiments et des vaisseaux. C\'est la'
    . ' ressource la moins coûteuse en énergie mais c\'est aussi la plus utilisée. Plus le niveau de la mine augmente,'
    . ' plus la mine est profonde et prend de la place sur la planète.',
902 => 'Le cristal est la ressource principale dans la fabrication des circuits électroniques et de certains alliages.',
903 => 'Le deutérium est récupéré dans les profondeurs marines. C\'est la ressource la plus rare dans l\'univers car il'
    . ' faut creuser les fonds marins pour l\'obtenir, ce qui en fait également la ressource la plus coûteuse.',
911 => 'De l\'énergie est nécessaire à la production de chacune des ressources.',
];

$LNG['longDescription'] = [
1 => 'Utilisé dans l\'extraction du metal, les mines de metals sont d\'une importance capitale pour tous les Empires'
    . ' émergents.',
2 => 'Le cristal est la principale ressource utilisé dans la construction des circuits éléctroniques et intervient dans'
    . ' la fabrication de certains alliages.',
3 => 'Le deutérium est utilisé comme combustible pour les vaisseaux spatiaux et est récolté en eau profonde. Le'
    . ' deutérium est une substance rare et est donc relativement coûteux.',
4 => 'Les centrales solaires absorbent l\'énergie du rayonnement solaire. Tous les mineurs ont besoin d\'énergie pour'
    . ' fonctionner.',
6 => 'Réduit le temps de recherche de chaque niveau de 8%.',
12 => 'Le réacteur de fusion utilise le deutérium pour produire de l\'énergie.',
14 => 'Les usines robotisées fabriquent les robots de construction pour aider à la construction de bâtiments. Chaque'
    . ' niveau augmente la vitesse de la mise à niveau des bâtiments',
15 => 'Ceci est le summum de la technologie robotique. Chaque niveau réduit le temps de construction de bâtiments, les'
    . ' vaisseauxs, et les défenses',
21 => 'Tous les types de vaisseauxs et des installations défensives sont construits dans le chantier naval planétaire.',
22 => 'Permet de stocker l\'excédent de métal.',
23 => 'Permet de stocker l\'excédent de crystal.',
24 => 'Permet de stocker l\'excédent de deuterieum',
31 => 'Un laboratoire de recherche est nécessaire afin de mener des recherches sur les nouvelles technologies.',
33 => 'Le terraformeur augmente la surface exploitable de la planète.',
34 => 'L\'Alliance Dépot offre la possibilité de fournir du carburant aux flottes alliés en orbite.',
35 => 'Le quai de réparation offre la possibilité de réparer les navires détruits qui ont quitté un champ d\'épaves à la'
    . ' suite d\'un combat. Le temps de réparation est de 12 heures maximum, mais il faut au moins 30 minutes jusqu\'à ce'
    . ' que les navires puissent être mis en service.<br>A partir du moment où le champ d\'épave est créé, il reste 3 jours'
    . ' pour commencer les réparations. Les navires réparés doivent être activement remis en service une fois la réparation'
    . ' terminée. Si cela ne se produit pas, ils seront automatiquement remis en service au bout de 3 jours.',
41 => 'La lune n\'a pas d\'atmosphère donc une base lunaire doit être construite avant la construction d\'autres'
    . ' bâtiments.',
42 => 'Le réseau de capteurs permet de surveiller les mouvements de la flotte. Plus le niveau est haut, plus la portée'
    . ' de la gamme de la phalange est grande .',
43 => 'Les portails de saut sont d\'énormes émetteurs qui sont en mesure d\'envoyer de grandes flottes sans perdre de'
    . ' temps à travers l\'univers.',
44 => 'Silos de missiles utilisés pour le stockage de roquettes.',

106 => 'Informations sur d\'autres planètes et lunes peut être obtenues en utilisant cette technologie.',
108 => 'Plus de flottes peuvent être commandés par l\'augmentation des capacités informatiques. Chaque niveau de'
    . ' l\'informatique augmente le nombre maximal de flottes par une.',
109 => 'La technologie des armes rend les systèmes d\'armes plus efficaces. Chaque niveau de la technologie des armes'
    . ' augmente la force de l\'arme d\'unités de 10% de la valeur de base.',
110 => 'La technologie de blindage rend les boucliers à des vaisseaux et des installations de défense plus efficaces.'
    . ' Chaque niveau de la technologie de protection augmente la résistance des boucliers de 10% de la valeur de'
    . ' base.',
111 => 'L\'alliages spéciaux permet d\'améliorer l\'armure sur les vaisseaux et les structures défensives.'
    . ' L\'efficacité de l\'armure peut être augmenté de 10% par niveau.',
113 => 'La commande des différents types d\'énergie est nécessaire pour de nombreuses nouvelles technologies.',
114 => 'En intégrant la 4e et 5e dimensions, il est désormais possible de rechercher un nouveau type de propultion qui'
    . ' est plus économique et efficace.',
115 => 'Le développement de cette propultion fait que les vaisseaux sont plus rapides, bien que chaque niveau augmente'
    . ' la vitesse de seulement 10% de la valeur de base.',
117 => 'La propultion a impulsion est basée sur le principe de la réaction. Le développement de cette propultion rend'
    . ' les vaisseaux plus rapides, bien que chaque niveau augmente la vitesse de seulement 20% de la valeur de base.',
118 => 'L\'Hyperspace déforme l\'espace temps autour des vaisseaux. Le développement de cette propultion fait que les'
    . ' vaisseaux sont plus rapides, bien que chaque niveau augmente la vitesse de seulement 30% de la valeur de base.',
120 => 'Focaliser la lumière produit un faisceau qui cause des dommages quand elle frappe un objet.',
121 => 'Un faisceau d\'ions accélérés mortelle. Cela provoque d\'énormes dégâts lors de la frappe d\'un objet.',
122 => 'Un autre développement de la technologie d\'ions qui accélère avec une haute énergie plasma, à la suite d\'une'
    . ' surchauffe d\'ions, cela a un effet dévastateur lors de la frappe d\'un objet.',
123 => 'Les chercheurs sur différentes planètes communiquent via ce réseau.',
124 => 'Avec un module de recherche en astrophysique, les vaisseaux peuvent entreprendre de longues expéditions. Chaque'
    . ' deuxième niveau de cette technologie vous permettra de coloniser une planète supplémentaire.',
131 => 'Augmente la production de Métal de 2%',
132 => 'Augmente la production de Crystal de 2%',
133 => 'Augmente la production de Deuterium de 2%',
199 => 'Un tir concentrée de particules de graviton peut créer un champ de gravité artificielle, qui peut détruire les'
    . ' vaisseaux ou même des lunes.',

202 => 'Le cargo léger est un vaisseau agile qui peut transporter rapidement des ressources vers d\'autres planètes.',
203 => 'Ce cargo a une capacité de chargement beaucoup plus grande que celle du cargo léger, et est généralement plus'
    . ' rapide grâce à un entraînement amélioré.',
204 => 'C\'est le premier vaisseau de combat que tous les empereurs vont construire. Le chasseur léger est un vaisseau'
    . ' agile, mais vulnérable. En grande quantité, ils peuvent devenir une grande menace pour tout empire. Ils sont'
    . ' les premiers à accompagner les petites et grandes cargaisons vers les planètes hostiles à faibles défenses. ',
205 => 'Ce combattant est mieux armé et a une force d\'attaque plus élevée que le chasseur léger.',
206 => 'Les croiseurs sont trois fois plus blindés que les combattants lourds et ont plus de deux fois leur puissance'
    . ' de feu. En outre, ils sont très rapides. ',
207 => 'Les vaisseaux de combat forment l\'épine dorsale d\'une flotte. Leurs canons lourds, à haute vitesse, et leurs'
    . ' grandes cales en font des adversaires à prendre au sérieux.',
208 => 'Ce vaisseau bien blind&eacute; sert &agrave; conqu&eacute;rir de nouvelles plan&egrave;tes, ce qui est essentiel'
    . ' pour un empire en plein essor. Sur la nouvelle colonie, le vaisseau est utilis&eacute; comme fournisseur de'
    . ' mati&egrave;res premi&egrave;res : il est d&eacute;mont&eacute; et tous les mat&eacute;riaux recyclables sont'
    . ' utilis&eacute;s pour l\'exploration du nouveau monde. Le nombre de plan&egrave;tes colonisables par empire'
    . ' d&eacute;pend directement de la recherche en astrophysique. Le premier niveau, ainsi que chaque deuxi&egrave;me niveau'
    . ' suppl&eacute;mentaire, permettent de poursuivre la colonisation.',
209 => 'Le recycleur peut obtenir des matières premières à partir de champs de débris.',
210 => 'Les sondes d\'espionnage sont de petits drones agiles, qui fournissent des données sur de longues distances sur'
    . ' les flottes et les planètes.',
211 => 'Le destructeur a été spécialement conçu pour détruire les défenses d\'une planète.',
212 => 'Les satellites solaires sont de simples plates-formes de paneaux solaires qui sont situées sur une orbite'
    . ' stationnaire élevée. Ils recueillent la lumière du soleil et la transmettent à la station au sol par laser.',
213 => 'Le destroyer est le roi des vaisseaux de guerre.',
214 => 'Le pouvoir destructeur de l\'Etoile de la Mort est à nulle autre pareille et peut détruire des lunes.',
215 => 'Le croiseur de combat est spécialisé dans l\'interception des flottes ennemies.',
216 => 'Le successeur de la fameuse étoile de la mort, plus rapide, mais pas aussi fort.',
217 => 'C\'est une amélioration du cargo lourd, il a plus de capacité de transport et vole plus grâce à la'
    . ' techonologie.',
218 => 'Le pire de tous, bien que très lent.',
219 => 'C\'est un immense cargo de recyclage, avec de nouveaux moteurs, qui lui permettent de voler plus vite et de'
    . ' recueillir plus de débris.',

401 => 'Le lance-roquettes est une défense simple mais rentable.',
402 => 'Les Lasers légers sont des armes simples basés au sol qui utilisent des systèmes spéciaux de ciblage pour'
    . ' suivre l\'ennemi et tirer un laser de haute intensité conçu pour transpercer la coque de la cible.',
403 => 'Le laser lourd est une version améliorée du laser léger.',
404 => 'Le canon de Gauss tire des projectiles métalliques de haute densité à très grande vitesse.',
405 => 'Un canon à ions est une arme qui tire des faisceaux d\'ions (particules chargées positivement ou'
    . ' négativement).',
406 => 'la tourelle plasma utilise une grande pile à combustible d\'un réacteur nucléaire pour alimenter un'
    . ' accélérateur électromagnétique qui déclenche une impulsion de plasma.',
407 => 'Le Petit bouclier enveloppe toute la planète dans un champ, qui peut absorber des quantités énormes'
    . ' d\'énergie.',
408 => 'Le développement du Grand Bouclier Dome nécessite beaucoup plus d\'énergie pour résister aux attaques.',
409 => 'C\'est l\'évolution de la technologie du grand bouclier Dome. Il utilise beaucoup plus d\'énergie, mais peut'
    . ' endurer bien plus d\'attaques que tout autre bouclier.',
410 => 'Après des années de recherche sur la force de gravitation, les chercheurs sont capables de développer un canon'
    . ' gravitationnel qui génère un petit champ de Gravition concentré, qui peut être tirer sur l\'ennemi.',
411 => 'C\'est une plate-forme de défense mobile. Il n\'a pas d\'énergie directement, et est maintenu par gravité dans'
    . ' une orbite stable de la planète. Le début de ce processus nécessite beaucoup d\'énergie.',

502 => 'Les missiles intercepteurs sont utilisés pour détruire attaquer missiles interplanétaires.',
503 => 'Les missiles interplanétaires sont utilisés pour détruire les adversaires défenses.',

901 => 'Le métal est la principale ressource nécessaire à la construction des bâtiments et des vaisseaux. C\'est la'
    . ' ressource la moins coûteuse en énergie mais c\'est aussi la plus utilisée. Plus le niveau de la mine augmente,'
    . ' plus la mine est profonde et prend de la place sur la planète.',
902 => 'Le cristal est la ressource principale dans la fabrication des circuits électroniques et de certains alliages.',
903 => 'Le deutérium est récupéré dans les profondeurs marines. C\'est la ressource la plus rare dans l\'univers car il'
    . ' faut creuser les fonds marins pour l\'obtenir, ce qui en fait également la ressource la plus coûteuse.',
911 => 'De l\'énergie est nécessaire à la production de chacune des ressources.',
];

$LNG['spytechStatsDescription']='Aux niveaux 0 et 1, seuls les points totaux des autres joueurs sont visibles. À partir du niveau 2, les points de construction des autres joueurs sont également visibles. '
    . 'À partir du niveau 4, les points de recherche sont également affichés. A partir du niveau 6, les navires et les points de défense sont affichés ainsi que les statistiques de la bataille.';
$LNG['secureResources'] = 'Selon le niveau d\'amélioration, 1 % de la production quotidienne, jusqu\'à un maximum de 10 %, est protégé contre le pillage pendant les attaques.';
