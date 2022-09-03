<?php

// Traduction Française by BigTwoProduction (KickXAss4Ever & Apocalypto2202) - All rights reserved (C) 2016
// Web : http://www.big-two.tk
// Version 1.0 - Initial release
// Version 1.1 - Decode accent HTML to UTF-8 format & small spellchecking

$LNG['and'] = 'et';

//----------------------------------------------------------------------------//
//SYSTEM
$LNG['sys_attacker_lostunits'] = 'Pertes attaquant :';
$LNG['sys_defender_lostunits'] = 'Pertes defenseur :';
$LNG['sys_units'] = 'Unités';
$LNG['debree_field_1'] = 'Coordonnées Champs de Débris';
$LNG['debree_field_2'] = 'Débris.';
$LNG['sys_moonproba'] = 'Probabilité d\'une Lune: ';
$LNG['sys_moonbuilt'] = 'Les énormes quantités de métal et de cristal libres s\'attirent et forment un satellite autour de la planète !';
$LNG['sys_attack_title'] = 'Les flottes suivantes s\'opposent : ';
$LNG['sys_attack_round'] = 'Tour';
$LNG['sys_attack_attacker_pos'] = 'Attaquant';
$LNG['sys_attack_techologies'] = 'Armes : %d %% Boucliers : %d %% Blindage : %d %% ';
$LNG['sys_attack_defender_pos'] = 'Défenseur';
$LNG['sys_ship_type'] = 'Type';
$LNG['sys_ship_count'] = 'Nombre';
$LNG['sys_ship_weapon'] = 'Armes';
$LNG['sys_ship_shield'] = 'Boucliers';
$LNG['sys_ship_armour'] = 'Blindage';
$LNG['sys_destroyed'] = 'Détruit !';
$LNG['fleet_attack_1'] = 'La flotte attaquante tire avec une force de';
$LNG['fleet_attack_2'] = 'Sur le défenseur. Les boucliers du défenseur absorbent';
$LNG['fleet_defs_1'] = 'La flotte du défenseur tire avec une force de';
$LNG['fleet_defs_2'] = 'Sur l\'attaquant. Les boucliers de l\'attaquant absorbent';
$LNG['damage'] = 'Points de dégats';
$LNG['sys_attacker_won'] = 'L\'attaquant a gagné la bataille !';
$LNG['sys_defender_won'] = 'Le défenseur a gagné la bataille !';
$LNG['sys_both_won'] = 'Le combat se termine par un match nul !';
$LNG['sys_stealed_ressources'] = 'Le butin s\'elève a';
$LNG['sys_and'] = 'Et';
$LNG['sys_mess_tower'] = 'Tour de contrôle';
$LNG['sys_mess_attack_report'] = 'Rapport de bataille';
$LNG['sys_spy_fleet'] = 'Flotte';
$LNG['sys_spy_defenses'] = 'Défenses';
$LNG['sys_mess_qg'] = 'Département Espionnage';
$LNG['sys_mess_spy_report_moon'] = '(Lune)';
$LNG['sys_mess_spy_report'] = 'Rapport d\'espionnage';
$LNG['sys_mess_head'] = 'Rapport d espionnage %s [%d:%d:%d] sur %s';
$LNG['sys_mess_spy_lostproba'] = 'La probabilité de la destruction de la/des sonde(s) d\'espionnage est :  %d %% ';
$LNG['sys_mess_spy_control'] = 'Tour de contrôle';
$LNG['sys_mess_spy_activity'] = 'Activité d\'espionnage';
$LNG['sys_mess_spy_ennemyfleet'] = 'Une flotte hostile en provenance de la planète';
$LNG['sys_mess_spy_seen_at'] = 'A été aperçue à proximité de votre planète';
$LNG['sys_mess_spy_destroyed'] = '<font color="red">Vos sondes d\'espionnage ont été détruites !</font>';
$LNG['sys_adress_planet'] = '[%s:%s:%s]';

$LNG['sys_stat_mess_stay'] = 'Flotte localisée';
$LNG['sys_stat_mess'] = 'Votre Flotte atteint la planète %s et y délivre %s %s, %s %s and %s %s.';

