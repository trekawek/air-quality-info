<?php
// http://powietrze.gios.gov.pl/pjp/current
define('PM10_INDEX', array(0, 21, 61, 101, 141, 201));
define('PM25_INDEX', array(0, 13, 37, 61, 85, 121));
define('INDEX_DESC', array(
  array('Bardzo dobry', 'Jakość powietrza jest bardzo dobra, zanieczyszczenie powietrza nie stanowi zagrożenia dla zdrowia, warunki bardzo sprzyjające do wszelkich aktywności na wolnym powietrzu, bez ograniczeń.'),
  array('Dobry', 'Jakość powietrza jest zadowalająca, zanieczyszczenie powietrza powoduje brak lub niskie ryzyko zagrożenia dla zdrowia. Można przebywać na wolnym powietrzu i wykonywać dowolną aktywność, bez ograniczeń.'),
  array('Umiarkowany', 'Jakość powietrza jest akceptowalna. Zanieczyszczenie powietrza może stanowić zagrożenie dla zdrowia w szczególnych przypadkach (dla osób chorych, osób starszych, kobiet w ciąży oraz małych dzieci). Warunki umiarkowane do aktywności na wolnym powietrzu.'),
  array('Dostateczny', 'Jakość powietrza jest dostateczna, zanieczyszczenie powietrza stanowi zagrożenie dla zdrowia (szczególnie dla osób chorych, starszych, kobiet w ciąży oraz małych dzieci) oraz może mieć negatywne skutki zdrowotne. Należy rozważyć ograniczenie (skrócenie lub rozłożenie w czasie) aktywności na wolnym powietrzu, szczególnie jeśli ta aktywność wymaga długotrwałego lub wzmożonego wysiłku fizycznego.'),
  array('Zły', 'Jakość powietrza jest zła, osoby chore, starsze, kobiety w ciąży oraz małe dzieci powinny unikać przebywania na wolnym powietrzu. Pozostała populacja powinna ograniczyć do minimum wszelką aktywność fizyczną na wolnym powietrzu - szczególnie wymagającą długotrwałego lub wzmożonego wysiłku fizycznego.'),
  array('Bardzo zły', 'Jakość powietrza jest bardzo zła i ma negatywny wpływ na zdrowie. Osoby chore, starsze, kobiety w ciąży oraz małe dzieci powinny bezwzględnie unikać przebywania na wolnym powietrzu. Pozostała populacja powinna ograniczyć przebywanie na wolnym powietrzu do niezbędnego minimum. Wszelkie aktywności fizyczne na zewnątrz są odradzane. Długotrwała ekspozycja na działanie substancji znajdujących się w powietrzu zwiększa ryzyko wystąpienia zmian m.in. w układzie oddechowym, naczyniowo-sercowym oraz odpornościowym.')
));

define('PM10_LIMIT', 50);
define('PM25_LIMIT', 25);
?>