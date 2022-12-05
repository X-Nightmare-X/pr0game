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

// Translation into Russian - Copyright © 2010-2013 InquisitorEA <support@moon-hunt.ru>

$LNG['faq_overview']             = 'Туториал';

$LNG['questions']                = array();
$LNG['questions'][1]['category'] = 'Советы для начинающих';
$LNG['questions'][1][1]['title'] = 'Шаг 1';
$LNG['questions'][1][1]['body']  = <<<BODY
<p>В этой части туториала описаны основные постройки. Вы узнаете, какие постройки следует строить в самом начале игры, и для чего они нужны. Для успешного создания империи важно проводить строительство в определённой очерёдности.</p>
<h3>Солнечная электростанция</h3>
<p>Для добычи ресурсов, необходимых для создания империи, следует построить эту постройку в самую первую очередь. Она обеспечивает рудники для добычи металла, кристалла и синтезаторы дейтерия электроэнергией. Как только электростанция будет построена, в меню "Сырьё" можно увидеть, сколько энергии вырабатывается в данный момент.</p>
<h3>Рудник по добыче металла:</h3>
<p>Металл - один из трёх ресурсов, которые можно добывать в игре. Он используется почти во всех постройках и исследованиях, поэтому мы советуем хорошо развить этот рудник. Особенно в самом начале очень важно иметь рудник по добыче металла на несколько уровней выше, чем у других начинающих игроков.</p>
<h3>Рудник по добыче кристалла и синтезатор дейтерия:</h3>
<p>Кристалл необходим для постройки кораблей, возведения построек и проведения исследований. Он добывается в меньшем количестве, чем металл, что, однако, не делает его менее важным ресурсом. В течение игры Вы сможете убедиться, насколько горячие бои происходят из-за кристалла. Дейтерий используется для производства энергии (в термоядерной электростанции), в качестве топлива для кораблей и основного ресурса для некоторых важных исследований и построек (исследовательская лаборатория).</p>
BODY;

$LNG['questions'][1][2]['title'] = 'Шаг 2';
$LNG['questions'][1][2]['body']  = <<<BODY
<p>Для расширения своей империи и соперничества с другими игроками Вам необходимы ещё постройки. При помощи исследований и строительства корабельной верфи Вы можете начать строительство флота и планетарной обороны. Более подробно смотрите здесь:</p>
<h3>Верфь:</h3>
<p>Здесь строятся все типы кораблей, имеющихся в игре. Несмотря на то, что верфь можно строить в самом начале игры, для строительства определённых типов кораблей Вам будет необходимо провести соответствующие исследования. Обзор исследований, необходимых для строительства определённого корабля или обороны, можно найти в меню "Технологии". Чем выше уровень верфи, тем больше сокращается время строительства.</p>
<h3>Исследовательская лаборатория:</h3>
<p>Здесь проводятся исследования, необходимые для строительства новых типов построек, обороны и начала новых исследований. Кроме того, исследования могут улучшать уже существующие достижения. Чем выше уровень исследовательской лаборатории, тем короче время исследований.</p>
<h3>Фабрика роботов:</h3>
<p>Фабрика роботов сокращает время строительства построек. С каждым уровнем здания строятся всё дольше, для сокращения этого времени необходимо дальнейшее развитие фабрики роботов.</p>
BODY;

$LNG['questions'][1][3]['title'] = 'Шаг 3';
$LNG['questions'][1][3]['body']  = <<<BODY
<p>Эта часть туториала посвящена обороне планеты, а также первые действия с флотом.</p>
<h3>Обзор галактики:</h3>
<p>Необходим для локализации других игроков. В этом обзоре можно найти подробную информацию о других планетах, а также частично отправлять прямо оттуда свой флот. Также из этого обзора можно писать личные сообщения и отправлять шпионские зонды, если они уже имеются на планете.</p>
<h3>Отправление флота:</h3>
<p>Флоты можно отправлять либо через обзор галактики, либо из меню "Флот". Для начала выберите, сколько и какие типы кораблей Вы хотите отправить. После этого Вы указываете координаты цели (можно узнать в обзоре галактики) и скорость полёта. Флотам можно отдавать следующие приказы: "Атаковать", "Расположить", "Удерживать", "Переработать".</p>
<h3>Оборона:</h3>
<p>Для обороны своей планеты помимо верфи можно строить оборонительные орудия. Они предоставляют некоторую защиту наравне со флотом во время вражеских нападений. В отличие от флота, 70% оборонительных орудий могут восстанавливаться сами по себе после боя.</p>
BODY;