$LNG['sys_colo_mess_from'] = 'Colonisation';
$LNG['sys_colo_mess_report'] = 'Rapport de colonisation';
$LNG['sys_colo_defaultname'] = 'Colonie';
$LNG['sys_colo_notech'] = 'Une de vos flottes est arrivée %s. Lorsque vos colons se sont approchés de la planète, ils'
    . ' devaient déterminer avec leur équipement si le climat était convenable et pouvait etre propise développé.'
    . ' Déçus, les colons sont revenus.';
$LNG['sys_colo_arrival'] = '';
$LNG['sys_colo_maxcolo'] = 'Une de vos flottes atteint les coordonnées %s. Les colons ne peuvent s\'y installer car'
    . ' vous avez atteint votre limite de %d planètes colonisables.';
$LNG['sys_colo_allisok'] = 'Une de vos flottes atteint les coordonnées %s. Les colons commencent a s\'installer sur la'
    . ' nouvelle planète.';
$LNG['sys_colo_badpos'] = 'Une de vos flottes atteint les coordonnées %s. Aucune colonisation n\'est possible sur cette'
    . ' planete. Les colons, déçus, reviennent.';
$LNG['sys_colo_notfree'] = 'Une de vos flottes atteint les coordonnées %s. Lorsque les colons arrivent aux coordonnées'
    . ' souhaitées, ils réalisent que la planète est déjà colonisée. Complètement démoralisés, ils décident de'
    . ' revenir.';

$LNG['sys_expe_report'] = 'Rapport d\'expédition';
$LNG['sys_recy_report'] = 'Rapport de recyclage';
$LNG['sys_expe_found_ships_nothing'] = 'Aucun vaisseau n\'a pu être trouvé';
$LNG['sys_expe_blackholl_1'] = 'Votre flotte a rencontre un tou noir et a ete partiellement detruite !';
$LNG['sys_expe_blackholl_2'] = 'Votre flotte a rencontre un tou noir et a ete completement detruite !';
$LNG['sys_expe_report_msg'] = 'Rapport d\'expédition de la flotte de %s %s :';
$LNG['sys_expe_found_goods'] = 'Des %s %s ont été réduits.';
$LNG['sys_expe_found_ships'] = 'Vos chercheurs ont trouve des vaisseaux spatiaux en parfait etat ! <br>: ';
$LNG['sys_expe_found_ress_1_1'] = 'Votre expedition a decouvert un petit champs d asteroides dont certaines matieres'
    . ' premieres peuvent etre extraites.';
$LNG['sys_expe_found_ress_1_2'] = 'Certains champs de ressources facilement accessibles ont ete trouves et des matieres'
    . ' premieres sont rapportees d un planetoide lointain.';
$LNG['sys_expe_found_ress_1_3'] = 'Certains champs de ressources facilement accessibles ont ete trouves et des matieres'
    . ' premieres sont rapportees d un planetoide lointain.';
$LNG['sys_expe_found_ress_1_4'] = 'L\'expedition a decouvert un planetoide contamine par les radiations avec une'
    . ' atmosphere hautement toxique. Le scan a cependant revele que ce planetoide contient beaucoup de matieres'
    . ' premieres. Il a ete analyse par des drones robotises pour en recuperer un maximum.';
$LNG['sys_expe_found_ress_2_1'] = 'Votre expedition a trouve un ancien convoi de transporteurs completement charges'
    . ' mais desert. Certaines ressources pourraient etre exploitees.';
$LNG['sys_expe_found_ress_2_2'] = 'Votre expedition a trouve un grand gisement de matieres premieres sur une petite'
    . ' lune a l atmosphere tenue. L equipage va exploiter au sol ces tresors naturels.';
$LNG['sys_expe_found_ress_2_3'] = 'Nous avons rencontre un petit convoi de vaisseaux civils qui a besoin de nourriture'
    . ' et de medecine de toute urgence. En echange, il nous donne tout un ensemble de ressources utiles.';
$LNG['sys_expe_found_ress_3_1'] = 'Votre expedition rapporte la decouverte d une epave Alien gigantesque. Sa'
    . ' technologie est encore rudimentaire, cependant, des pieces et des composantes du vaisseau votre expedition'
    . ' rapporte de precieuses matieres premieres.';
$LNG['sys_expe_found_ress_3_2'] = 'Votre expedition rencontre une ceinture autour d une planete inconnue contenant de'
    . ' grandes quantites de matieres premieres en mineraux. La flotte de l expedition stocke ces richesses au'
    . ' complet !';
