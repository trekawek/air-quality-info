### Nowości

##### Zmiana certyfikatu

*2024-03-21*

aqi.eco wykorzystuje serwis Let's Encrypt, aby zapewnić bezpieczne połączenie przez protokół https. Ten sam protokół jest używany, aby odbierać dane z czujników.

W zeszłym miesiącu, [Let's Encrypt zaktualizował swój główny certyfikat](https://letsencrypt.org/2023/07/10/cross-sign-expiration) z *DST Root CA X3* na *ISRG Root X1*.

Urządzenia sensor.community i Nettigo Air Monitor wykorzystują stary certyfikat *DST Root CA X3*, który umieszczono na stałe w ich firmware. W rezultacie, nie będą w stanie połączyć się z aqi.eco, gdy zmiana zapowiedziana przez Let's Encrypt dojdzie do skutku.

**NAM**

Nettigo opublikowało nowy firmware: [NAMF-2020-45a](https://github.com/nettigo/namf/releases/tag/NAMF-2020-45a) (dla użytkowników *stable*) oraz [NAMF-2020-46rc4](https://github.com/nettigo/namf/releases/tag/NAMF-2020-46rc4) (dla użytkowników *beta*). Te i nowsze wersje będą w stanie wysyłać dane do aqi.eco nawet po zmianie certyfikatu.

W większości przypadków firmware jest aktualizowany automatycznie. Jeśli się tak nie stanie, należy otworzyć stronę ustawień NAM i kliknąć:

1. *Konfiguracja*
2. *Zaawansowane*
3. *Aktualizuj firmware automatycznie* powinno być zaznaczone.
4. Jako kanał, należy wybrać *stable* lub *beta*.

**sensor.community**

Jak do tej pory sensor.community nie opublikowało firmware'u wspierającego certyfikat *ISRG Root X1* certificate. Postęp prac można śledzić pod adresem: https://github.com/opendata-stuttgart/sensors-software/pull/1015

Nadchodzący firmware będzie miał wersję `NRZ-2020-133-P1`.

Dopóki nowy firmware nie jest dostępny, problem może być tymczasowo ominięty przez wyłączenie https w ustawieniach urządzenia:

1. Otwórz stronę ustawień swojego czujnika.
2. Znajdź opcję *Wysyłaj dane do własnego API* (powinna być zaznaczona).
3. Znajdź pole *Port* ponizej. Jeśli używasz https, będzie tam wpisane `443`. Zamiast tego wpisz `80`.
4. Kliknij *Zapisz i zrestartuj*.
