# 🛠️ Zadania do wykonania — audyt kodu WirralAI

> Stan: 30 testów → 26 błędów, 3 porażki (0% pass)

---

## 🔴 Priorytet CRITICAL — blokujące błędy

### 1. Naprawa `settings.php` — `$env` użyte jako callable mimo że jest `null`

**Plik:** `src/settings.php:19`  
**Problem:**  
```php
'token' => $_ENV['OVH_AI_ENDPOINTS_ACCESS_TOKEN'] ?? $env('OVH_AI_ENDPOINTS_ACCESS_TOKEN'),
```
`$env` jest zadeklarowane jako `$env = null` na linii 5. Gdy `$_ENV` nie zawiera zmiennej (np. w Dockerze przy braku `.env` lub gdy zmienna nie jest załadowana), PHP spróbuje wykonać `null(...)` co rzuci `TypeError` (`null is not callable`).

**Co zrobić:** Zamienić na podwójne odwołanie do `$_ENV`:
```php
'token' => $_ENV['OVH_AI_ENDPOINTS_ACCESS_TOKEN'] ?? 'no-token-set',
```
Albo dodać helper funkcyjny, który bezpiecznie odczytuje z environment.

---

### 2. Testy `AuthServiceTest` — niezdefiniowana zmienna `$input`

**Plik:** `tests/AuthServiceTest.php`  
**Linie:** 88 i 104  
**Problem:**  
W `testLoginValidCredentials()` i `testLoginInvalidCredentials()` zmienna została nazwana `$registerInput`, ale przy wywołaniu użyto starej nazwy `$input`:
```php
$registerInput = new RegisterInput('test@example.com', 'password123');
$this->authService->register($input);   // ← błąd: $input nie istnieje
```

**Co zrobić:** Zamienić `$input` na `$registerInput` w liniach 88 i 104.

---

### 3. Testy `TaskServiceTest` — `TaskResult` traktowany jak tablica, a to DTO

**Plik:** `tests/TaskServiceTest.php`  
**Problem:** Metody serwisu zwracają obiekt `TaskResult` (z `public int $id`, `public string $title` itd.), ale testy odnoszą się do wyniku przez `$result['id']`, `$result['title']` itd.

Dotyczy wszystkich testów oprócz `testGetUserTasks`:
- `testCreateTask` (linia 68): `$result['id']` → `$result->id`
- `testGetTaskById` (linia 93): `$result['id']` → `$result->id`
- `testUpdateTask` (linia 129): `$result['id']` → `$result->id`
- `testDeleteTask` (linia 147): `$result['id']` → `$result->id`
- `testDeleteTaskWrongUser` (linia 179): `$result['id']` → `$result->id`

Dodatkowo w `testGetUserTasks` (linia 84): `$tasks[0]['title']` → `$tasks[0]->title`

**Co zrobić:** Wszędzie, gdzie testy używają `$result['klucz']` na obiekcie `TaskResult`, zmienić na `$result->klucz`.

---

### 4. Testy `TaskServiceTest` — metoda `getTaskById()` nie istnieje

**Plik:** `tests/TaskServiceTest.php`  
**Linie:** 93, 104, 121  
**Problem:** Testy `testGetTaskById`, `testGetTaskByIdNotFound`, `testGetTaskByIdWrongUser` wołają `$this->taskService->getTaskById($id, $userId)`, ale `TaskService` nie ma takiej metody. Jest tylko `getUserTasks($userId)`, która zwraca wszystkie zadania użytkownika.

**Co zrobić (dwie opcje):**
- **A)** Dodać metodę `getTaskById(int $taskId, int $userId): TaskResult` do `TaskService.php` (analogicznie do `deleteTask` — pobiera z repo, sprawdza ownership, zwraca).  
- **B)** Przepisać testy, żeby pobierały zadanie przez `getUserTasks()` i filtrowały po ID.

---

### 5. Mockowanie `final` klas w PHPUnit — konfiguracja `dg/bypass-finals`

**Problem:** PHPUnit 11.5 domyślnie nie pozwala mockować klas `final`. Wszystkie testy Auth, Chat i ChatModels padają z błędem:
```
ClassIsFinalException: Class "App\Infrastructure\Mail\MailService" is declared "final" and cannot be doubled
ClassIsFinalException: Class "App\Infrastructure\AI\OvhAiClient" is declared "final" and cannot be doubled
```
To dotyczy **18 z 30 testów**.

