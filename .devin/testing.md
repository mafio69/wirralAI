# Testy Aplikacji WirralAI

## Wymagania

Do uruchomienia testów potrzebne jest:
- PHP 8.4+
- Composer
- Rozszerzenie PHP `pdo_sqlite`

## Instalacja rozszerzenia SQLite

### Ubuntu/Debian:
```bash
sudo apt-get install php-sqlite3
```

### macOS (Homebrew):
```bash
brew install php
# lub
sudo pecl install pdo_sqlite
```

### Windows:
Edytuj `php.ini` i odkomentuj:
```
extension=pdo_sqlite
```

## Uruchomienie testów

```bash
composer test
```

## Struktura testów

### AuthServiceTest.php
Testuje serwis autoryzacji:
- `testRegisterNewUser` - rejestracja nowego użytkownika
- `testRegisterDuplicateEmail` - próba rejestracji z istniejącym emailem
- `testLoginValidCredentials` - logowanie z poprawnymi danymi
- `testLoginInvalidCredentials` - logowanie z błędnym hasłem
- `testLoginNonExistentUser` - logowanie nieistniejącego użytkownika
- `testValidateTokenValid` - walidacja poprawnego tokena
- `testValidateTokenInvalid` - walidacja błędnego tokena
- `testVerifyEmailValidToken` - weryfikacja email z poprawnym tokenem
- `testVerifyEmailInvalidToken` - weryfikacja email z błędnym tokenem

### TaskServiceTest.php
Testuje serwis zarządzania zadaniami:
- `testCreateTask` - tworzenie zadania
- `testGetUserTasks` - pobieranie zadań użytkownika
- `testGetTaskById` - pobieranie zadania po ID
- `testGetTaskByIdNotFound` - pobieranie nieistniejącego zadania
- `testGetTaskByIdWrongUser` - próba dostępu do zadania innego użytkownika
- `testUpdateTask` - aktualizacja zadania
- `testDeleteTask` - usuwanie zadania
- `testDeleteTaskNotFound` - usuwanie nieistniejącego zadania
- `testDeleteTaskWrongUser` - próba usunięcia zadania innego użytkownika

### ChatServiceTest.php
Testuje serwis czatów:
- `testCreateChat` - tworzenie czatu z wybranym modelem
- `testCreateChatWithDefaultModel` - tworzenie czatu z domyślnym modelem
- `testGetUserChats` - pobieranie czatów użytkownika
- `testGetChatWithMessages` - pobieranie czatu z wiadomościami
- `testGetChatNotFound` - pobieranie nieistniejącego czatu
- `testGetChatWrongUser` - próba dostępu do czatu innego użytkownika
- `testAddMessage` - dodawanie wiadomości do czatu
- `testAddMessageChatNotFound` - dodawanie wiadomości do nieistniejącego czatu
- `testAddMessageWrongUser` - próba dodania wiadomości do czatu innego użytkownika

### ChatServiceModelsTest.php
Testuje funkcjonalność modeli AI:
- `testGetAvailableModels` - pobieranie listy dostępnych modeli z API OVH
- `testModelDescriptionMapping` - mapowanie opisów modeli
- `testModelCategoryMapping` - kategoryzacja modeli

## Pokrycie kodu

Testy pokrywają główne funkcjonalności aplikacji:
- Autoryzację i uwierzytelnianie
- Zarządzanie zadaniami (CRUD)
- Zarządzanie czatami (CRUD)
- Wybór modeli AI
- Walidację danych wejściowych
- Kontrolę dostępu (user ownership)

## Uwagi

- Testy używają in-memory SQLite dla izolacji
- Testy integracyjne z API OVH AI są oznaczone jako skipable jeśli API jest niedostępne
- Mockowane są zależności zewnętrzne (mail service, AI client) dla szybszych testów

## Przyszłe ulepszenia

- Dodanie testów integracyjnych z prawdziwą bazą danych
- Dodanie testów endpointów HTTP
- Dodanie testów UI (Selenium/Playwright)
- Pokrycie kodu (coverage reporting)