$LNG['sys_expe_found_ships_1_1'] = 'Nous avons decouvert les restes d une ancienne expedition ! Nos techniciens'
    . ' verifient ce qu ils peuvent obtenir des epaves.';
$LNG['sys_expe_found_ships_1_2'] = 'Nous avons trouve une base pirate deserte. Certains vieux vaisseaux se trouvent'
    . ' encore dans le hangar. Nos techniciens jettent un coup d oeil sur ce qui peut encore etre utilise.';
$LNG['sys_expe_found_ships_1_3'] = 'Notre expedition a trouve une planete qui a ete presque completement detruite par'
    . ' les guerres continues. Plusieurs epaves derivent dans l orbite de cette planete. Les techniciens tentent de'
    . ' reparer une partie de cette flotte.';
$LNG['sys_expe_found_ships_1_4'] = 'Aux confins de l\'espace une vieille forteresse fait son apparition. Quelques'
    . ' vaisseaux gisent dans son hangar. Les techniciens se mettent au travail pour en recuperer quelques uns.';
$LNG['sys_expe_found_ships_2_1'] = 'Nous avons trouve les restes d une armada. Les techniciens de la flotte'
    . ' d\'expedition se rendent immediatement sur les vaisseaux en partie intacts et les remettent en ordre de'
    . ' marche.';
$LNG['sys_expe_found_ships_2_2'] = 'Notre expedition a decouvert un vieux chantier spatial automatise. Certains'
    . ' vaisseaux sont encore en phase de production. L approvisionnement en energie du chantier spatial a ete restaure'
    . ' par nos techniciens.';
$LNG['sys_expe_found_ships_3_1'] = 'Nous avons trouve un cimetiere gigantesque de vaisseaux. Les techniciens de la'
    . ' flotte de l expedition ont reussi a les mettre en marche a nouveau.';
$LNG['sys_expe_found_ships_3_2'] = 'Nous avons decouvert une planete avec des restes d une civilisation. L\'expedition'
    . ' y trouve une gigantesque station visible de l orbite de la planete. Nos techniciens et des pilotes se rendent a'
    . ' la surface pour l examiner. Ils y decouvrent des vaisseaux encore utilisables.';
$LNG['sys_expe_lost_fleet_1'] = 'Le dernier message que vous recevez de l expedition est le suivant : Zzzrrt oh Dieu'
    . ' !...Krrrzzzzt le zrrrtrzt sees krgzzzz comment Krzzzzzzzztzzzz...';
$LNG['sys_expe_lost_fleet_2'] = 'Les derniers cliches envoyes par votre expedition sont celui d un trou noir puis'
    . ' blackout total ...';
$LNG['sys_expe_lost_fleet_3'] = 'Un rupture de la coque du vaisseau mere de l expedition entraîne une explosion en'
    . ' chaine spectaculaire qui aneantit la totalite des vaisseaux de l expedition';
$LNG['sys_expe_lost_fleet_4'] = 'L expedition a connu un dysfonctionnement de l ordinateur de bord et en particulier du'
    . ' saut hyperespace. Nos scientifiques et techniciens cherchent ce qu il a bien pu se passer mais sans resultat.'
    . ' La flotte est definitivement perdue.';
$LNG['sys_expe_time_fast_1'] = 'Des agregats dans les bobines d energie des propulseur force l expedition a revenir'
    . ' plus tot que prevu. Les premiers rapports ne font pas cependant etat d une defaillance grave.';
$LNG['sys_expe_time_fast_2'] = 'Le commandant a fait preuve d initiative en exploitant un vortex instable pour'
    . ' manoeuvrer la flotte ce qui diminiue son temps de retour. En revanche, rien de particulier n a ete trouve.';
$LNG['sys_expe_time_fast_3'] = 'L\'investigation du secteur n a rien donne. Cependant, la flotte profite d un vent'
    . ' solaire qui l a fait sauter dans l espace, reduisant ainsi son temps de retour.';
$LNG['sys_expe_time_slow_1'] = 'Une erreur de calcul du navigateur entraîne un saut errone de la flotte dans l espace.'
    . ' La flotte revient mais aura du retard sur l heure de retour originel.';
$LNG['sys_expe_time_slow_2'] = 'Pour des raisons jusqu a present inconnues, le saut de la flotte de l expedition est'
    . ' completement errone. La flotte sort de l hyperespace presque au milieu d une etoile. Heureusement la flotte'
    . ' atterrit dans un systeme connu. le retour plus long que prevu initialement.';
