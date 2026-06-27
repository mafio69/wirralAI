# Poprawki UI JSON - 2026-06-14

## Problem
Użytkownik zgłosił, że:
1. Po wejściu na stronę przycisk JSON był "wyszarzony" (mniej widoczny)
2. Zapis z aplikacji w JSON był widoczny domyślnie
3. To było niepoprawne zachowanie

## Oczekiwane zachowanie
1. Po wejściu na stronę chat przycisk JSON powinien być widoczny (zielony/aktywny)
2. JSON powinien być ukryty domyślnie
3. Użytkownik musi ręcznie włączyć wyświetlanie JSON

## ✅ Zaimplementowane poprawki

### 1. Naprawienie domyślnego stanu JSON
**Zmiana w funkcji `showJsonResponse()`:**
- **Przed**: Automatycznie ustawiała `showJson = true` i pokazywała JSON
- **Po**: Tylko pokazuje JSON jeśli `showJson` jest już `true`
- **Efekt**: JSON nie jest automatycznie pokazywany przy operacjach API

```javascript
// PRZED
function showJsonResponse(data) {
  if (!showJson) {
    showJson = true;  // ❌ Automatycznie włączała
    container.style.display = 'block';
  }
  jsonResult.textContent = ...;
}

// PO
function showJsonResponse(data) {
  if (showJson) {  // ✅ Tylko jeśli użytkownik włączył
    container.style.display = 'block';
    jsonResult.textContent = ...;
  } else {
    container.style.display = 'none';
    jsonResult.textContent = '';
  }
}
```

### 2. Poprawienie koloru przycisku JSON
**Zmiana w HTML i CSS:**
- **Przed**: Przycisk miał klasę `btn-outline` (szary/less visible)
- **Po**: Przycisk ma klasę `btn-primary` (zielony/widoczny)
- **Efekt**: Przycisk jest bardziej widoczny i aktywny

```html
<!-- PRZED -->
<button class="btn-outline btn-sm" onclick="toggleAllJson()">Ukryj/Pokaż JSON</button>

<!-- PO -->
<button class="btn-primary btn-sm" onclick="toggleAllJson()">Pokaż JSON</button>
```

### 3. Dynamiczna zmiana stanu przycisku
**Zmiana w funkcji `toggleAllJson()`:**
- Dodano logiczną zmianę tekstu i koloru przycisku
- **Pokaż JSON**: zielony przycisk (`btn-primary`)
- **Ukryj JSON**: czerwony przycisk (`btn-danger`)

```javascript
function toggleAllJson() {
  showJson = !showJson;
  
  if (showJson) {
    button.textContent = 'Ukryj JSON';
    button.classList.remove('btn-primary');
    button.classList.add('btn-danger');  // Czerwony gdy aktywne
  } else {
    button.textContent = 'Pokaż JSON';
    button.classList.remove('btn-danger');
    button.classList.add('btn-primary');  // Zielony gdy nieaktywne
  }
  
  container.style.display = showJson ? 'block' : 'none';
}
```

### 4. Resetowanie stanu przy otwarciu/zamknięciu czatu
**Zmiany w funkcjach `openChat()` i `closeChatDetail()`:**
- Każde otwarcie czatu resetuje stan JSON do ukrytego
- Zamknięcie czatu również resetuje stan
- Przycisk wraca do stanu początkowego

```javascript
// W openChat()
async function openChat(id) {
  // Reset JSON state when opening chat
  showJson = false;
  document.getElementById('json-container').style.display = 'none';
  document.getElementById('chat-json-result').textContent = '';
  
  // Reset button text
  const button = document.querySelector('button[onclick="toggleAllJson()"]');
  button.textContent = 'Pokaż JSON';
  button.classList.remove('btn-danger');
  button.classList.add('btn-primary');
  
  // ... reszta funkcji
}

// W closeChatDetail()
function closeChatDetail() {
  // Reset JSON state when closing chat
  showJson = false;
  document.getElementById('json-container').style.display = 'none';
  document.getElementById('chat-json-result').textContent = '';
  chatHistory = [];
}
```

## 🎯 Obecne zachowanie

### Przy wejściu na stronę chat:
1. ✅ Przycisk "Pokaż JSON" jest zielony (widoczny)
2. ✅ JSON jest ukryty (container `display: none`)
3. ✅ Brak widocznych danych JSON

### Po kliknięciu "Pokaż JSON":
1. ✅ Przycisk zmienia się na czerwony "Ukryj JSON"
2. ✅ JSON container się pokazuje
3. ✅ Przyszłe odpowiedzi API będą wyświetlane w JSON

### Po kliknięciu "Ukryj JSON":
1. ✅ Przycisk zmienia się na zielony "Pokaż JSON"
2. ✅ JSON container się ukrywa
3. ✅ Dane JSON są czyszczone

### Przy otwarciu innego czatu:
1. ✅ Stan JSON jest resetowany do ukrytego
2. ✅ Przycisk wraca do "Pokaż JSON" (zielony)
3. ✅ Historia jest czyszczona

## 📋 Zmienione pliki

- `public/ui/index.html` - wszystkie poprawki w UI i JavaScript

## ✅ Status: POPRAWKI ZAKOŃCZONE

Teraz zachowanie jest zgodne z oczekiwaniami:
- JSON domyślnie ukryty
- Przycisk jest widoczny i ma odpowiedni kolor
- Użytkownik ma pełną kontrolę nad wyświetlaniem JSON
- Stan jest poprawnie resetowany przy operacjach na czatach