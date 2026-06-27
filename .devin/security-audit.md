# Audit Bezpieczeństwa - WirralAI

## Data analizy: 2026-06-14

## ✅ **Mocne strony:**

1. **Ochrona przed SQL injection**: Wszystkie zapytania do bazy danych używają prepared statements z parametrami
2. **Bezpieczne przechowywanie haseł**: Używa `password_hash()` i `password_verify()` z algorytmem PASSWORD_DEFAULT
3. **Walidacja danych wejściowych**: Mappery walidują dane (format emaila, długość hasła, dozwolone wartości)
4. **Ochrona przed XSS**: Frontend używa funkcji `esc()` do escaping HTML
5. **Konfiguracja .gitignore**: Plik `.env` jest wykluczony z repozytorium
6. **Tokeny sesji**: Używa kryptograficznie bezpiecznych `random_bytes(32)` do generowania tokenów
7. **Weryfikacja email**: Implementuje potwierdzenie adresu email przed aktywnością konta
8. **HTTPS**: Komunikacja z OVH AI API używa HTTPS

## ⚠️ **Krytyczne problemy bezpieczeństwa:**

### 1. Brak middleware autoryzacji w routes.php
- **Lokalizacja**: `src/routes.php`
- **Problem**: Wszystkie endpointy API są publiczne, autoryzacja sprawdzana tylko w kontrolerach
- **Ryzyko**: Możliwe omijanie autoryzacji przez nieuwagę programisty
- **Rozwiązanie**: Dodać middleware autoryzacji dla chronionych endpointów

### 2. Niebezpieczne ciasteczka w AuthController.php
- **Lokalizacja**: `src/Controller/AuthController.php` (linie 44-50)
- **Problem**: 
  - `'secure' => false` - ciasteczko przesyłane przez HTTP (nie HTTPS)
  - `'httponly' => false` - ciasteczko dostępne dla JavaScript (podatność na XSS)
  - `'samesite' => 'Lax'` - powinno być 'Strict' dla lepszej ochrony
- **Rozwiązanie**: Zmienić na:
  ```php
  setcookie('token', $result->token, [
      'expires' => strtotime($result->expiresAt),
      'path' => '/',
      'secure' => true,
      'httponly' => true,
      'samesite' => 'Strict',
  ]);
  ```

### 3. Brak ochrony CSRF
- **Problem**: Brak tokenów CSRF dla operacji zmieniających stan (POST, PUT, DELETE)
- **Ryzyko**: Aplikacja podatna na ataki CSRF
- **Rozwiązanie**: Zaimplementować mechanizm tokenów CSRF

## 🔶 **Średnie ryzyko:**

### 4. Przechowywanie tokenów w localStorage
- **Lokalizacja**: `public/ui/index.html`, `public/ui/landing.html`
- **Problem**: Tokeny autoryzacyjne przechowywane w localStorage (podatne na XSS)
- **Rozwiązanie**: Przenieść tokeny z localStorage do httpOnly cookies

### 5. Brak rate limitingu
- **Problem**: Brak ograniczeń liczby prób logowania
- **Ryzyko**: Możliwe ataki brute-force na endpointy logowania
- **Rozwiązanie**: Dodać rate limiting na endpointy autoryzacji

### 6. Szczegółowe błędy w środowisku deweloperskim
- **Lokalizacja**: `src/middleware.php` (linie 52-55)
- **Problem**: W środowisku dev zwraca stack trace w odpowiedziach HTTP
- **Ryzyko**: Może ujawnić wrażliwe informacje o implementacji
- **Rozwiązanie**: Ograniczyć szczegółowość błędów w środowisku produkcyjnym

### 7. Brak nagłówków bezpieczeństwa HTTP
- **Problem**: Brak nagłówków: Content-Security-Policy, X-Frame-Options, X-Content-Type-Options, HSTS
- **Rozwiązanie**: Dodać nagłówki bezpieczeństwa HTTP w middleware

## 📋 **Rekomendacje:**

### Krytyczne:
1. ✅ Dodać middleware autoryzacji w routes.php dla chronionych endpointów
2. ✅ Zmienić ustawienia ciasteczek: `secure => true`, `httponly => true`, `samesite => 'Strict'`
3. ✅ Zaimplementować ochronę CSRF (tokeny CSRF)

### Wysokie priorytetowo:
4. ✅ Przenieść tokeny z localStorage do httpOnly cookies
5. ✅ Dodać rate limiting na endpointy autoryzacji
6. ✅ Dodać nagłówki bezpieczeństwa HTTP

### Średnie priorytetowo:
7. ✅ Ograniczyć szczegółowość błędów w środowisku produkcyjnym
8. ✅ Dodać logowanie zdarzeń bezpieczeństwa
9. ✅ Zaimplementować wygasanie sesji przy wylogowaniu
10. ✅ Dodać walidację siły hasła (min. 8 znaków, wymagane znaki specjalne)

### Niskie priorytetowo:
11. Dodać CORS policy jeśli API będzie używane przez inne domeny
12. Zaimplementować 2FA (dwuskładnikowe uwierzytelnianie)
13. Dodać audyt logów dla operacji administracyjnych

## 📁 **Powiązane pliki:**
- `src/routes.php` - definicje tras API
- `src/Controller/AuthController.php` - obsługa autoryzacji
- `src/middleware.php` - middleware aplikacji
- `public/ui/index.html` - frontend testowy
- `public/ui/landing.html` - strona lądowania

## 📊 **Ocena ogólna:**
Aplikacja ma solidne podstawy bezpieczeństwa (brak SQL injection, bezpieczne hashowanie), ale wymaga poprawek w obszarze autoryzacji, zarządzania sesjami i ochrony przed atakami webowymi.