$LNG['sys_expe_time_slow_3'] = 'Le nouveau module de navigation connait une defaillance. Le saut hyperespace de la'
    . ' flotte d expedition est completement faux. Il reste tout juste assez de deuterium pour regagner par impulsion'
    . ' la lune de la planete de depart.';
$LNG['sys_expe_time_slow_4'] = 'Votre expedition traverse un secteur d orage de particules amplifiees. Cette immense'
    . ' source d energie couplee a celle de la flotte desamorcent le calculateur de vol. Les techniciens travaillent a'
    . ' restaurer la situation ce qui prend du temps supplementaire.';
$LNG['sys_expe_time_slow_5'] = 'Le vaisseau mere de votre flotte d expedition entre en collision avec un vaisseau'
    . ' etrange qui est apparu brusquement. Le vaisseau explose et les degat pour votre vaisseau mere sont'
    . ' considerables. L expedition est compromise et votre flotte fait demi-tour.';
$LNG['sys_expe_time_slow_6'] = 'Le vent solaire d une geante rouge contrarie le saut hyperespace de la flotte et les'
    . ' calculateurs de vol travaillent maintenant a un retour premature.';
$LNG['sys_expe_nothing_1'] = 'L\'expedition ne rapporte rien de tres interessant hormis quelques curieux petits animaux'
    . ' vivant dans des marecages exotiques inconnus.';
$LNG['sys_expe_nothing_2'] = 'Votre expedition a realise de jolis cliches d une supernova mais n a rien appris de'
    . ' nouveau.';
$LNG['sys_expe_nothing_3'] = 'Un virus informatique s est infiltre dans l ordinateur de bord. Il a etrangement modifie'
    . ' la trajectoire du vol ce qui a pour consequence de le faire tourner en rond dans le systeme solaire. L'
    . ' expedition revient a vide.';
$LNG['sys_expe_nothing_4'] = 'Une forme de vie a partir d energie pure a capte l attention de tous les membres l'
    . ' expedition. Ils sont hypnotises par les echantillons sur les ecrans pendant des jours. Lorsqu ils reprennent'
    . ' leurs esprits, ils decident de rentrer, le deuterium faisant defaut.';
$LNG['sys_expe_nothing_5'] = 'Desormais nous savons que les anomalies spectrales de classe 5 entraînent des effets'
    . ' chaotiques sur les systemes embarques mais aussi des hallucinations de l equipage. Sinon l expedition ne'
    . ' rapporte rien d autre.';
$LNG['sys_expe_nothing_6'] = 'Le scan de ce secteur etait prometteur mais malheureusement nos detecteurs n ont rien'
    . ' capte sur place. L expedition revient a vide';
$LNG['sys_expe_nothing_7'] = 'L\'expedition decide de se poser sur une planete exotique et lointaine pour feter l'
    . ' anniversaire du Capitaine. Bon nombre de membres de l equipage tombe malade. Apres analyse, ils sont atteints d'
    . ' une sorte de fievre jaune. L expetion est reduite a faire demi-tour.';
$LNG['sys_expe_nothing_8'] = 'Votre expedition est reduite a faire demi-tour, exposee au vide intersideral et a l'
    . ' absence d un quelconque rayonnement.';
$LNG['sys_expe_nothing_9'] = 'Une fuite du reacteur principal d un des vaisseaux de la flotte a pratiquement declenche'
    . ' une explosion en chaine dans toute la flotte. Heureusement, vos techniciens tres performants parviennent a'
    . ' circonvenir l incendie mais l expedition doit faire demi-tour.';
$LNG['sys_expe_attack_1_1_1'] = 'Une bande de pirates de l espace desesperee a tente vainement de requisitionner votre'
    . ' flotte.';
$LNG['sys_expe_attack_1_1_2'] = 'Certains barbares primitifs nous attaquent avec des vaisseaux spatiaux archaiques.'
    . ' Nous repondons par le feu ...';
$LNG['sys_expe_attack_1_1_3'] = 'Nous avons intercepte des messages radio de pirates tres ivres. Apparemment, nous'
    . ' allons etre attaques ...';
$LNG['sys_expe_attack_1_1_4'] = 'Nous avons dû lutter contre des pirates qui, heureusement, n etaient pas trop nombreux'
    . ' ...';
