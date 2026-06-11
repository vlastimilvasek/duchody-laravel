<x-mail::message>
# Vítejte!

Odběr newsletteru **Důchody.cz** je potvrzen. Co od nás můžete čekat:

- **Valorizace a změny zákonů** — vždy, když se něco změní, dozvíte se to jako první.
- **Nové kalkulačky a nástroje** — průběžně přidáváme nové funkce.
- **Praktické rady** — jak zvýšit důchod, kdy odejít, jak na penzijní spoření.

Žádný spam — píšeme jen tehdy, když máme co říct.

<x-mail::button :url="route('kalkulacka.vyse')" color="success">
Spočítat si důchod
</x-mail::button>

S pozdravem,<br>
Redakce Důchody.cz

<x-mail::subcopy>
Pokud už newsletter nechcete dostávat, můžete se [odhlásit jedním klikem]({{ $unsubscribeUrl }}).
</x-mail::subcopy>
</x-mail::message>
