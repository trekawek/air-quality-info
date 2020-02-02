### Konfiguracja czujnika z aqi.eco

Aby wykorzystać aqi.eco do prezentacji wyników pomiaru czystości powietrza, musisz mieć jeden ze wspiarnych czujników. W tej chwili aqi.eco jest kompatybilny z następującymi czujnikami:

* [Nettigo Air Monitor](https://air.nettigo.pl/zbuduj/),
* [Sensor.community](http://luftdaten.org.pl/) znany też jako Luftdaten,
* [Smogomierz](https://github.com/hackerspace-silesia/Smogomierz).

Gdy masz już swój czujnik, możesz skonfigurować go na aqi.eco:

1. [Zarejestruj](/register) się w serwisie:
    - nazwa domeny którą wpiszesz będzie wspólna dla wszystkich Twoich czujników,
    - dla każdego czujnika będziesz mógł utworzyć adres w postaci `<domena>.aqi.eco/<czujnik>`.
2. [Zaloguj](/login) się korzystając z utworzonego login i hasła.
3. W panelu administracyjnym wybierz [Urządzenia](/device) i kliknij przycisk [Dodaj](/device/create).
4. Wpisz krótką nazwę czujnika która będzie używana w adresach URL (np. *sloneczna*) oraz dłuższy opis (np. *Czujnik przy sklepie "Słoneczko"*).
5. Otworzy się strona konfiguracyjna czujnika.
6. (Opcjonalnie) Wybierz pozycję czujnika na mapie:
- jeśli chcesz, zaznacz pole *Ustaw lokalizację* i wybierz przybliżoną lokalizację czujnika na mapie,
- pole *Wysokość* ustawi się automatycznie. Będzie ono używane do obliczenia ciśnienia atmosferycznego,
- kliknij *Aktualizuj*.
7. Skopiuj wartość pola *Ścieżka* z panelu *Konfiguracja czujnika*. Powinna ona wyglądać tak: `/update/<sekretny kod>`. Kod jest używany przez aqi.eco do identyfikacji Twojego urządzenia.
8. Dalsze kroki zależą od rodzaju Twojego czujnika.

#### Nettigo Air Monitor / Luftdaten

9. Znajdź adres Twojgo urządzenia w sieci lokalnej. Sposób postępowania zależy od Twojego systemu operacyjnego. Jeśli nie jesteś pewien jaki lokalny adres ma Twój czujnik, przeczytaj sekcję "Jak znaleźć czujnik w sieci lokalnej?" w [dokumentacji Nettigo Air Monitor](https://air.nettigo.pl/baza-wiedzy/namf-konfiguracja-firmware/).
10. Otwórz stronę konfiguracyjną Nettigo Air Monitor.
11. Kliknij *Konfiguracja*.
12. Znajdź opcję *Wysyłaj dane do własnego API* i uzupełnij całą sekcję wg. wzoru:
- zaznacz *Wysyłaj dane do własnego API*,
- jako *Adres serwera* wpisz `api.aqi.eco`,
- jako *Ścieżkę* wklej wartość `/update/...` którą skopiowałeś z aqi.eco w kroku 7,
- jako *Port* wpisz 443,
- *Nazwę użytkownika* i *Hasło* pozostaw puste,
13. Kliknij *Zapisz i zrestartuj*.
14. Gratulacje, od tej pory Twój czujnik będzie wysyłał dane do aqi.eco. Jeśli chcesz dodać kolejne czujniki do swojego konta, wróć do punku 3.

#### Smogomierz

9. Znajdź nazwę Smogomierza w sieci lokalnej. Informację o tym jak to zrobić uzyskasz z [dokumentacji Smogomierza](https://github.com/hackerspace-silesia/Smogomierz/blob/master/instrukcje/software-additionals.md#nazwa-urz%C4%85dzenia-oraz-bonjourzeroconf).
10. Otwórz stronę konfiguracyjną Smogomierza i zaznacz następujące wartości konfiguracyjne:
- *Wysyłanie danych do aqi.eco*: `Tak`,
- *Adres serwera aqi.eco*: `api.aqi.eco`,
- *Ścieżka aqi.eco*: wartość `/update/...` skopiowana z aqi.eco w kroku 7.
11. Po zapisaniu ustawień Smogomierz zacznie wysyłać dane do aqi.eco.