$LNG['sys_expe_attack_1_1_5'] = 'Le rapport de votre flotte d expedition fait etat d un certain Moa Tikarr et de ses'
    . ' grades exigeant la reddition inconditionnelle de votre flotte. C est sans compter avec votre puissance de feu'
    . ' ...';
$LNG['sys_expe_attack_1_2_1'] = 'Votre expedition croise des pirates de l espace ...';
$LNG['sys_expe_attack_1_2_2'] = 'La flotte d expedition tombe dans le piege de pirates de l espace. La riposte est'
    . ' inevitable !';
$LNG['sys_expe_attack_1_2_3'] = 'L\'appel a l aide de l expedition precedente s est avere etre un piege de certains'
    . ' pirates ruses. Une bataille etait inevitable.';
$LNG['sys_expe_attack_1_3_1'] = 'Des signaux captes du fin fond de l espace s averent provenir d une base pirate'
    . ' secrete. Les pirates sont peu enthousiates a l idee que l on manoeuvre dans leur secteur.';
$LNG['sys_expe_attack_1_3_2'] = 'Votre flotte d expedition rapportent de gros combats avec une armada pirate !';
$LNG['sys_expe_attack_1_3_3'] = 'Nous venons de recevoir un message urgent du commandant de l\'expédition : "Ils'
    . ' viennent vers nous ! Ils ont sauté de l\'hyperespace, heureusement ce ne sont que des pirates, donc nous avons'
    . ' une chance, nous allons nous battre!';
$LNG['sys_expe_attack_2_1_1'] = 'Votre flotte d expedition experimente un premier contact peu chaleureux avec une'
    . ' espece inconnue.';
$LNG['sys_expe_attack_2_1_2'] = 'Quelques vaisseaux etrangers ouvrent le feu sur votre flotte d expedition sans'
    . ' avertissement prealable !';
$LNG['sys_expe_attack_2_1_3'] = 'Notre expedition a ete attaquee par un petit groupe de vaisseaux inconnus !';
$LNG['sys_expe_attack_2_1_4'] = 'Votre flotte d expedition entre en contact radio avec un vaisseau Alien. Elle rapporte'
    . ' un message decode. Celui-ci n indique aucune animosite de la part du vaisseau etranger. Etrangement, il ouvre'
    . ' le feu malgre tout !';
$LNG['sys_expe_attack_2_2_1'] = 'Une espece inconnue attaque notre flotte !';
$LNG['sys_expe_attack_2_2_2'] = 'Votre expedtion traverse le territoire d une civilisation Alien extremement agressive'
    . ' et guerriere. Celle-ci n apprecie guere votre presence et pointe ses armes sur votre flotte d expedition.';
$LNG['sys_expe_attack_2_2_3'] = 'La transmission radio avec votre flotte d expedition a ete interrompue quelques'
    . ' minutes. Lorsque le contact est retabli, votre flotte essuie un feu nourri. L agresseur n a pas ete identifie.';
$LNG['sys_expe_attack_2_3_1'] = 'Votre expedition connaît une invasion Alien caracterisee par d\'intenses combats !';
$LNG['sys_expe_attack_2_3_2'] = 'Notre expedition se trouve sur la trajectoire d un colossal cimetiere de vaisseaux'
    . ' spatiaux. Nous devons devier notre route pour eviter le pire.';
$LNG['sys_expe_attack_2_3_3'] = 'Nous avons des problèmes mineurs de prononciation des mots dans les langues'
    . ' étrangères. Notre diplomate a accidentellement dit "Feu" au lieu de "Paix".';
