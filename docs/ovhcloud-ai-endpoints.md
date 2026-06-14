# OVHcloud AI Endpoints — Katalog modeli

> Źródło: [ovhcloud.com/pl/public-cloud/ai-endpoints/catalog/](https://www.ovhcloud.com/pl/public-cloud/ai-endpoints/catalog/)  
> Data pobrania: 2026-06-14

---

## Co to jest AI Endpoints?

OVHcloud AI Endpoints to gotowe API do modeli AI — bez potrzeby stawiania własnej infrastruktury GPU. Jako dev podajesz endpoint i klucz API, dostajesz odpowiedź. Kompatybilne ze standardem OpenAI, więc istniejące integracje działają bez przeróbek.

Kluczowe cechy platformy:

- **Standardowe API** — kompatybilne z OpenAI API, drop-in replacement w istniejącym kodzie
- **Zero Data Retention** — OVHcloud nie przechowuje Twoich promptów ani odpowiedzi (tylko dane do fakturowania)
- **SLA 99,5%** dla API
- **Sandbox** — bezpłatne testowanie wszystkich modeli przez UI lub API
- **Odwracalność** — brak vendor lock-in, modele można wdrożyć też na własnej infrastrukturze
- **Ponad 40 modeli** open weights, stale aktualizowanych
- **Suwerenność danych** — infrastruktura OVHcloud w Europie (od 1999)
- **Certyfikaty** — SOC, ochrona danych medycznych

---

## Cennik — jak to działa?

Modele LLM rozliczane są **per milion tokenów** (Mtoken), osobno za wejście i wyjście.  
Modele audio (Whisper) rozliczane są **per sekundę** nagrania.  
Część modeli jest **bezpłatna** (oznaczone jako `Gratis`).

---

## Modele — podział według kategorii

### Large Language Models (LLM)

Ogólnego przeznaczenia — chat, RAG, klasyfikacja, streszczanie, itp.

| Model | Parametry | Kwantyzacja | Cena wejście | Cena wyjście | Licencja |
|-------|-----------|-------------|-------------|-------------|----------|
| Mistral-7B-Instruct-v0.3 | 7B | fp16 | 0,10 €/Mtoken | 0,10 €/Mtoken | Apache 2.0 |
| Mistral-Nemo-Instruct-2407 | 12,2B | fp16 | 0,13 €/Mtoken | 0,13 €/Mtoken | Apache 2.0 |
| Meta-Llama-3_3-70B-Instruct | 70B | fp8 | 0,67 €/Mtoken | 0,67 €/Mtoken | Llama 3.3 Community |

---

### Reasoning LLM

Modele z rozszerzonym trybem myślenia (chain-of-thought, reasoning steps). Lepsze przy zadaniach wieloetapowych, logice, matematyce.

| Model | Parametry | Kwantyzacja | Cena wejście | Cena wyjście | Licencja |
|-------|-----------|-------------|-------------|-------------|----------|
| Qwen3-32B | 32,8B | fp8 | 0,08 €/Mtoken | 0,23 €/Mtoken | Apache 2.0 |
| gpt-oss-20b | 21B | fp4 | 0,04 €/Mtoken | 0,15 €/Mtoken | Apache 2.0 |
| gpt-oss-120b | 117B | fp4 | 0,08 €/Mtoken | 0,40 €/Mtoken | Apache 2.0 |

> **Wsparcie:** Function calling, Reasoning

---

### Visual LLM (Multimodal)

Modele przyjmujące zarówno tekst jak i obrazy (vision). Przydatne do analizy screenshotów, dokumentów, diagramów.

| Model | Parametry | Kwantyzacja | Cena wejście | Cena wyjście | Licencja | Uwagi |
|-------|-----------|-------------|-------------|-------------|----------|-------|
| Qwen3.6-27B | 27B | fp8 | 0,40 €/Mtoken | 2,70 €/Mtoken | Apache 2.0 | Nowość |
| Qwen3.5-397B-A17B | 397B (MoE, aktywne 17B) | fp8 | 0,60 €/Mtoken | 3,60 €/Mtoken | Apache 2.0 | Nowość |
| Qwen3.5-9B | 9,7B | bf16 | 0,10 €/Mtoken | 0,15 €/Mtoken | Apache 2.0 | Nowość |
| Mistral-Small-3.2-24B-Instruct-2506 | 24B | bf16 | 0,09 €/Mtoken | 0,28 €/Mtoken | Apache 2.0 | |
| Qwen2.5-VL-72B-Instruct | 72B | fp8 | 0,91 €/Mtoken | 0,91 €/Mtoken | Qwen | |

> **Wsparcie:** Function calling, Multimodal, Reasoning (model-zależne)

---

### Code LLM

Modele wyspecjalizowane w generowaniu i rozumieniu kodu.

| Model | Parametry | Kwantyzacja | Cena wejście | Cena wyjście | Licencja |
|-------|-----------|-------------|-------------|-------------|----------|
| Qwen3-Coder-30B-A3B-Instruct | 30B (MoE, aktywne 3B) | fp8 | 0,06 €/Mtoken | 0,22 €/Mtoken | Apache 2.0 |

> **Wsparcie:** Function calling, Code Assistant

---

### Embeddings

Modele generujące wektory (embeddingi) — używane do wyszukiwania semantycznego, RAG, klastrowania, podobieństwa tekstów.

| Model | Parametry | Kwantyzacja | Cena wejście | Licencja | Uwagi |
|-------|-----------|-------------|-------------|----------|-------|
| Qwen3-Embedding-8B | 7,6B | fp16 | 0,10 €/Mtoken | Apache 2.0 | Nowość |
| bge-multilingual-gemma2 | 0,567B | fp16 | 0,01 €/Mtoken | Gemma | |
| bge-m3 | 0,567B | fp16 | 0,01 €/Mtoken | MIT | |

> Modele embeddingowe nie mają ceny za wyjście — zwracają wektory, nie tokeny.

---

### Speech to Text (STT)

Transkrypcja audio na tekst. Rozliczane per sekunda nagrania.

| Model | Parametry | Kwantyzacja | Cena | Licencja |
|-------|-----------|-------------|------|----------|
| whisper-large-v3 | 1,54B | fp16 | 0,00004083 €/sek. | Apache 2.0 |
| whisper-large-v3-turbo | 0,81B | fp16 | 0,00001278 €/sek. | Apache 2.0 |

> `turbo` jest ~3x tańszy i wystarczy do większości zastosowań. `large-v3` daje lepszą dokładność przy trudnych nagraniach.

---

### Text to Speech (TTS)

Synteza mowy z tekstu. Wszystkie modele TTS są **bezpłatne**.

| Model | Język | Kwantyzacja | Cena | Licencja |
|-------|-------|-------------|------|----------|
| nvr-tts-en-us | Angielski (US) | fp32 | Gratis | Riva license |
| nvr-tts-de-de | Niemiecki | fp32 | Gratis | Riva license |
| nvr-tts-es-es | Hiszpański | fp32 | Gratis | Riva license |
| nvr-tts-it-it | Włoski | fp32 | Gratis | Riva license |

---

### Image Generation

Generowanie obrazów z tekstu.

| Model | Parametry | Kwantyzacja | Cena | Licencja |
|-------|-----------|-------------|------|----------|
| stable-diffusion-xl-base-v10 | 3,5B | fp32 | Gratis | OpenRail++ |

---

### LLM Guard (moderacja)

Modele do weryfikacji bezpieczeństwa treści — wykrywają szkodliwe wyjście z LLM. Przydatne jako warstwa ochronna w pipeline'ach produkcyjnych.

| Model | Parametry | Kwantyzacja | Cena | Licencja | Status |
|-------|-----------|-------------|------|----------|--------|
| Qwen3Guard-Gen-8B | 8B | fp16 | Gratis | Apache 2.0 | Beta |
| Qwen3Guard-Gen-0.6B | 0,6B | fp16 | Gratis | Apache 2.0 | Beta |

> **Wsparcie:** Moderation

---

## Dla dewelopera — jak zacząć?

### Kompatybilność z OpenAI SDK

API jest w standardzie OpenAI — wystarczy zmienić `base_url`:

```python
from openai import OpenAI

client = OpenAI(
    base_url="https://oai.endpoints.kepler.ai.cloud.ovh.net/v1",
    api_key="TWÓJ_KLUCZ_API"
)
```

```php
// Symfony / Guzzle
$client = new \GuzzleHttp\Client([
    'base_uri' => 'https://oai.endpoints.kepler.ai.cloud.ovh.net/v1/',
    'headers'  => [
        'Authorization' => 'Bearer TWÓJ_KLUCZ_API',
        'Content-Type'  => 'application/json',
    ],
]);
```

### Gdzie przetestować bez kodu?

Sandbox UI dostępny po zalogowaniu do OVHcloud — pozwala wysłać prompty do każdego modelu bez ani jednej linii kodu.

---

## Integracje i ekosystem

OVHcloud współpracuje z kilkoma narzędziami, które możesz podpiąć bezpośrednio do AI Endpoints:

| Narzędzie | Opis |
|-----------|------|
| **Hugging Face** | Platforma ML — modele, datasety, Spaces |
| **LiteLLM** | Proxy łączące 100+ modeli przez jedno API (OpenAI-compatible) |
| **Pydantic AI** | Framework agentowy dla ustrukturyzowanych odpowiedzi (Python) |
| **Pydantic AI Gateway** | Jeden klucz do wszystkich modeli, kontrola budżetu |
| **Continue** (`continue.dev`) | Plugin do VS Code / JetBrains — prywatny asystent kodowania |
| **Kilo Code** | Popularny open-source agent kodowania (extension) |
| **Kilo Code CLI** | Ten sam agent w terminalu |
| **Shell AI (SHAI)** | Asystent OVHcloud dla terminala |
| **OpenCode** | Open-source agent kodowania w terminalu |
| **Apache Airflow** | Orkiestracja workflow i pipeline'ów danych |
| **Mastra** | Framework agentowy w TypeScript |
| **LlamaIndex** | Framework RAG i agentów uwzględniających kontekst |
| **LiveKit** | Framework real-time dla agentów głosowych i wideo |

---

## Przypadki użycia

| Scenariusz | Sugerowane modele |
|------------|-----------------|
| Chatbot / asystent konwersacyjny | Mistral-7B, Qwen3-32B, gpt-oss |
| Asystent kodowania w IDE | Qwen3-Coder-30B, Continue/Kilo Code |
| Analiza obrazów / OCR dokumentów | Qwen3.5-9B, Mistral-Small-3.2 |
| Wyszukiwanie semantyczne / RAG | bge-m3, bge-multilingual-gemma2, Qwen3-Embedding-8B |
| Transkrypcja rozmów / spotkań | whisper-large-v3-turbo |
| Synteza mowy (TTS) | nvr-tts-en-us (i inne języki) |
| Generowanie obrazów | stable-diffusion-xl-base-v10 |
| Moderacja treści w pipeline | Qwen3Guard-Gen-8B / 0.6B |
| Złożone zadania wieloetapowe | gpt-oss-120b, Qwen3-32B |

---

## Kwantyzacja — co oznaczają skróty?

| Symbol | Opis | Kiedy stosowany |
|--------|------|-----------------|
| `fp32` | 32-bit float, pełna precyzja | Mniejsze modele, TTS/Image |
| `fp16` | 16-bit float | Dobry balans jakości i rozmiaru |
| `bf16` | bfloat16, lepsza stabilność od fp16 | Nowsze architektury |
| `fp8` | 8-bit float, mocno skompresowany | Duże modele 30B+ |
| `fp4` | 4-bit float, najbardziej agresywna kompresja | Bardzo duże modele 100B+ |

> Im mniejszy format, tym model zajmuje mniej VRAM i jest tańszy w obsłudze — kosztem drobnej utraty jakości.

---

## Linki

- [Pierwsze kroki z AI Endpoints](https://www.ovhcloud.com/pl/public-cloud/ai-endpoints/)
- [Specyfikacja techniczna](https://www.ovhcloud.com/pl/public-cloud/ai-endpoints/)
- [Tutoriale](https://www.ovhcloud.com/pl/public-cloud/ai-endpoints/)
- [AI Deploy](https://www.ovhcloud.com/pl/public-cloud/ai-deploy/) — własne wdrożenia modeli ML
- [Cloud GPU](https://www.ovhcloud.com/pl/public-cloud/gpu-instances/) — instancje GPU do obliczeń równoległych
- [Data Platform](https://www.ovhcloud.com/pl/public-cloud/data-platform/) — projekty Data & Analytics
- [e-Learning OVHcloud](https://www.ovhcloud.com/pl/learn/) — szkolenia z certyfikatem (Compute, Storage, Network, AI)

---

## Śmieci — dane pominięte (do weryfikacji)

Poniższe rekordy z JSONa nie zawierały treści merytorycznej — głównie duplikaty etykiet kolumn lub puste wpisy ze scraperowego HTML. Sprawdź, czy czegoś tu nie brakuje.

| # | Co to było |
|---|-----------|
| Wszystkie `data7`–`data13` we wszystkich rekordach | Powtórzone nagłówki kolumn: `"Licencja:"`, `"Liczba parametrów:"`, `"Kwantyzacja:"`, `"Wsparcie klienta:"`, `"/Mtoken(wejście)"`, `"/Mtoken(wyjście)"` — artefakty scrapera, nie dane |
| `pagination` we wszystkich rekordach | Puste pole — paginator nie był używany lub strona była pojedyncza |
| `web_scraper_order`, `web_scraper_start_url` | Metadane scrapera, nie zawierają treści |
| Rekord #25 | Ogólny slogan marketingowy: *"Wydajne, bezpieczne i proste w integracji API generatywnej AI"* — nic konkretnego |
| Rekord #60 | Całkowicie pusty |
| Rekord #61 | Link do e-Learning (dodany w sekcji Linki) |
| `data6` w rekordach #41–#53 | Duplikaty nazw integracji z opisami — te same info co `title`, już uwzględnione w tabeli integracji |
| `data5` we wszystkich rekordach | Identyczna treść jak `data2` — dosłowny duplikat z innego elementu DOM |