$LNG['questions'][2]['category'] = 'Расширенная информация';
$LNG['questions'][2][1]['title'] = 'Грабёж';
$LNG['questions'][2][1]['body']  = <<<BODY
<h3>Грабёж:</h3>
<p>Под грабежом понимается атака другого игрока с целью захвата его ресурсов.
Существует несколько видов грабежа:</p>
<ul>
<li>Грабёж фермы</li>
<li>Атака флота</li>
<li>Грабёж обломков</li>
<li>Подлов</li>
</ul>
<h3>Грабёж ферм:</h3>
<p>Самый наиболее часто встречающийся вид грабежа.<br>
Игрок сканирует близлежащие планеты и отправляет на них флоты, которые могут унести достаточно ресурсов, и которые имеют достаточно боевой мощи для пролома обороны и успешного возвращения на базу.</p>
<h3>Атака флота:</h3>
<p>Высший класс грабежа.<br>
При этом виде целенаправленно атакуются вражеские флоты.<br>
Помимо шпионских зондов и удачи для атаки флота требуется сенсорная фаланга и опыт. Как только вражеский флот найден, его надо атаковать и переработать обломки. Зачастую это непросто, т.к. флоты, несущие в себе большую прибыль в виде обломков, как правило не могут быть переработаны собственными переработчиками, поэтому на помощь надо призывать других игроков, либо организовывать атаку через САБ.</p>
<h3>Грабёж обломков:</h3>
<p>Самый удобный и самый презираемый вид наживы, при котором игрок собирает чужие обломки.
Согласно правилам обломки не принадлежат никому, и их может собирать кто угодно, поэтому такая кража зачастую является большой неприятностью.</p>
<h3>Подлов</h3>
<p>Cмысл жизни флотовода состоит в том, чтобы построить свой флот и уничтожать вражеские флоты. Так как вражеские флоты не стоят в ожидании атаки, то для их подлова надо приложить немного усилий. Подловить флот во время сэйва или во время атаки, однако это не так просто, т.к. существуют способы защиты от подлова.</p>
<ul>
<li><p>Луна облегчает подлов, т.к. при помощи сенсорной фаланги можно выследить вражеский флот. Когда флот выслежен, надо высчитать, когда он будет подлетать обратно к базе. Для этого надо знать, до какого уровня развиты двигатели у кораблей, что можно узнать из разведданных. Зная данные двигателей можно высчитать, сколько флот ещё будет в пути. Если поставить будильник на время прилёта, то у Вас будет хороший шанс!</p>
<p>Для расчёта времени существуют специальные программы, ссылки на которые можно найти на форуме.<br>Зная время прилёта, Вы отправляете свой флот и переработчиков так, чтобы они прибыли на планету на 3 секунды позже и уничтожили флот.</p></li>
<li><p>Если Вы хотите подловить флот, летящий с луны к полю обломков, то Вам потребуется примерное время его вылета. Потом Вы делаете несколько видимых полей обломков (напр., шпионскими зондами) там, куда, по Вашему мнению, летит этот флот. Теперь Вы рассчитываете, сколько времени понадобится этому флоту для достижения этих полей, а когда флот приблизится к обломкам, то при постоянной актуализации Вы можете увидеть, когда обломки исчезнут. Теперь Вы высчитываете, сколько времени флот будет лететь обратно, и отправляете свой флот так, чтобы они прибыл на несколько секунд позже.</p>
<p><b>Внимание:</b> такой подлов получается не всегда, т.к. существуют некоторые способы защиты. Например, можно выслать один переработчик, который заранее соберёт обломки.</p></li>
<li>Ещё одна форма подлова - это удержание на чужой орбите своего флота с целью защиты от атакующих флотов. Атакующий флот в таком случае отзывается, т.к. ему пришлось бы сражаться против двух флотов. Против такого подлова можно защититься, сделав сэйвскан (см. сэйвскан).</li>
<li><p>Если флот летит на удержание на другую планету и внезапно возвращается обратно, то тогда он не виден в фаланге. Но есть один способ, как такой флот можно подловить. Для этого надо некоторое время наблюдать как игрок сэйвит свой флот, когда вылетает и когда отменяет полёт. При посекундном сканировании планеты заметно, когда флот больше не виден, зная время возврата из симулятора, Вы отправляете свой так, что бы он прибыл на планету с задержкой в несколько секунд.</p>
<p>Если игрок летит с луны к полю обломков и высылает вперёд переработчиков, то подловить его практически невозможно. Однако есть один способ - уничтожить луну при помощи ЗС.</p>
<p>Когда луна уничтожена, то фалангой сканируется планета, на которую теперь автоматически возвращается флот, дальше всё делается как описано выше.</p>
<p>При подлове всегда очень полезно знать, как противник сэйвит свой флот - например каждый день в 23 часа, или в 12 часов он появляется он-лайн. Поэтому перед подловом хорошо изучите противника!</p></li>
</ul>
BODY;