$LNG['sys_expe_attackname_1'] = 'Pirates';
$LNG['sys_expe_attackname_2'] = 'Aliens';
$LNG['sys_expe_back_home'] = 'Ta flotte est revenue de l\'expédition à %s %s.';
$LNG['sys_expe_back_home_ress'] = 'Elle ramene %s %s, %s %s et %s %s.';
$LNG['sys_expe_back_home_ships_flound'] = 'Les navires suivants ont été trouvés :';
$LNG['sys_expe_back_home_ships_lost'] = 'Les navires suivants ont été perdus :';
$LNG['sys_mess_transport'] = 'Rapport d\'exploitation';
$LNG['sys_tran_mess_owner'] = 'Une de vos flottes atteint la planete %s %s et y delivre %s %s, %s %s et %s %s.';
$LNG['sys_tran_mess_user'] = 'Une flotte pacifique de %s %s atteint %s %s et y delivre %s %s, %s %s et %s %s.';
$LNG['sys_mess_fleetback'] = 'Retour de la flotte';
$LNG['sys_tran_mess_back'] = 'Une de vos flottes revient de la planete %s %s.';
$LNG['sys_recy_gotten'] = 'Vos recycleurs ont récolté %2$s %6$s et %3$s %7$s sur un total de %4$s %6$s et %5$s %7$s aux coordonnées %1$s.';
$LNG['sys_gain'] = 'Profit';
$LNG['sys_irak_subject'] = 'Missile';
$LNG['sys_irak_no_def'] = 'La planete n a pas de defenses';
$LNG['sys_irak_no_att'] = 'Tous vos missiles ont ete interceptes.';
$LNG['sys_irak_def'] = '%d de vos missiles ont ete interceptes.';
$LNG['sys_irak_mess'] = 'Les missiles interplanetaires (%d) provenant de %s atteignent la planete %s <br><br>';
$LNG['sys_gain'] = 'Profit';
$LNG['sys_fleet_won'] = 'Une de vos flottes revient d\'une attaque sur la planète %s %s à %s %s. Vous avez capturé'
    . ' %s %s, %s %s et %s %s';
$LNG['sys_perte_attaquant'] = 'Pertes de l attaquant';
$LNG['sys_perte_defenseur'] = 'Pertes du defenseur';
$LNG['sys_debris'] = 'Champ de ruines';
$LNG['sys_destruc_title'] = 'Les flottes suivantes sont impliquees dans la destruction de la Lune :';
$LNG['sys_mess_destruc_report'] = 'Rapport : Destruction de la Lune';
$LNG['sys_raport_not_found'] = 'Rapport de bataille invalide';

$LNG['sys_raport_lost_contact'] = 'Le contact avec la flotte attaquante a été perdu ( elle a donc été détruite lors du'
    . ' premier tour.)';
$LNG['sys_destruc_lune'] = 'La probabilite de destruction de la Lune est : %d%%';
$LNG['sys_destruc_rip'] = 'La probabilite d auto destruction de la flotte est : %d%%';
$LNG['sys_destruc_stop'] = 'Le defenseur a empeche la destruction de Lune avec succes.';
$LNG['sys_destruc_mess1'] = 'Les etoiles de la mort dirige son energie destrcutrice sur la Lune.';
$LNG['sys_destruc_mess'] = 'Une flotte de la planete [%d:%d:%d] atteint la Lune en [%d:%d:%d].';
$LNG['sys_destruc_echec'] = 'Des tremblements de terre secouent la planete. Un probleme se produit : Les etoiles de la'
    . ' mort explosent en millions de fragments. <br>L onde de choc se repercute sur toute la flotte.';
$LNG['sys_destruc_reussi'] = 'Les tirs des etoiles de la mort mettent la Lune en piece. <br>La Lune est completement'
    . ' detruite.';
$LNG['sys_destruc_null'] = 'Les etoiles de la mort ne peuvent fournir leur plein rendement et implosent. La lune n est'
    . ' pas detruite.';


$LNG['fcp_colony'] = 'Colonie';
$LNG['fl_simulate'] = 'Simuler';

$LNG['type_mission_1'] = 'Attaquer';
$LNG['type_mission_2'] = 'Attaque associee';
$LNG['type_mission_3'] = 'Transport';
$LNG['type_mission_4'] = 'Deployer';
$LNG['type_mission_5'] = 'Stationner';
$LNG['type_mission_6'] = 'Espionner';
$LNG['type_mission_7'] = 'Coloniser';
$LNG['type_mission_8'] = 'Recycler';
$LNG['type_mission_9'] = 'Destruire';
$LNG['type_mission_15'] = 'Expedition';

$LNG['type_planet_short_1'] = 'P';
$LNG['type_planet_short_2'] = 'D';
$LNG['type_planet_short_3'] = 'L';

$LNG['type_planet_1'] = 'Planète';
$LNG['type_planet_2'] = 'Débris';
$LNG['type_planet_3'] = 'Lune';

$LNG['sys_transfer_mess_owner'] = $LNG['sys_tran_mess_owner'];
$LNG['sys_transfer_mess_user'] = $LNG['sys_tran_mess_user'];
