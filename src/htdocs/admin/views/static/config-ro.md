### Configurarea senzorului pentru a trimite datele la aqi.eco

Pentru a utiliza aqi.eco în scop de prezentare a rezultatelor măsurării calității aerului, trebuie să aveți un senzor compatibil. În acest moment, aqi.eco este compatibil cu următorii senzori:

* [Nettigo Air Monitor](https://air.nettigo.pl/zbuduj/),
* [Sensor.Community](https://sensor.community/en/), cunoscut și ca Luftdaten,
* [Smogomierz](https://github.com/hackerspace-silesia/Smogomierz).

După ce ați achiziționat/construit senzorul, îl puteți configura pentru a trimite datele la aqi.eco:

1. [Înregistrați-vă](/register) la aqi.eco:
    - numele de sub-domeniu pe care îl introduceți va fi comun tuturor senzorilor dvs.,
    - pentru fiecare senzor veți putea crea o adresă de forma `<domeniu>.aqi.eco/<sensor>`.
2. [Autentificare](/login) utilizând numele de utilizator și parola pe care le-ați creat.
3. În panoul de administrare selectați [Dispozitive](/device) și faceți clic pe butonul [Adăugați](/device/create).
4. Introduceți numele scurt al senzorului care va fi utilizat în adresele URL și o scurtă descriere.
5. Se va deschide pagina de configurare a senzorului.
6. (Opțional) Selectați poziția senzorului pe hartă:
- dacă doriți, bifați caseta setare locație și selectați locația aproximativă a senzorului pe hartă,
- câmpul *Altitudine* va fi setat automat. Acesta va fi utilizat pentru calcularea presiunii atmosferice,
- faceți clic pe *Actualizați*.
7. Copiați valoarea câmpului *Path* din panoul de configurare al senzorului: `/update/<cheia secretă>`. Valoarea este folosită de aqi.eco pentru identificarea dispozitivului.
8. Următorii pași depind de tipul senzorului dvs.

#### Nettigo Air Monitor / Sensor.Community

9. Găsiți adresa dispozitivului dvs. în rețeaua locală. Procedura depinde de sistemul dvs. de operare. Dacă nu sunteți sigur ce adresă locală are senzorul dvs., citiți secțiunea „Cum puteți găsi un senzor în rețeaua locală?” din [documentația Nettigo Air Monitor](https://air.nettigo.pl/baza-wiedzy/namf-konfiguracja-firmware/) - folosiți funcția de traducere din PL->RO din browser-ul dvs., dacă la momentul accesării nu este definitivată traducerea din poloneză.
10. Deschideți pagina de configurare a Nettigo Air Monitor.
11. Faceți clic pe *Configurare*.
12. Găsiți opțiunea *Trimite date către propriul API* și completați întreaga secțiune cu:
- bifează *Trimite date către propriul API*,
- la *Adresă Server* se introduce `api.aqi.eco`,
- la *Cale* se introduce `/update/...` pe care ați copiat-o din aqi.eco la pasul 7,
- la *Port* se introduce 443,
- la *Nume utilizator* și *Parolă* lăsați necompletat,
13. Clic pe *Salvați și reporniți*.
14. Felicitări, de acum înainte senzorul dvs. va trimite date către aqi.eco. Dacă doriți să adăugați mai mulți senzori în cont, reveniți la punctul 3.

#### Smogomierz

9. Găsiți numele Smogomierz în rețeaua locală. Informații suplimentare in limba poloneză (folosiți Google Translate) aici: [dokumentacji Smogomierza](https://github.com/hackerspace-silesia/Smogomierz/blob/master/instrukcje/software-additionals.md#nazwa-urz%C4%85dzenia-oraz-bonjourzeroconf).
10. Deschideți pagina de configurare Smogomierz și selectați următoarele valori de configurare:
- *Wysyłanie danych do aqi.eco*: `Tak`,
- *Adres serwera aqi.eco*: `api.aqi.eco`,
- *Ścieżka aqi.eco*: wartość `/update/...` pe care ați copiat-o din aqi.eco la pasul 7.
11. După salvarea setărilor, senorul Smogomierz va începe să trimită date către aqi.eco