$LNG['questions'][2][2]['title'] = 'Сэйв флота';
$LNG['questions'][2][2]['body']  = <<<BODY
<h3>Как мне защититься?</h3>
<p>Большое количество обороны отпугивает одних игроков, но, к сожалению, привлекает других, и они приходят и приходят, пока на планете больше ничего не остаётся. При этом теряется много ресурсов, т.к. из обороны не получаются поля обломков (за исключением отдельных вселенных, где это возможно). Но даже в отельных вселенных, где это возможно, не стоит строить слишком много обороны, т.к. это обломки будут просто подарком атакующему.</p>
<p>Ещё существуют межпланетные ракеты, чья единственная задача - уничтожение обороны. В отличие от атаки флотом, оборона после ракетной атаки не восстанавливается. Хорошо спланированное нападение с применением ракет снижает риск для собственного флота.</p>
<p>Большие флоты тоже удерживают только более слабых игроков, зато более сильные с удовольствием нападут на такой флот, чтобы собрать потом его обломки. В конце концов ещё есть САБ, которая даёт возможность нескольким слабым игрокам атаковать более сильного.</p>
<p>Хорошо развитая шпионская технология - самый дорогой способ утаить от противника информацию о себе, за исключением информации о ресурсах. К сожалению высокий уровень шпионажа может быть преодолён большим количеством вражеских зондов, и если в первых разведданных противник видит достаточно ресурсов, то он шлёт намного больше зондов, чтобы узнать всё о планете.</p>
<p>Наилучшая возможность защиты - не дать противнику увидеть сколько ресурсов есть на планете. Это делается так - все ресурсы отправляются со всем флотом, по крайней мере на то время, пока игрока нет в игре. Так как атака в полёте невозможна, то корабли и ресурсы находятся в безопасности.</p>
<h3>Сэйв как лучшая оборона</h3>
<p>Сэйв флота является одной из важнейших деталей ОГейма, которой, к сожалению, обладают не все игроки. Существует множество способов защищать свои ресурсы и флот от вражеских атак, некоторые из них надёжны на 100%, некоторые нет.<br>
Первый метод сэйва - "удержание и возврат". Для этого игрок отправляет свой флот на одну из своих колоний с заданием "удерживать". Потом этот полёт прерывается и обратный полёт не видим сенсорной фалангой. Этот способ не полностью надёжен, но если отменить полёт не в последний момент, а на несколько часов раньше, то поймать флот будет невероятно сложно.</p>
<p>Другой очень широко используемый способ - полёт с луны к полю обломков. Этот способ считается относительно надёжным, т.к. такие полёты не просматриваются фалангой. Однако если противник знает, когда флот достиг обломков, то он может рассчитать, когда флот вернётся обратно на планету. Но можно перед основным флотом запустить так называемых "теневых переработчиков", которые соберут обломки до прибытия туда основного флота, и тем самым введут противника в заблуждение. Ещё один способ сделать такой полёт надёжным - сбор обломков возле абсолютно незащищённой планеты, т.к. оно никогда не отображается в "галактике".</p>
<p>Самый надёжный вид сэйва - это удержание на разных лунах. Такой полёт не виден ни в какой фаланге, и наблюдение за полями обломков тут тоже бессмысленно. Единственная возможность подловить такой флот - это уничтожить луну, что можно сделать только при огромных затратах времени и ресурсов.</p>
BODY;

