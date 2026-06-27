# Poprawka przycisku JSON - finalna wersja

## Data: 2026-06-14

## Problem
Użytkownik zgłosił, że:
1. Zielonego przycisku JSON nie ma być
2. Przycisk JSON ma być tylko w jednym miejscu - na górze w jednej linii z "Odśwież"
3. Szary przycisk JSON = kod niewidoczny na stronie
4. Zielony przycisk JSON = kod JSON widoczny na stronie
5. Domyślnie przycisk ma być szary

## ✅ Zaimplementowane rozwiązanie

### 1. Przycisk JSON tylko na górze
**Lokalizacja**: Sekcja czatów, obok przycisku "Odśwież"
```html
<div class="card">
  <div class="justify-between mb-2">
    <h2 style="margin:0">Czaty</h2>
    <button class="btn-primary btn-sm" onclick="listChats()">Odśwież</button>
    <button class="btn-outline btn-sm" id="toggle-global-json" onclick="toggleAllJson()">JSON</button>
  </div>
```

### 2. Usunięcie przycisków JSON z innych miejsc
- ❌ Usunięto przycisk JSON z dolnej części panelu chatu
- ❌ Usunięto przyciski JSON z kontenera JSON
- ❌ Usunięto stare funkcje: `toggleGlobalJsonView()`, `showCurrentSectionJson()`, `hideCurrentSectionJson()`
- ❌ Usunięto zmienną `globalJsonViewActive`

### 3. Logika kolorów przycisku
**Szary (btn-outline)** = JSON ukryty
**Zielony (btn-primary)** = JSON widoczny

```javascript
function toggleAllJson() {
  showJson = !showJson;
  const container = document.getElementById('json-container');
  const globalButton = document.getElementById('toggle-global-json');
  
  container.style.display = showJson ? 'block' : 'none';
  
  // Update button color: gray (hidden) vs green (visible)
  if (showJson) {
    // JSON is visible - green button
    globalButton.classList.remove('btn-outline');
    globalButton.classList.add('btn-primary');
  } else {
    // JSON is hidden - gray button
    globalButton.classList.remove('btn-primary');
    globalButton.classList.add('btn-outline');
  }
  
  // Update JSON elements visibility
  const jsonElements = document.querySelectorAll('.msg-json');
  jsonElements.forEach(el => {
    el.style.display = showJson ? 'block' : 'none';
  });
}
```

### 4. Domyślny stan
- ✅ `showJson = false` na początku
- ✅ Przycisk ma klasę `btn-outline` (szary)
- ✅ JSON container jest ukryty (`display: none`)
- ✅ Reset do stanu szarego przy otwarciu/zamknięciu czatu

### 5. Resetowanie stanu
**W `openChat()`:**
```javascript
// Reset global button to gray (hidden state)
const globalButton = document.getElementById('toggle-global-json');
globalButton.classList.remove('btn-primary');
globalButton.classList.add('btn-outline');
```

**W `closeChatDetail()`:**
```javascript
// Reset global button to gray (hidden state)
const globalButton = document.getElementById('toggle-global-json');
globalButton.classList.remove('btn-primary');
globalButton.classList.add('btn-outline');
```

## 🎯 Zachowanie

### Przy wejściu na stronę:
1. ✅ Przycisk "JSON" jest szary (btn-outline)
2. ✅ Jest tylko w jednym miejscu - obok "Odśwież"
3. ✅ JSON jest ukryty

### Po kliknięciu przycisku "JSON":
1. ✅ Przycisk zmienia kolor na zielony (btn-primary)
2. ✅ JSON container się pokazuje
3. ✅ API responses są wyświetlane w formacie JSON

### Po ponownym kliknięciu:
1. ✅ Przycisk wraca do szarego (btn-outline)
2. ✅ JSON się ukrywa

### Przy zmianie czatu:
1. ✅ Stan jest resetowany do szarego przycisku
2. ✅ JSON jest ukryty

## 📋 Zmienione pliki

- `public/ui/index.html` - wszystkie poprawki

## ✅ Status: POPRAWKI ZAKOŃCZONE

Teraz przycisk JSON:
- Jest tylko w jednym miejscu (na górze przy "Odśwież")
- Jest szary domyślnie (JSON ukryty)
- Zmienia na zielony gdy JSON jest widoczny
- Poprawnie resetuje stan przy operacjach na czatach