<?php
// http://powietrze.gios.gov.pl/pjp/current
define('POLLUTION_LEVELS', array(
  array('name' => 'Bardzo niski',  'desc' => 'Jakość powietrza jest bardzo dobra, zanieczyszczenie powietrza nie stanowi zagrożenia dla zdrowia, warunki bardzo sprzyjające do wszelkich aktywności na wolnym powietrzu, bez ograniczeń.'),
  array('name' => 'Niski',         'desc' => 'Jakość powietrza jest zadowalająca, zanieczyszczenie powietrza powoduje brak lub niskie ryzyko zagrożenia dla zdrowia. Można przebywać na wolnym powietrzu i wykonywać dowolną aktywność, bez ograniczeń.'),
  array('name' => 'Średni',        'desc' => 'Jakość powietrza jest akceptowalna. Zanieczyszczenie powietrza może stanowić zagrożenie dla zdrowia w szczególnych przypadkach (dla osób chorych, osób starszych, kobiet w ciąży oraz małych dzieci). Warunki umiarkowane do aktywności na wolnym powietrzu.'),
  array('name' => 'Wysoki',        'desc' => 'Jakość powietrza jest dostateczna, zanieczyszczenie powietrza stanowi zagrożenie dla zdrowia (szczególnie dla osób chorych, starszych, kobiet w ciąży oraz małych dzieci) oraz może mieć negatywne skutki zdrowotne. Należy rozważyć ograniczenie (skrócenie lub rozłożenie w czasie) aktywności na wolnym powietrzu, szczególnie jeśli ta aktywność wymaga długotrwałego lub wzmożonego wysiłku fizycznego.'),
  array('name' => 'Bardzo wysoki', 'desc' => 'Jakość powietrza jest zła, osoby chore, starsze, kobiety w ciąży oraz małe dzieci powinny unikać przebywania na wolnym powietrzu. Pozostała populacja powinna ograniczyć do minimum wszelką aktywność fizyczną na wolnym powietrzu - szczególnie wymagającą długotrwałego lub wzmożonego wysiłku fizycznego.'),
));

define('PM10_THRESHOLDS', array(0, 25, 50, 90, 180));
define('PM25_THRESHOLDS', array(0, 15, 30, 55, 110));

define('PM10_LIMIT', 50);
define('PM25_LIMIT', 25);

function find_level($thresholds, $value) {
  if ($value === null) {
    return null;
  }
  foreach ($thresholds as $i => $v) {
    if ($v > $value) {
      return $i - 1;
    }
  }
  return count($thresholds) - 1;
}

?>