$LNG['questions'][2][2]['title'] = 'Система альянсового боя';
$LNG['questions'][2][2]['body']  = <<<BODY
<h3>Атака с САБ</h3>
<p>Чтобы атаковать с САБ для начала надо отправить флот с приказом "атаковать". После начала атаки в меню флотов Вы нажимаете на "союз" и превращаете свой флот в союз из нескольких флотов. В появившемся меню союза Вы можете дать союзу другое название или приглашать в союз новых игроков. Игрок, начавший атаку, автоматически попадает в союз. Присоедините все желаемые флоты и союз готов. Для этого каждый приглашённый игрок при указании цели атаки должен отдать приказ "совместная атака". Скорость всего союза соответствует скорости самого медленного флота. Быстрые флоты не влияют нв скорость всего союза, медленные же замедляют его до своей скорости. Обратите внимание, что в союзе могут принимать участие максимум 5 игроков с общим количеством флотов, не превышающим 16. Присоединившийся к союзу флот может увеличить время полёта на максимум 30%. Например, если союзу осталось лететь 100 минут, то после присоединения ещё одного флота максимальное время составляет 130 мин.<br>
<b>Внимание:</b> если флот основателя союза отзывается, то остальные флоты продолжают лететь дальше!</p>
<h3>Оборона с САБ</h3>
<p>При вражеской атаке у Вас есть возможность получить помощь от дружественных игроков в виде флотов. Это касается всех игроков, состоящих в Вашем альянсе, либо в списке друзей. Им просто надо отправить свои флоты на Ваши координаты с приказом «Держаться» и указать, как долго им следует оставаться на Вашей орбите. Помимо затрат дейтерия на полёт, дружественные игроки также платят некоторую сумму ресурсов, зависящую от размера флота и продолжительности пребывания на орбите. Эта сумма считывается при старте флота. Находящиеся на орбите флоты принимают участие во всех боях на планете и на лунах. В разведданных они отображаются как Ваши собственные флоты, поэтому необходимо отправлять их так, чтобы они прилетали на орбиту незадолго до атаки, и т.о. не могли быть замечены. Владельцы флотов всегда могут отозвать их в меню «флот», если их не отзывать, то они продержатся на орбите указанное при старте время.</p>
<h3>Потребление дейтерия за 1 час удержания на орбите:</h3>
<p>Малый транспорт: 5<br>
Большой транспорт: 5<br>
Лёгкий истребитель: 2<br>
Тяжёлый истребитель: 7<br>
Крейсер: 30<br>
Линкор: 50<br>
Колонизатор: 100<br>
Переработчик: 30<br>
Шпионский зонд: 0,1<br>
Бомбардировщик: 100<br>
Уничтожитель: 100<br>
Звезда смерти: 0,1</p>
BODY;

