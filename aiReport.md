# 📊 تقرير شامل ومفصل: خدمة الذكاء الاصطناعي في تطبيق البلاغات المرورية

> **المشروع:** `traffic_app` — تطبيق بلاغات مرورية (Laravel + Filament)
> **تاريخ التحليل:** 2026-06-26
> **نطاق التقرير:** جميع الملفات والأكواد المتعلقة بخدمة الذكاء الاصطناعي (AI)

---

## فهرس المحتويات

1. [نظرة عامة ومقدمة](#1-نظرة-عامة-ومقدمة)
2. [البنية المعمارية للذكاء الاصطناعي](#2-البنية-المعمارية-للذكاء-الاصطناعي)
3. [مزود خدمة الذكاء الاصطناعي (LiteLLM)](#3-مزود-خدمة-الذكاء-الاصطناعي-litellm)
4. [الإعدادات والتهيئة (Configuration)](#4-الإعدادات-والتهيئة-configuration)
5. [خدمة الاتصال الأساسية — `AiService.php`](#5-خدمة-الاتصال-الأساسية--aiservicephp)
6. [محلل البلاغات — `ReportAiAnalyzer.php`](#6-محلل-البلاغات--reportaianalyzerphp)
7. [التدفّق المعتمد على الأحداث (Event-Driven Flow)](#7-التدفّق-المعتمد-على-الأحداث-event-driven-flow)
8. [قاعدة البيانات والـ Schema](#8-قاعدة-البيانات-وال-schema)
9. [نموذج البيانات — `Report.php`](#9-نموذج-البيانات--reportphp)
10. [واجهة العرض — Filament Resources](#10-واجهة-العرض--filament-resources)
11. [الميزات الست الرئيسية للذكاء الاصطناعي](#11-الميزات-الست-الرئيسية-للذكاء-الاصطناعي)
12. [البرومبتات (Prompts) المُستخدمة](#12-البرومبتات-prompts-المُستخدمة)
13. [معالجة الأخطاء والمتانة](#13-معالجة-الأخطاء-والمتانة)
14. [الاختبارات (Tests)](#14-الاختبارات-tests)
15. [خريطة الملفات الكاملة](#15-خريطة-الملفات-الكاملة)
16. [التقييم ونقاط القوة والضعف](#16-التقييم-ونقاط-القوة-والضعف)

---

## 1. نظرة عامة ومقدمة

دمج هذا المشروع نظام ذكاء اصطناعي متكامل هدفه **تحليل البلاغات المرورية تلقائياً** فور إنشائها من قبل المواطنين، دون أي تدخل بشري في مرحلة التحليل الأولي. يقوم النظام بمعالجة البلاغات متعددة الوسائط (نص + صور + فيديو) وإنتاج تقييم منظم يساعد الإدارة والشرطة على ترتيب الأولويات واتخاذ القرار.

### الأهداف الوظيفية للنظام (حسب `ai.md`):

| # | الميزة | الوصف |
|---|--------|-------|
| 1 | **التعرف على لوحات المركبات (ALPR)** | استخراج رقم اللوحة تلقائياً من صور البلاغ |
| 2 | **تصنيف نوع الحادث** | تحديد نوع الحادث (تصادم، انقلاب، مخالفة...) |
| 3 | **تقييم الخطورة** | درجة خطورة (1–5) لتسهيل ترتيب الأولويات + تقييم الأضرار |
| 4 | **كشف البلاغات المكررة** | مقارنة البلاغات الجديدة بالقائمة حسب الموقع والتوقيت |

### ميزات إضافية مُنفّذة (تتجاوز المواصفات الأصلية):
- ✅ **كشف الصور/الفيديوهات المولّدة بالذكاء الاصطناعي (Deepfake)** والرفض التلقائي للبلاغات المفبركة.
- ✅ **التقييم التفصيلي للأضرار** من الفيديو والصورة.
- ✅ **ملخص ذكي شامل** لكل بلاغ.

---

## 2. البنية المعمارية للذكاء الاصطناعي

```
مواطن يُنشئ بلاغاً (صورة/فيديو/نص)
        │
        ▼
ReportRoutingService::createReport()          ← يُنشئ البلاغ في DB
        │
        │  ReportCreated::dispatch($report)   ← إطلاق الحدث
        ▼
[طابور غير متزامن / Queue]
        │
        ▼
AnalyzeReportWithAi (Listener)                ← implements ShouldQueue
        │
        ▼
ReportAiAnalyzer::analyze()                   ← المنطق التحليلي
        │
        ├── buildContentParts()               ← تجميع الصورة/الفيديو
        ├── runComprehensiveAnalysis()        ← استدعاء AI #1 (تحليل شامل)
        ├── checkDuplicate()                  ← فلتر جغرافي + استدعاء AI #2 (تأكيد التكرار)
        └── parseJsonResponse()               ← تحليل الاستجابة JSON
        │
        ▼
AiService::chat()                             ← طبقة الاتصال بـ HTTP
        │
        │  POST + headers: x-litellm-api-key
        ▼
LiteLLM Proxy (api.abdalgani.com)             ← بوابة موحّدة
        │
        ▼
نموذج Gemini (Google)                          ← المزود الفعلي للذكاء الاصطناعي
        │
        ▼
تحديث أعمدة ai_* في جدول reports              ← حفظ النتائج
        │
        ▼
عرض النتائج في Filament (Admin + Police)      ← واجهة المستخدم
```

**المبادئ التصميمية:**
- **فصل المسؤوليات (Separation of Concerns):** طبقة اتصال (`AiService`) منفصلة عن منطق التحليل (`ReportAiAnalyzer`) منفصلة عن التشغيل (`Listener`).
- **التنفيذ غير المتزامن (Asynchronous):** التحليل يجري في الطابور (Queue) فلا يُبطئ إنشاء البلاغ.
- **المتانة (Resilience):** فشل الذكاء الاصطناعي لا يكسر تدفّق إنشاء البلاغ أبداً.

---

## 3. مزود خدمة الذكاء الاصطناعي (LiteLLM)

النظام لا يستدعي Google Gemini مباشرةً، بل يمر عبر **بوابة LiteLLM Proxy** موحّدة.

### لماذا LiteLLM؟
- يوفّر **واجهة متوافقة مع OpenAI API** مهما كان المزود الخلفي (Gemini, GLM, DeepSeek, Qwen...).
- يسمح بتغيير المزود/النموذج بمجرد تعديل متغير البيئة دون تغيير الكود.
- يوحّد المصادقة عبر ترويسة `x-litellm-api-key`.

### نقطة النهاية (Endpoint):
```
https://api.abdalgani.com/openai/deployments/gemini-3-flash-preview/chat/completions
```

### النموذج الافتراضي:
- **`gemini-3-flash-preview`** (Google Gemini) — سريع، يدعم الوسائط المتعددة (نص/صورة/فيديو).

### المزودات والنماذج المتاحة عبر البوابة (حسب `aiConnect.md`):
| الفئة | أمثلة على النماذج |
|------|------------------|
| Google Gemini | `gemini-3-flash-preview`, `gemini-3.1-pro`, `gemini-2.5-pro` |
| Zhipu GLM | `glm-5.1`, `glm-5-turbo`, `glm-4.7` |
| DeepSeek | `deepseek-v4-pro`, `deepseek-v4-flash`, `deepseek-r1` |
| Qwen (Alibaba) | `qwen3.5`, `nvidia/qwen3-coder-480b` |
| Kimi / MiniMax / Nemotron | `kimi-k2.5`, `minimax-m2.7`, `nvidia/nemotron-ultra-253b` |

> **ملاحظة:** النظام الحالي يستخدم Gemini لأنه يدعم الصور والفيديو (Multimodal)، وهي قدرة جوهرية لتطبيق البلاغات المرورية.

---

## 4. الإعدادات والتهيئة (Configuration)

### الملف: `config/ai.php`
```php
return [
    'api_url'     => env('AI_API_URL'),           // رابط LiteLLM proxy
    'api_key'     => env('AI_API_KEY'),           // مفتاح المصادقة
    'model'       => env('AI_MODEL', 'gemini-3-flash-preview'),  // النموذج
    'max_tokens'  => env('AI_MAX_TOKENS', 4096),  // الحد الأقصى للرموز
    'temperature' => env('AI_TEMPERATURE', 0.3),  // درجة العشوائية
];
```

### متغيرات البيئة المطلوبة (`.env`):
| المتغير | الوصف | القيمة الافتراضية |
|---------|-------|-------------------|
| `AI_API_URL` | رابط البوابة | لا يوجد (إلزامي) |
| `AI_API_KEY` | مفتاح المصادقة | لا يوجد (إلزامي) |
| `AI_MODEL` | معرّف النموذج | `gemini-3-flash-preview` |
| `AI_MAX_TOKENS` | حد الرموز | `4096` |
| `AI_TEMPERATURE` | درجة العشوائية | `0.3` |

> **ملاحظة:** متغيرات الذكاء الاصطناعي **غير موجودة** في ملف `.env.example` ويجب إضافتها يدوياً للنشر.

---

## 5. خدمة الاتصال الأساسية — `AiService.php`

**المسار:** `app/Services/AiService.php` (138 سطراً)

هذه الطبقة مسؤولة حصراً عن **الاتصال بـ HTTP** مع البوابة. وهي لا تعرف شيئاً عن البلاغات — تبقى عامة وقابلة لإعادة الاستخدام.

### الخصائص (Properties):
```php
private string $apiUrl;
private string $apiKey;
private string $model;
private int $maxTokens;
private float $temperature;
```
تُهيّأ كلها من `config('ai.*')` في الـ constructor.

### الدوال الرئيسية:

#### `chat(array $messages, ?float $temperature = null): ?string`
القلب النابض للخدمة — يُرسل طلب محادثة بصيغة متوافقة مع OpenAI:

```php
$response = Http::timeout(60)
    ->withHeaders([
        'x-litellm-api-key' => $this->apiKey,     // ← المصادقة
        'Content-Type' => 'application/json',
    ])
    ->post($this->apiUrl, [
        'model' => $this->model,
        'messages' => $messages,
        'max_tokens' => $this->maxTokens,
        'temperature' => $temperature ?? $this->temperature,
    ]);
```
- مهلة (timeout) قدرها **60 ثانية**.
- يَستخرج النص عبر `$response->json('choices.0.message.content')`.
- عند الفشل: يسجّل الخطأ في السجل ويعيد `null`.

#### `buildImageContent(string $storagePath): ?array`
يحوّل صورة من قرص التخزين `public` إلى **Data URI مُرمّز base64**:
```php
[
    'type' => 'image_url',
    'image_url' => [
        'url' => "data:image/jpeg;base64,...",
    ],
]
```
- يكتشف نوع MIME تلقائياً.
- يتحقق من وجود الملف ويعيد `null` عند غيابه.

#### `buildVideoContent(string $storagePath): ?array`
يحوّل الفيديو إلى base64 بنفس الصيغة. **حجر الأمان:** يتخطى الفيديوهات الأكبر من **20 ميجابايت** لتجنّب مشاكل الذاكرة.

---

## 6. محلل البلاغات — `ReportAiAnalyzer.php`

**المسار:** `app/Services/ReportAiAnalyzer.php` (295 سطراً)

هذا هو **العقل التحليلي** للنظام. يحوّل البلاغ الخام إلى تقييم منظم.

### `analyze(Report $report): void` — نقطة الدخول
تدفّق التنفيذ:
1. يجمع أجزاء الوسائط (صورة/فيديو) عبر `buildContentParts()`.
2. يتخطّي التحليل إذا لم يوجد محتوى (لا وصف ولا وسائط).
3. يستدعي `runComprehensiveAnalysis()` للحصول على تحليل شامل واحد.
4. **يكتشف البلاغات المفبركة بالذكاء الاصطناعي** → إذا كان `is_ai_generated === true`:
   - يضيف علامة `❌ تم الرفض` إلى الملخص.
   - **يغيّر الحالة إلى `Rejected` تلقائياً.**
5. يبني مصفوفة التحديث مع تقييد درجة الخطورة بين 1 و5: `max(1, min(5, ...))`.
6. يستدعي `checkDuplicate()` لكشف التكرار.
7. يُحدّث البلاغ عبر `$report->update($updateData)`.

```php
$updateData = [
    'ai_detected_plate'     => $analysisResult['detected_plate'] ?? null,
    'ai_incident_type'      => $analysisResult['incident_type'] ?? null,
    'ai_severity_score'     => max(1, min(5, (int) $analysisResult['severity_score'])),
    'ai_damage_assessment'  => $analysisResult['damage_assessment'] ?? null,
    'ai_summary'            => $analysisResult['summary'] ?? null,
    'ai_analyzed_at'        => now(),
];
```

### `runComprehensiveAnalysis()` — الاستدعاء التحليلي #1
يبني رسالة متعددة الوسائط:
- **رسالة system (عربية):** تُعرّف دور النموذج كـ «محلل بلاغات مرورية متخصص» وتلزمه بإخراج JSON فقط.
- **رسالة user:** تحتوي على البرومبت النصي + أجزاء الصورة/الفيديو.
- درجة الحرارة = **0.2** (منخفضة لإخراج دقيق/تحليلي).

### `checkDuplicate()` — كشف التكرار (هجين جغرافي + ذكاء اصطناعي)
نهج من مرحلتين **لتقليل تكلفة استدعاءات الذكاء الاصطناعي:**

**المرحلة 1 — فلتر جغرافي سريع (SQL):**
```php
Report::where('id', '!=', $report->id)
    ->where('report_type', $report->report_type)
    ->where('created_at', '>=', now()->subHours(24))   // آخر 24 ساعة
    ->whereBetween('latitude', [...±0.0045])            // ~500 متر
    ->whereBetween('longitude', [...±0.0045])
    ->orderBy('created_at', 'asc')
    ->first();
```

**المرحلة 2 — تأكيد ذكي:** فقط عند وجود مرشّح جغرافي، يُستدعى `confirmDuplicateWithAi()` ليقرّر النموذج ما إذا كان البلاغان يصفان الحادثة نفسها.

### `confirmDuplicateWithAi()` — الاستدعاء #2
برومبت عربي يقارن البلاغين (النوع/الوصف/الموقع/التاريخ) ويطلب JSON: `{"is_duplicate": true|false}`. درجة الحرارة = **0.1** (أدنى لقرار حاسم).

### `parseJsonResponse()` — مُحلّل JSON المتسامح
ينظّف الاستجابة من أغلفة markdown الشائعة التي يضيفها النموذج أحياناً:
```php
$cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
$cleaned = preg_replace('/\s*```$/i', '', $cleaned);
```
ثم `json_decode` مع تسجيل تحذير عند فشل التحليل.

---

## 7. التدفّق المعتمد على الأحداث (Event-Driven Flow)

### الحدث: `ReportCreated` (`app/Events/ReportCreated.php`)
حدث بسيط يحمل `Report` كخاصية للقراءة فقط.

### من يُطلق الحدث؟
**`ReportRoutingService::createReport()`** (السطر 55):
```php
$report = DB::transaction(...);
ReportCreated::dispatch($report);
```
هذه الخدمة هي المسؤولة عن إنشاء البلاغ وتحديد القسم المُختص (شرطة مرور/دوريات طرق/شرطة محلية) حسب نوع البلاغ ونوع الموقع.

### المستمع: `AnalyzeReportWithAi` (`app/Listeners/AnalyzeReportWithAi.php`)
```php
class AnalyzeReportWithAi implements ShouldQueue   // ← تنفيذ غير متزامن
```
- يتخطّى البلاغات التي لا تحتوي على أي محتوى (وصف/صورة/فيديو).
- **يبتلع الاستثناءات** (try/catch) فلا يكسر تدفّق إنشاء البلاغ أبداً.
- يُكتشف **تلقائياً** عبر آلية Laravel لاكتشاف الأحداث من مجلد `app/Listeners` (لا حاجة لتسجيل يدوي — راجع التعليق في `AppServiceProvider.php` السطر 18–20 الذي يحذّر من التسجيل المزدوج).

### لماذا `ShouldQueue`؟
لأن استدعاء الذكاء الاصطناعي قد يستغرق عشرات الثواني (مهلة 60s)، فالتنفيذ غير المتزامن يحفظ استجابة إنشاء البلاغ سريعة. يتطلب هذا إعداد `QUEUE_CONNECTION` (حالياً `database` في `.env.example`).

---

## 8. قاعدة البيانات والـ Schema

### الملف: `database/migrations/2026_06_23_105751_add_ai_analysis_to_reports_table.php`

يضيف **8 أعمدة** إلى جدول `reports`:

| العمود | النوع | القيمة الافتراضية | الوصف |
|--------|------|-------------------|-------|
| `ai_detected_plate` | `string` (nullable) | NULL | رقم اللوحة المُكتشف |
| `ai_incident_type` | `string` (nullable) | NULL | نوع الحادث المُكتشف |
| `ai_severity_score` | `tinyInteger` (nullable) | NULL | درجة الخطورة (1–5) |
| `ai_damage_assessment` | `text` (nullable) | NULL | تقييم الأضرار |
| `ai_summary` | `text` (nullable) | NULL | ملخص التحليل |
| `ai_is_duplicate` | `boolean` | `false` | هل البلاغ مكرر؟ |
| `ai_duplicate_of` | `foreignId` (nullable) | NULL | FK → `reports.id` (مع `nullOnDelete`) |
| `ai_analyzed_at` | `timestamp` (nullable) | NULL | وقت التحليل |

> العلاقة `ai_duplicate_of` مرجع ذاتي (Self-referencing) إلى جدول `reports` نفسه، مع حذف cascading (يصبح NULL عند حذف البلاغ الأصلي).

---

## 9. نموذج البيانات — `Report.php`

**المسار:** `app/Models/Report.php`

### الأعمدة القابلة للتعبئة (`$fillable`):
تتضمن جميع أعمدة `ai_*` الثمانية.

### التحويلات (Casts) المُطبَّقة على حقول الذكاء الاصطناعي:
```php
'ai_is_duplicate'  => 'boolean',
'ai_severity_score'=> 'integer',
'ai_analyzed_at'   => 'datetime',
```

### العلاقة:
```php
public function duplicateOf(): BelongsTo
{
    return $this->belongsTo(Report::class, 'ai_duplicate_of');
}
```
تتيح الوصول للبلاغ الأصلي الذي يتكرر منه البلاغ الحالي (مثلاً `$report->duplicateOf->id`).

---

## 10. واجهة العرض — Filament Resources

تظهر نتائج الذكاء الاصطناعي في **لوحتين** بصفات (Sections) مخصّصة:

### أ) لوحة الإدارة — `ReportResource` (Admin)
**السطر 115–166** يحتوي قسم **«تحليل الذكاء الاصطناعي»**:
- **درجة الخطورة:** شريط تقدّم ملوّن (أخضر→أحمر) مع نص `X/5` ووصف.
  - الألوان: `1=#10b981`, `2=#22d3ee`, `3=#f59e0b`, `4=#f97316`, `5=#ef4444`.
- **رقم اللوحة:** شارة بخلفية ملوّنة وخط monospace بارز.
- **نوع الحادث:** مع ترجمة لغوية عبر خريطة (`accident`→«حادث»...).
- **تقييم الأضرار:** نص كامل.
- **الملخص:** نص كامل.
- **مكرر؟:** علامة ✓ خضراء (لا) أو ⚠ حمراء (نعم) مع **رابط للبلاغ الأصلي**.
- **تاريخ التحليل:** بصيغة `Y/m/d h:i A`.
- القسم **قابل للطي (collapsible)**.

### ب) لوحة الشرطة — `AssignedReportResource` (Police)
**السطر 79–130:** نفس قسم التحليل بنفس المكوّنات تقريباً (مع تكييف الروابط لمسار الشرطة).

### الجداول (Tables):
كلتا اللوحتين تعرضان عمودين للذكاء الاصطناعي:
- `ai_severity_score`: شارة (badge) ملوّنة حسب الدرجة، بصيغة `X/5`، قابلة للفرز والإخفاء.
- `ai_is_duplicate`: أيقونة منطقية (✓ / ⚠).

### زر «إعادة التحليل الذكي» (Re-analyze):
موجود في كلتا الصفحتين (`ViewReport.php` و`ViewAssignedReport.php`):
```php
Action::make('reanalyze')
    ->label(__('messages.ai_reanalyze'))
    ->icon('heroicon-o-cpu-chip')
    ->requiresConfirmation()
    ->action(function (ReportAiAnalyzer $analyzer) {
        $analyzer->analyze($this->record);
        // ← إشعار + تحديث الحقول في الواجهة
    });
```
يسمح للمستخدم بإعادة تشغيل التحليل يدوياً عبر حقن `ReportAiAnalyzer` (Dependency Injection).

### دعم التعدد اللغوي:
ملفات اللغة (`lang/ar/messages.php` و`lang/en/messages.php`) تحتوي مفاتيح موحّدة (`ai_analysis`, `ai_severity_score`, `ai_reanalyze`...) بدعم كامل للعربية والإنجليزية.

---

## 11. الميزات الست الرئيسية للذكاء الاصطناعي

| # | الميزة | كيف تتحقق | المخرَج |
|---|--------|----------|---------|
| 1 | **التعرف على اللوحات (ALPR)** | النموذج البصري يحلّل الصورة ويستخرج النص | `ai_detected_plate` |
| 2 | **تصنيف نوع الحادث** | النموذج يصنّف ضمن: `accident/hazard/traffic_jam/security_threat` | `ai_incident_type` |
| 3 | **تقييم الخطورة** | النموذج يعطي درجة 1–5 (تُقيَّد بـ `max/min`) | `ai_severity_score` |
| 4 | **تقييم الأضرار** | تحليل بصري للأضرار المرئية في الصورة/الفيديو | `ai_damage_assessment` |
| 5 | **كشف التكرار** | فلتر جغرافي (500م/24س) + تأكيد AI | `ai_is_duplicate`, `ai_duplicate_of` |
| 6 | **كشف التزييف (Deepfake)** | النموذج يفحص إن كانت الوسائط مولّدة بالـ AI → **رفض تلقائي** | `status=Rejected` + ملخص مُعلَّم |
| — | **الملخص الشامل** | توليد نص شامل (ماذا/أين/خطورة/إجراء موصى به) | `ai_summary` |

---

## 12. البرومبتات (Prompts) المُستخدمة

### البرومبت التحليلي (Arabic System + User):
**System:**
> «أنت محلل بلاغات مرورية متخصص. تقوم بتحليل البلاغات المرورية وتقديم تقييم شامل. أجب دائماً بتنسيق JSON فقط بدون أي نص إضافي أو markdown.»

**User:** يحدّد نوع البلاغ، الوصف، الموقع، و—إن وُجدت مرفقات— يطلب:
- استخراج رقم اللوحة، نوع الحادث الفعلي، الأضرار المرئية.
- **التحقق بدقة عالية من كون الصورة مولّدة بالـ AI (Deepfake)** والبحث عن التشوهات الشائعة.

**صيغة JSON المطلوبة:**
```json
{
    "is_ai_generated": false,
    "detected_plate": "رقم اللوحة أو null",
    "incident_type": "accident | hazard | traffic_jam | security_threat",
    "severity_score": 3,
    "damage_assessment": "وصف تفصيلي للأضرار",
    "summary": "ملخص شامل"
}
```

### برومبت كشف التكرار:
> «هل البلاغان التاليان يصفان نفس الحادثة المرورية؟ أجب بـ JSON فقط: `{"is_duplicate": true}` أو `{"is_duplicate": false}`»
يقارن: النوع، الوصف، الموقع، التاريخ.

---

## 13. معالجة الأخطاء والمتانة

النظام مصمَّم ليكون **متساهلاً مع الأخطاء (fault-tolerant):**

| الموقف | السلوك | الموقع |
|--------|--------|--------|
| فشل طلب HTTP | تسجيل خطأ + إعادة `null` | `AiService::chat()` L50–55 |
| استثناء شبكي | catch + تسجيل + `null` | `AiService::chat()` L56–62 |
| غياب ملف الصورة | تسجيل تحذير + `null` | `AiService::buildImageContent()` |
| فيديو أكبر من 20MB | تخطّي + تسجيل معلوماتي | `AiService::buildVideoContent()` |
| استجابة JSON مشوّهة | تسجيل تحذير + `null` | `ReportAiAnalyzer::parseJsonResponse()` |
| فشل التحليل الكامل | catch + تسجيل (لا كسر) | `ReportAiAnalyzer::analyze()` L72–77 |
| فشل في المستمع | catch + تسجيل (لا كسر إنشاء البلاغ) | `AnalyzeReportWithAi::handle()` L29–35 |
| درجة خطورة خارج النطاق | تقييد بين 1 و5 | `ReportAiAnalyzer::analyze()` |
| استجابة مغلَّفة بـ markdown | تنظيف قبل التحليل | `parseJsonResponse()` |

> **النتيجة:** حتى لو تعطّلت خدمة الذكاء الاصطناعي بالكامل، يستمر التطبيق في استقبال البلاغات وعملها بشكل طبيعي — تُترك حقول الذكاء الاصطناعي فارغة فحسب.

---

## 14. الاختبارات (Tests)

**الملف:** `tests/Feature/ReportAiAnalyzerTest.php` (445 سطراً، اختبارات Pest)

### استراتيجية الاختبار:
تُستخدم **Mockery** لعزل `AiService` بحيث لا تُجرى استدعاءات HTTP حقيقية. الدالة المساعدة `mockAiServiceForAnalysis()` تُحاكي الاستدعاء الأول (تحليل) والثاني (تأكيد التكرار).

### تغطية الاختبارات (17 حالة):

**تحليل الاستجابة والتحديث:**
1. تحديث حقول البلاغ من استجابة صالحة.
2. تحليل البلاغات النصية بلا مرفقات.
3. تقييد درجة الخطورة العالية إلى 5.
4. تقييد درجة الخطورة السالبة إلى 1.
5. عدم الكسر عند إعادة الـ API لـ `null`.
6. تحليل JSON مغلَّف بـ ```` ```json ``` ````.
7. تحليل JSON مغلَّف بـ ```` ``` ````.
8. التعامل مع JSON مشوّه دون كسر.

**كشف التكرار:**
9. اكتشاف تكرار بلاغ قريب حديث.
10. تمييز البلاغات المختلفة (ليست مكررة).
11. تخطّي كشف التكرار عند غياب الإحداثيات.

**المستمع (Listener):**
12. تخطّي البلاغات الفارغة.
13. تشغيل التحليل عند وجود وصف.
14. التقاط الاستثناءات دون كسر.

**ربط الأحداث:**
15. وصول الحدث `ReportCreated` للمستمع.

**خدمة المحتوى:**
16. بناء محتوى صورة حقيقي كـ Data URI base64.
17. إعادة `null` عند غياب ملف الصورة/الفيديو.

**تكامل النموذج:**
- التحقق من الـ casts (boolean/integer/datetime).
- حلّ علاقة `duplicateOf`.

---

## 15. خريطة الملفات الكاملة

| # | الملف | الدور | السطور |
|---|------|------|--------|
| 1 | `config/ai.php` | إعدادات الذكاء الاصطناعي | 9 |
| 2 | `app/Services/AiService.php` | طبقة الاتصال بـ HTTP | 138 |
| 3 | `app/Services/ReportAiAnalyzer.php` | المنطق التحليلي | 295 |
| 4 | `app/Events/ReportCreated.php` | حدث إنشاء البلاغ | 19 |
| 5 | `app/Listeners/AnalyzeReportWithAi.php` | المستمع غير المتزامن | 37 |
| 6 | `app/Services/ReportRoutingService.php` | إنشاء البلاغ + إطلاق الحدث | 68 |
| 7 | `app/Models/Report.php` | النموذج + casts + علاقات | 71 |
| 8 | `app/Enums/ReportStatus.php` | تعداد الحالات (يستخدم `Rejected`) | 31 |
| 9 | `database/migrations/2026_06_23_..._add_ai_analysis_to_reports_table.php` | إضافة أعمدة AI | 40 |
| 10 | `app/Filament/Admin/Resources/ReportResource.php` | عرض AI للإدارة | 261 |
| 11 | `app/Filament/Admin/.../Pages/ViewReport.php` | زر إعادة التحليل | 47 |
| 12 | `app/Filament/Police/Resources/AssignedReportResource.php` | عرض AI للشرطة | 242 |
| 13 | `app/Filament/Police/.../Pages/ViewAssignedReport.php` | زر إعادة التحليل | 100 |
| 14 | `app/Observers/ReportObserver.php` | تسجيل تغيّر الحالة | 40 |
| 15 | `app/Providers/AppServiceProvider.php` | ملاحظة اكتشاف المستمعات | 34 |
| 16 | `lang/ar/messages.php` | ترجمة عربية لمفاتيح AI | — |
| 17 | `lang/en/messages.php` | ترجمة إنجليزية لمفاتيح AI | — |
| 18 | `tests/Feature/ReportAiAnalyzerTest.php` | اختبارات شاملة | 445 |
| 19 | `ai.md` | مواصفات ميزات AI | 9 |
| 20 | `aiConnect.md` | تقرير تكامل مزود AI | 353 |

---

## 16. التقييم ونقاط القوة والضعف

### ✅ نقاط القوة
1. **بنية نظيفة ومفصولة:** كل طبقة لها مسؤولية واحدة (اتصال / تحليل / تشغيل / عرض).
2. **تنفيذ غير متزامن** عبر الطابور — لا يؤثر على تجربة المواطن.
3. **مقاومة للأعطال:** فشل الذكاء الاصطناعي لا يكسر التطبيق أبداً.
4. **توفير التكلفة:** الفلتر الجغرافي السريع قبل استدعاء الذكاء الاصطناعي في كشف التكرار.
5. **حماية من الاحتيال:** كشف الصور المولّدة + رفض تلقائي للبلاغات المفبركة.
6. **توافق OpenAI:** استخدام LiteLLM يجعل تغيير المزود سهلاً جداً.
7. **اختبارات شاملة** (17 حالة) بعزل تام للشبكة.
8. **دعم لغوي كامل** (عربي/إنجليزي) وواجهة غنية بصرية.

### ⚠️ نقاط الضعف / فرص التحسين
1. **لا وجود لإعادة المحاولة (Retry):** استدعاء واحد فقط دون exponential backoff.
2. **لا تتبّع للتكلفة/الرموز:** لا يُحصى استهلاك `max_tokens` لكل تحليل.
3. **النموذج مُثبَّت في `.env`:** لا يوجد اختيار ديناميكي للنموذج حسب نوع البلاغ.
4. **معالجة الفيديو بدائية:** يُرسل كصورة base64 (قد يُحلّل الإطار الأول فقط)؛ شرط 20MB يُسقط الفيديوهات الكبيرة بصمت.
5. **متغيرات `.env` غير موثّقة** في `.env.example` — قد يفشل النشر إذا نُسيت.
6. **لا حدّ معدل (Rate Limiting)** على استدعاءات الذكاء الاصطناعي.
7. **كشف التكرار يأخذ أول مرشّح فقط** (`->first()`) — قد يفوّت تطابقاً أفضل.
8. **الاعتماد على JSON الصارم:** رغم التسامح مع أغلفة markdown، أي انحراف في البنية يُفقد النتيجة كاملة.

### 📈 مقترحات التحسين (مستمدة من `aiConnect.md`)
- **أولوية عالية:** إضافة آلية إعادة محاولة، تتبّع التكلفة، اختيار النموذج.
- **أولوية متوسطة:** Rate limiting، توليد الصور (غير مطلوب هنا)، رسائل أخطاء أدق.
- **أولوية منخفضة:** تصدير التحليلات، برومبتات نظام قابلة للتهيئة.

---

## خلاصة

نظام الذكاء الاصطناعي في هذا المشروع هو **حل متكامل وقوي لإدارة البلاغات المرورية الذكية**، يعتمد على نموذج Google Gemini عبر بوابة LiteLLM، ويغطي كامل دورة حياة البلاغ: من الإنشاء غير المتزامن للتحليل، مروراً بكشف اللوحات وتصنيف الحوادث وتقييم الخطورة والأضرار، وصولاً إلى كشف التكرار ومحاربة البلاغات المفبركة بالذكاء الاصطناعي. البنية قابلة للصيانة جيداً ومُختبَرة، مع حُزن متين من الأخطاء يضمن استمرار عمل التطبيق حتى عند انقطاع خدمة الذكاء الاصطناعي.

**إجمالي الأكواد المرتبطة بالذكاء الاصطناعي:** ~1900 سطر موزّعة على 20 ملفاً.