**Co zrobić (dwie opcje):**
- **A) (zalecane)** Dodać do `phpunit.xml` konfigurację `dg/bypass-finals`:
  ```xml
  <extensions>
      <bootstrap class="DG\BypassFinals\BypassFinals"/>
  </extensions>
  ```
  Potem: `composer require --dev dg/bypass-finals`
  
- **B)** Usunąć słowo `final` z klas `OvhAiClient`, `MailService` i `ChatService` (mniej zalecane, osłabia architekturę).

---

## 🟡 Priorytet MEDIUM — ostrzeżenia i ulepszenia

### 6. `OVH_AI_TIMEOUT` nieużywany — timeout zahardcodowany

**Pliki:** `.env.example:16` oraz `src/Infrastructure/AI/OvhAiClient.php:22`  
**Problem:** `.env.example` definiuje `OVH_AI_TIMEOUT=60`, ale `OvhAiClient` ma timeout zahardcodowany na `120` sekund:
```php
$this->httpClient = new Client([
    'base_uri' => $url,
    'timeout' => 120,   // ← zahardcodowane
]);
```

**Co zrobić:**  
1. Dodać `'ovh_ai_timeout' => (int)($_ENV['OVH_AI_TIMEOUT'] ?? 60)` do `src/settings.php` w sekcji `ovh_ai`.  
2. Przekazać timeout przez konstruktor `OvhAiClient` (dodać parametr `int $timeout = 120`).  
3. Zaktualizować definicję DI w `src/dependencies.php`.

---

### 7. Ciasteczko sesyjne — `secure: false`, `httponly: false`

**Plik:** `src/Controller/AuthController.php:47`  
**Problem:** Token sesji zapisywany w ciasteczku z `secure: false` (wysyłane też przez HTTP) i `httponly: false` (czytelne przez JavaScript). Na produkcji to zagrożenie — token może być przechwycony przez MITM lub XSS.

**Co zrobić:**  
1. Ustawić `'secure' => $_ENV['APP_ENV'] === 'prod'`.  
2. Ustawić `'httponly' => true` (chyba że JS faktycznie potrzebuje czytać cookie — wtedy udokumentować dlaczego).  
3. Rozważyć dodanie `'samesite' => 'Strict'` dla produkcji.

---

### 8. Duplikaty pliku `przykazaniaAi.md`

**Pliki:** `.devin/przykazaniaAi.md`, `.junie/przykazaniaAi.md`, `.local/przykazaniaAi.md`  
**Problem:** Trzy identyczne kopie tego samego pliku 66-linijkowego.

**Co zrobić:** Zostawić jeden (np. `.local/przykazaniaAi.md`), usunąć pozostałe. Jeśli `.junie/` i `.devin/` są używane przez różne narzędzia AI, utworzyć symlinki.

---

## ⚪ Priorytet LOW — do rozważenia

### 9. Brak CI/CD

Nie ma żadnego pipeline'u (GitHub Actions, GitLab CI itp.). Testy trzeba odpalać ręcznie przez `composer test`.

**Propozycja:** Dodać `.github/workflows/test.yml` z uruchomieniem `composer test` na PHP 8.4.

### 10. `ChatService::addMessage()` — zdublowane odczyty bazy

**Plik:** `src/Service/ChatService.php:95-119`  
W `addMessage()`, wiadomość użytkownika jest zapisywana, potem odczytywana historia (wraz z tą samą wiadomością) i wysyłana do AI. Można uniknąć drugiego SELECT-a przekazując historię z `$messages` wcześniej.

### 11. `ChatService::getAvailableModels()` — ryzyko timeoutu

**Plik:** `src/Service/ChatService.php:152`  
Tworzy nowy `GuzzleHttp\Client` bez timeoutu — zapytanie do OVH API może wisieć w nieskończoność. Należy ustawić timeout.

### 12. Plik PNG w root repozytorium

**Plik:** `Zrzut ekranu z 2026-06-14 17-17-28.png`  
Screenshot w głównym katalogu. Do usunięcia lub przeniesienia do `docs/assets/`.

---

## 📋 Kolejność wykonywania

1. ✅ **Najpierw** — zadania #1–#5 (krytyczne, blokują testy)  
2. ✅ **Potem** — zadania #6–#8 (ostrzeżenia bezpieczeństwa / konfiguracji)  
3. ✅ **Na koniec** — #9–#12 (niski priorytet, nice-to-have)  