$LNG['questions'][2][3]['title'] = 'Луны';
$LNG['questions'][2][3]['body']  = <<<BODY
<p>Луна может появиться при наличии поля обломков из минимум 100 000 единиц, в этом случае шанс на появление луны составляет 1%. Максимальный шанс составляет 20% независимо от размера поля обломков.</p>
<h3>Общее</h3>
<p>Луны можно переименовывать как планеты, но в конце всегда будет стоять «Луна». Возле одной планеты можно иметь только одну луну. С помощью луны можно незаметно отправлять флоты в сэйв, поэтому стоить сделать её как можно раньше. На луне можно строить фабрику роботов, верфь, хранилище для металла, кристалла и дейтерия, склад альянса, лунную базу, сенсорную фалангу и ворота.</p>
<h3>Создание луны</h3>
<p>Под созданием луны подразумевается умышленное уничтожение флота для создание поля обломков. Как правило для этого используются лёгкие истребители, т.к. их легче всего уничтожить. Для 20%-го шанса появления луны надо уничтожить 1667 штук. Обратите внимание, что если луна делается сильным и слабым игроком, то обломки собирает только слабый, иначе это будет прокачка.</p>
<p>Во вселенной, в которой разбитая оборона также попадает в обломки, это получается лучше, т.к. один раз построенную оборону можно разбивать несколько раз. Для этого советуется строить 3334 ракетных установок или 3334 лёгких лазера.</p>
<p>При появлении луны появляется сообщение «невероятные массы свободного металла и кристалла образуют спутник возле планеты».</p>
<p>При шансе появления луны между 1 и 19% существует 20%-ная возможность того, что диаметр луны будет более 8000км.<br>Луна никогда не может быть больше 8944 км в диаметре.</p>
<h3>Застройка</h3>
<p>Как было указано выше, на луне можно строить фабрику роботов, верфь, хранилище для металла, кристалла и дейтерия, склад альянса, лунную базу, сенсорную фалангу и ворота. Однако не стоит строить какие-либо хранилища, т.к. на лунах ничего не добывается, и их строительство будет бессмысленно. Верфь также не стоит строить, т.к. корабли и оборона на лунах строятся очень долго. Целесообразность строительства склада альянса всё ещё спорна.<br>
Три важных здания на луне:</p>
<ul>
<li><p><b>Лунная база</b><br>Служит для создания новых полей для строительства, за каждый уровень даётся 3 дополнительных поля, одно из которых потом будет занято следующим уровнем базы.</p></li>
<li><p><b>Сенсорная фаланга</b><br>Служит для слежения за передвижениями вражеских флотов на планетах. Шпионаж за лунами невозможен. Каждый уровень увеличивает радиус действия.</p></li>
<li><p><b>Телепорт</b><br>При помощи ворот можно без потери времени перемещать флоты между лунами, однако транспортировка ресурсов при этом невозможна. Между прыжками должен пройти 1 час, иначе ворота перегреются.</p></li></ul>
<p>Ниже приведён список оптимальной застройки луны:<br>
<b>Легенда:</b><br>
ЛБ – лунная база<br>
СФ – сенсорная фаланга<br>
ВТ - ворота<br>
ФР – фабрика роботов</p>
<p>ЛБ1 - ФР1 - ФР2 - ЛБ2 - СФ1 - ФР3 - ЛБ3 - СФ2 - ФР4 - ЛБ4 - СФ3 - ФР5 - ЛБ5 - СФ4 - ФР6 - ЛБ6 - СФ5 - СФ6 - ЛБ7 - СФ7 - СФ8 - ЛБ8 - ВТ1<br>
После этого можно продолжать строить СФ если для этого хватает ресурсов. Также можно снести несколько уровней фабрики роботов и освободить тем самым место для СФ!</p>
<h3>Уничтожение</h3>
<p>Луну можно уничтожить, но только при помощи звезды смерти.</p>
<p>Для этого выбирается приказ «уничтожить». Для начала ЗС должна уничтожить стоящий на луне флот. При уничтожении луны ЗС тоже может быть уничтожена. Поле обломков при уничтожении луны не возникает!</p>
<h3>Атака лун звёздами смерти:</h3>
<p>Шанс уничтожения луны составляет „(100 - корень (размер луны)) * корень (кол-во ЗС)“. Шанс того, что ЗС тоже уничтожатся составляет „корень(размер луны) / 2“. Чем больше луна, тем больше шанс потерять ЗС.</p>
<h3>Шанс на появление луны и её размер:</h3>
<p>100 000 единиц обломков соответствуют 1%-му шансу на появление луны. Максимальный шанс – 20%. После изменения максимальный размер луны составляет 79 полей для строительства.<br>
Раньше размер рассчитывался так:<br>
округл. ((диаметр луны /1000)2)<br>
Наибольшая луна может быть 8944 км в диаметре, т.е. 79 полей.</p>
BODY;

