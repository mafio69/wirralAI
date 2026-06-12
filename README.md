# WirralAI - Backend API with Slim 4, PHP 8.4, and SQLite

WirralAI to backend API zbudowany na Slim 4 z użyciem PHP 8.4 i SQLite. Zawiera integrację z OVH AI Endpoints oraz system logowania i zarządzania zadaniami.

## Funkcjonalności

- **Logowanie i rejestracja użytkowników**
- **Zarządzanie zadaniami (CRUD)**
- **Czaty z AI (w budowie)**
- **Integracja z OVH AI Endpoints**

## Architektura

Aplikacja stosuje clean architecture z oddzieleniem warstw:
- **Controller** - tylko obsługuje żądania HTTP
- **Mapper** - mapuje dane wejściowe do DTO
- **Service** - logika aplikacji
- **Repository** - dostęp do bazy danych
- **DTO** - obiekty przenoszące dane

## Wymagania

- PHP 8.4
- Docker i Docker Compose
- SQLite

## Konfiguracja

1. Skopiuj `.env.example` do `.env`:
```bash
cp .env.example .env
```

2. Ustaw odpowiednie wartości w `.env` (np. token OVH AI)

## Uruchamianie

### Lokalne uruchomienie (Docker):

```bash
# Budowanie i uruchamianie kontenerów
docker compose up --build

# Uruchamianie w tle
docker compose up -d
```

Aplikacja będzie dostępna na porcie `8181`.

## Panel Web UI

Aplikacja zawiera panel administracyjny dostępny pod adresem `/`. Jest to SPA (Single Page Application) umożliwiające testowanie wszystkich endpointów API bezpośrednio z przeglądarki.

Funkcje panelu:
- Rejestracja i logowanie użytkownika
- Zarządzanie zadaniami (CRUD)
- Podgląd i wysyłanie wiadomości w czatach AI

Panel jest serwowany przez Slim jako route `GET /`, który ładuje `public/ui/index.html`.

## Strona błędu 503

W przypadku błędu inicjalizacji aplikacji (np. problem z migracją bazy danych) wyświetlana jest strona błędu 503 w stylu retro terminala (zielony neon na czarnym tle). Błąd jest logowany do `error_log` z pełnym stack trace, bez ujawniania danych użytkownikowi.

## Routing

Ruch jest obsługiwany przez nginx, który serwuje pliki statyczne bezpośrednio, a wszystkie pozostałe żądania przekazuje do `index.php` (Slim 4):

| Ścieżka | Opis |
|---------|------|
| `/` | Panel SPA |
| `/api/auth/*` | Endpointy autoryzacji |
| `/api/tasks*` | Endpointy zadań |
| `/api/chats*` | Endpointy czatów |

## Endpointy API

### Autoryzacja
- `POST /api/auth/register` - Rejestracja użytkownika
- `POST /api/auth/login` - Logowanie użytkownika
- `GET /api/auth/me` - Pobierz informacje o zalogowanym użytkowniku

### Zadania
- `GET /api/tasks` - Lista zadań
- `POST /api/tasks` - Tworzenie zadania
- `PUT /api/tasks/{id}` - Aktualizacja zadania
- `DELETE /api/tasks/{id}` - Usunięcie zadania

### Czaty
- `GET /api/chats` - Lista czatów
- `POST /api/chats` - Tworzenie czatu
- `GET /api/chats/{id}` - Pobranie czatu
- `POST /api/chats/{id}/messages` - Dodanie wiadomości

## Struktura katalogów

```
├── public/
│   ├── index.php          # Entry point Slim
│   ├── ui/index.html      # Panel SPA
│   └── error-pages/       # Statyczne strony błędów
│       └── 503.html
├── src/
│   ├── Controller/        # Kontrolery HTTP
│   ├── Mapper/            # Mappery DTO
│   ├── Dto/               # Obiekty danych
│   ├── Service/           # Logika aplikacji
│   ├── Repository/        # Dostęp do bazy danych
│   ├── Infrastructure/    # Infrastruktura (baza, AI klient)
│   └── Exception/         # Wyjątki aplikacyjne
├── docker/
│   └── nginx/default.conf # Konfiguracja nginx
├── var/database/          # Baza SQLite
└── docker-compose.yml
```

## Baza danych

Aplikacja używa SQLite z automatycznymi migracjami przy starcie (`public/index.php`).
W przypadku błędu migracji:
- Błąd jest logowany do `error_log` z komunikatem, plikiem, linią i stack trace
- Serwer zwraca HTTP 503 ze stroną błedu w stylu retro terminala
- Aplikacja nie uruchamia się — bezpieczne zatrzymanie

Tabele:
- `users` - użytkownicy
- `sessions` - sesje
- `chats` - czaty
- `chat_messages` - wiadomości
- `tasks` - zadania

## Integracja z OVH AI

Aplikacja integruje się z OVH AI Endpoints przez klasę `OvhAiClient`.
Domyślny model: `Qwen3-Coder-30B-A3B-Instruct`

## Testy

Uruchom testy jednostkowe:
```bash
composer test
```

## Wymagania techniczne

- PHP 8.4
- Composer
- Docker i Docker Compose# wirralAI
