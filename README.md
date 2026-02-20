# RackCloud - Professional File Management System

A RackCloud egy robusztus, PHP-alapú felhőalapú fájltároló rendszer, amelyet a Rackhost gyakornoki programjának keretében fejlesztettem. A projekt az iparági sztenderd MVC (Model-View-Controller) architektúrát követi.

# Főbb Funkciók
* Custom PSR-4 Autoloader**: Saját betöltő rendszer a külső függőségek minimalizálása érdekében.
* MVC Architektúra**: Tiszta elválasztás a logika (Controllers), az adatok (Models) és a megjelenítés (Views) között.
* Biztonságos Fájlkezelés**: 
    * Token-alapú ideiglenes megosztási linkek (24h lejárattal).
    * Bináris streaming letöltés a közvetlen fájlhozzáférés megakadályozására.
* Tárhely Menedzsment**: Dinamikus kvótarendszer (50 MB limit) vizuális Progress Barral.
* Adminisztrációs Panel**: Globális statisztikák, top felhasználók és fájltípus-analitika.
* AI Predikció**: Lineáris regressziós modell a tárhely betelésének megbecslésére az audit logok alapján.
* Verziókezelés**: Automatikus archiválás azonos nevű fájlok feltöltésekor.
* Audit Log**: Minden felhasználói tevékenység (IP címmel együtt) rögzítésre kerül.

# Technikai Stack
* Backend: PHP 8.x (OOP, PDO, Namespaces)
* Frontend: Bootstrap 5, Bootstrap Icons
* Database: MySQL