$LNG['questions'][2][4]['title'] = 'Альянсы';
$LNG['questions'][2][4]['body']  = <<<BODY
<h3>Как основать свой альянс?</h3>
<p>Для того, чтобы основать альянс, зайдите в меню "Мой альянс". Потом на "Основать свой альянс". Затем надо ввести тэг и название альянса, тэг - это сокращение от полного названия. Например, если название "My Top Ally", то тэг можно сделать "MTA".<br>
Название и тэг могут содержать только буквы, цифры и пробелы.<br>
Нажмите на "основать".<br>
Если альянс с таким названием или тэгом уже существует, то Вы получите соответствующее уведомление, и Вам придётся сменить название/тэг.<br>
Если альянс создан, то Вы увидите сообщение. Нажмите "дальше" и Вы попадёте в пустое меню альянса, в которое теперь можно попасть из меню "мой альянс". В нём видно несколько вариантов выбора и информация об альянсе, вверху видно название и тэг альянса.</p>
<p>Внизу находятся следующие ссылки:<br>
"Члены": здесь видны все члены Вашего альянса, их рейтинг, координаты главных планет, дата вступления и онлайн статус. Клик на названии столбца может изменить сортировку.<br>
"Ваш ранг": здесь видно название Вашего ранга, рядом - ссылка на управление альянса.<br>
"Общее сообщение": отправка сообщения всем членам альянса или отдельным рангам.</p>
<h3>Управление альянсом</h3>
<p>Вверху находится меню "Установить ранги", где можно установить права для различных рангов. Ниже находится меню "Члены альянса", здесь можно удалять из альянса, назначать ранги и просматривать сколько времени тот или иной игрок не был в игре. При помощи двух кнопок чуть пониже можно менять название и тэг альянса.<br>
Ниже можно редактировать внешний и внутренний текст альянса. Внешний текст видят все игроки, внутренний - только члены альянса. "Текст заявки" - это пример заявки для желающих вступить в альянс.<br>
В настройках можно вставить ссылку на картинку, которую будет видно на странице альянса в игре, а также ссылку к форуму альянса, если таковой имеется. Кроме того, можно включать и отключать приём заявок, и редактировать название ранга для основателя альянса. Если ранг оставить пустым, то автоматически он будет называться "Основатель".<br>
Распустить альянс можно соответствующей кнопкой, а "Покинуть/перенять альянс" передаёт управление альянсом другому игроку.</p>
BODY;
$LNG['questions'][2][5]['title']	= 'Ссылки';
$LNG['questions'][2][5]['body']		= <<<BODY
<h3>Где я могу найти дополнительную информацию?</h3>
<p>Немецкий:<br>
<a href="https://www.owiki.de/index.php/Aufbautaktik" target="_blank">Начальный справочник (немецкий)</a><br>
<a href="https://www.owiki.de/index.php/Miner" target="_blank">Стиль игры: Шахтер (немецкий)</a> <br>
<a href="https://www.owiki.de/index.php/Raideraccount" target="_blank">Стиль игры: флиттер (немецкий)</a> <br>
<a href="https://www.owiki.de/index.php/Comber" target="_blank">Стиль игры: комбайнер (немецкий)</a> <br>
<a href="https://www.owiki.de/index.php/Saven" target="_blank">Экономия (немецкий)</a></p>
<p>Английский язык:<br>
<a href="https://ogame.fandom.com/wiki/Quick_Start_Guide" target="_blank">Краткое руководство пользователя (Английский язык)</a><br>
<a href="https://ogame.fandom.com/wiki/Playing_Styles" target="_blank">Игровые стили (Шахтер, флиттер, комбайнер)(Английский язык)</a><br>
<a href="https://ogame.fandom.com/wiki/Fleetsaving" target="_blank">Экономия (Английский язык)</a></p>
BODY;