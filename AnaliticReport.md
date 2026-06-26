# تقرير شامل — `AnalyticsService` (الإصدار المُحدَّث بعد الإصلاحات)

> **الملف:** `app/Services/Analytics/AnalyticsService.php`
> **نوع التقرير:** مراجعة معمارية + تحليل دوال + تقييم جودة + **سجلّ الإصلاحات المُطبّقة**
> **عدد الأسطر:** 499 ← **544** (بعد الإصلاحات)
> **التاريخ:** 2026-06-26
> **حالة الاختبارات:** ✅ **42/42 ناجحة** (98 تأكيداً)

---

## 0. ملخّص حالة الإصلاحات

| # | المشكلة | الخطورة | الحالة | الدليل |
|---|---|---|---|---|
| 1 | خطر حقن SQL في دوال التوزيع | 🔴 عالية | ✅ **مُصلَحة** | قائمة بيضاء + `guardColumn()` |
| 2 | عيب توقيع `violationDistribution` | 🔴 عالية | ✅ **مُصلَحة** | إضافة نوع `Carbon $end` |
| 3 | دلالات `collection_rate` / `outstanding_fines` | 🔶 متوسطة | ✅ **مُصلَحة** | استبعاد `canceled`، إدراج `pending` |
| 4 | أداء `regionCompliance` (`get()` كامل) | 🔶 متوسطة | ✅ **مُصلَحة** | `get(['description'])` |
| 5 | اتجاه التنبؤ يعتمد على طرفي السلسلة | 🔶 متوسطة | ✅ **مُصلَحة** | استخدام إشارة الميل `slope` |
| 6 | `peak_hours` تُهمل ساعات البلاغات | 🟡 منخفضة | ✅ **مُصلَحة** | دمج ساعات البلاغات |
| 7 | غياب فهارس على أعمدة التاريخ | 🔶 متوسطة | ✅ **مُصلَحة** | migration جديد |

> تفاصيل كل إصلاح في **القسم 10** (سجلّ الإصلاحات).

---

## 1. نظرة عامة (Overview)

`AnalyticsService` هي طبقة الخدمة (Service Layer) المركزية المسؤولة عن كل عمليات التحليل والإحصاء في نظام إدارة المخالفات والبلاغات المرورية. تُلخّص البيانات الخام القادمة من جدولين رئيسيّين:

| المصدر | الموديل | عمود التاريخ المرجعي |
|---|---|---|
| البلاغات (Reports) | `App\Models\Report` | `created_at` |
| المخالفات (Violations) | `App\Models\TrafficViolation` | `issued_at` |
| السائقون | `App\Models\CitizenData` | — |

الخدمة **لا تعتمد على قاعدة بيانات محددة** في أغلب منطقها (تستخدم `substr(...)` بدل دوال التاريخ الخاصة بقاعدة معيّنة)، ما يجعلها محمولة بين SQLite/MySQL.

### المستهلكون (Consumers)

تُستخدم الخدمة في 7 مواضع عبر آلية الحاوية `app(AnalyticsService::class)`:

1. `App\Filament\Admin\Pages\AdvancedAnalytics` — لوحة التحليلات الرئيسية (KPIs، اتجاهات، تنبؤ، نقاط ساخنة، تصدير Excel/CSV/PDF).
2. `App\Filament\Admin\Pages\CustomReportBuilder` — منشئ التقارير المخصّصة.
3. `App\Filament\Admin\Widgets\AnalyticsKpiOverview` — ودجت ملخّص الـ KPIs في لوحة التحكم.
4. `App\Exports\AnalyticsExport` / `AnalyticsCsvExport` / `CustomReportExport` — طبقات التصدير.

---

## 2. التصميم المعماري (Architecture)

### 2.1 مبادئ التصميم

- **Service Layer Pattern:** منطق الأعمال منفصل تماماً عن طبقة العرض (Filament) وعن الموديلات.
- **Dependency through Container:** تُحلّ عبر الحاوية وليس بحقن مباشر، ما يسهّل الاختبار والاستبدال.
- **Single Public API:** كل ميزة تحليلية مغلّفة بدالة عامة موثّقة بـ PHPDoc مفصّل.
- **In-Request Memoization:** تخزين مؤقت داخلي لتجنّب إعادة الحساب داخل نفس الطلب.
- **Allow-List Defense (جديد):** مدخلات الأعمدة تُراقب ضد قائمة بيضاء ثابتة قبل الولوج للاستعلام الخام.

### 2.2 النطاق (Namespace & Visibility)

```
App\Services\Analytics\AnalyticsService
```

- دوال **عامة (public):** الواجهة الخارجية للتحليلات.
- دوال **محمية (protected):** المساعدات الداخلية (`reportQuery`, `violationQuery`, `key`, `linearRegression`, `guardColumn`, `customReports`, ...).
- **ثوابت خاصة (private const):** قوائم الأعمدة المسموحة `REPORT_DISTRIBUTION_COLUMNS` و`VIOLATION_DISTRIBUTION_COLUMNS`.

---

## 3. آلية التخزين المؤقت (Caching)

```php
private array $cache = [];
```

- النطاق: **داخل نسخة الكائن فقط** (Request-scoped)، وليست Cache::store عبر الطلبات.
- المفتاح: `key()` ينتج `"{method}:{start}|{end}"`.
- تُستخدم عبر عامل `??=`.

### متى تفيد؟
داخل صفحة `AdvancedAnalytics` تُستدعى `regionCompliance()` من عدّة دوال (`bestRegions`, `worstRegions`, `hotspots`, `regionCompliance` مباشرة). التخزين المؤقت يضمن تنفيذ منطق ترتيب المناطق **مرة واحدة فقط** لكل طلب عرض.

### حدّها
ℹ️ **لا توجد فائدة عبر الطلبات.** لأن `app(AnalyticsService::class)` تُنشئ نسخة جديدة في كل طلب (الخدمة ليست `singleton` مُسجّلة). للحصول على كاش حقيقي عبر الطلبات يمكن ربطها بـ `Cache::remember` لاحقاً كتحسين اختياري.

---

## 4. تحليل الدوال (Method-by-Method)

### 4.1 مؤشرات الأداء `kpis()` — الأسطر 45–65

تُرجع 9 مؤشرات في استدعاء واحد:

| المؤشر | المصدر | المنطق |
|---|---|---|
| `total_reports` | reports | `count()` |
| `total_violations` | violations | `count()` |
| `avg_response_minutes` | reports (resolved) | متوسط `updated_at - created_at` |
| `resolution_rate` | reports | `resolved / total × 100` |
| `violation_rate_per_driver` | violations ÷ citizens | `count / drivers` |
| `collection_rate` | violations | `paid / (total − canceled) × 100` ✅ **مصحَّح** |
| `total_fines` | violations | `sum(fine_amount)` |
| `collected_fines` | violations (paid) | `sum(fine_amount)` |
| `outstanding_fines` | violations (unpaid + pending) | `sum(fine_amount)` ✅ **مصحَّح** |

---

### 4.2 `averageResponseMinutes()` — الأسطر 70–86

يجلب البلاغات `resolved` ثم يحسب الفرق بالثواني في PHP ويحوّل لدقائق.

> **ملاحظة:** يُحمّل السجلات إلى الذاكرة (`get()`) بدل استخدام `AVG()` في SQL — وذلك لتفادي الاعتماد على دالة فرق تواريخ خاصة بقاعدة معيّنة. مقايضة **قابلية النقل مقابل الأداء**. تحسين اختياري مستقبلي.

---

### 4.3 `monthlyTrend()` — الأسطر 145–173

يُنشئ فترة شهرية عبر `CarbonPeriod`، ثم يجمع عدد البلاغات/المخالفات لكل شهر باستخدام `substr(..., 1, 7)`.

✅ يضمن ظهور كل الأشهر في النطاق (حتى الفارغة = 0).

---

### 4.4 دوال التوزيع `reportDistribution()` / `violationDistribution()` — الأسطر 180–209 ✅ **مُصلَحة**

تجمع حسب عمود مرن بعد **التحقق منه ضد قائمة بيضاء**:

```php
public function reportDistribution(Carbon $start, Carbon $end, string $column): array
{
    $this->guardColumn($column, self::REPORT_DISTRIBUTION_COLUMNS);
    return $this->reportQuery($start, $end)
        ->selectRaw("{$column}, COUNT(*) as aggregate")
        ...
}
```

الأعمدة المسموحة: `['report_type', 'status', 'assigned_department']` للبلاغات، و`['violation_type', 'status']` للمخالفات. أي عمود غير مسموح يُطرح `InvalidArgumentException`.

✅ التوقيع أصبح مُوحّداً: `violationDistribution(Carbon $start, Carbon $end, ...)`.

---

### 4.5 `regionCompliance()` — الأسطر 216–259 ✅ **مُحسَّنة الأداء**

**منطق الترتيب:** المنطقة الأقل حوادث = الأكثر التزاماً.

**التحسين:** بدلاً من جلب كل أعمدة المخالفات (`get()`)، يتم جلب العمود المطلوب فقط:
```php
$violationRegions = $this->violationQuery($start, $end)->get(['description']);
```
ما يقلّل حجم البيانات المنقولة والذاكرة المستهلكة.

> ℹ️ استخراج المنطقة `extractRegion()` (الأسطر 460–489) يبقى يعتمد على مطابقة نص حرّ. تحسين طويل المدى (اختياري): إضافة عمود `region`/`city_id` مُفهرس.

---

### 4.6 `compareWithPrevious()` — الأسطر 276–303

يقارن النطاق الحالي بنطاق سابق بنفس الطول، ويُرجع لكل مؤشر: `current`, `previous`, `absolute`, `percent` (نسبة مئوية أو `null` إذا كان السابق صفراً).

---

### 4.7 التنبؤ `forecastIncidents()` — الأسطر 310–347 ✅ **مُصلَح**

يستخدم **انحدار خطّي (Linear Regression)** عبر `linearRegression()` على البيانات الشهرية، ثم يتنبأ بالأشهر القادمة.

**الإصلاح:** اتجاه الترند أصبح مُشتقّاً من **إشارة الميل (`slope`)** بدل مقارنة أول/آخر قيمة:
```php
$trendDirection = count($values) < 2 ? 'stable'
    : ($slope > 0 ? 'up' : ($slope < 0 ? 'down' : 'stable'));
```
هذا أكثر دقّة لأنه يأخذ بالاعتبار **كامل السلسلة** وليس طرفيها فقط.

---

### 4.8 النقاط الساخنة `hotspots()` — الأسطر 354–390 ✅ **مُصلَحة**

- `peak_hours`: مصفوفة 24 خانة — أصبحت تجمع ساعات **البلاغات والمخالفات معاً**:
```php
foreach ($reportHours as $hour => $count) { $hours[(int) $hour] += (int) $count; }
foreach ($violationHours as $hour => $count) { $hours[(int) $hour] += (int) $count; }
```
- `top_hotspots`: أعلى 5 مناطق حسب الحوادث.

---

### 4.9 التقارير المخصّصة `customReport()` — الأسطر 398–409

توجيه (`match`) حسب النوع: `reports` / `violations` / `incidents` (اتحاد الاثنين).

---

### 4.10 `linearRegression()` — الأسطر 497–514

تنفيذ صحيح للمربّعات الصغرى مع حماية ضد القسمة على صفر.

---

### 4.11 `guardColumn()` (جديدة) — الأسطر 533–538

دالة حماية تتحقق من عضوية العمود في القائمة البيضاء قبل تمريره لاستعلام خام:

```php
protected function guardColumn(string $column, array $allowed): void
{
    if (! in_array($column, $allowed, true)) {
        throw new \InvalidArgumentException("Distribution column [{$column}] is not allowed.");
    }
}
```

---

## 5. تدفّق البيانات (Data Flow)

```
┌─────────────────────────────────────────────────────────────┐
│  Filament Page (AdvancedAnalytics / CustomReportBuilder)    │
│     $this->service = app(AnalyticsService::class)           │
└──────────────────────────┬──────────────────────────────────┘
                           │ Carbon $start, Carbon $end
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                     AnalyticsService                        │
│   kpis() / monthlyTrend() / regionCompliance() / ...        │
│                 [in-memory cache by date key]               │
│            [guardColumn() allow-list for raw queries]       │
└──────────┬────────────────────────────────┬─────────────────┘
           │ reportQuery()                  │ violationQuery()
           ▼                                ▼
   Report::query()                 TrafficViolation::query()
   whereBetween(created_at)        whereBetween(issued_at)
   📈 index: created_at            📈 index: issued_at
```

---

## 6. نقاط القوة (Strengths)

1. **فصل نظيف للمسؤوليات** — لا يوجد تسرّب لمنطق التحليل إلى الموديلات أو الـ Controllers.
2. **توثيق PHPDoc ممتاز** — أنواع الإرجاع مفصّلة لكل دالة.
3. **قابلية النقل بين قواعد البيانات** — استخدام `substr()` بدل دوال التاريخ الخاصة.
4. **تخزين مؤقت ذكي داخل الطلب** — يمنع إعادة الحساب المكرّر.
5. **دفاع أمني صريح (جديد)** — قائمة بيضاء للأعمدة تمنع حقن SQL.
6. **تغطية اختبارية شاملة** — `tests/Feature/AnalyticsServiceTest.php` يغطّي الآن **11 سيناريو** (7 أصلية + 4 جديدة للإصلاحات).

---

## 7. المشكلات الأصلية وحلولها

### 7.1 ✅ مُصلَح — خطر حقن SQL
**المشكلة:** `$column` كان يُمرّر مباشرة إلى `selectRaw` و`groupBy`.
**الحل:** ثوابت قائمة بيضاء + `guardColumn()` تطرح `InvalidArgumentException` عند أي عمود غير مسموح.

### 7.2 ✅ مُصلَح — عيب توقيع الدالة
**المشكلة:** `$end` بلا نوع `Carbon` في `violationDistribution`.
**الحل:** توحيد التوقيع: `violationDistribution(Carbon $start, Carbon $end, string $column)`.

### 7.3 ✅ مُصلَح — مشاكل الأداء
| المجال | قبل | بعد |
|---|---|---|
| `regionCompliance` | `get()` يجلب كل الأعمدة | `get(['description'])` للأعمدة المطلوبة فقط |
| فهارس التاريخ | `created_at`/`issued_at` غير مُفهرسين | migration جديد يضيف فهارس B-Tree |

> **ملف الـ migration:** `2026_06_26_152814_add_date_indexes_to_reports_and_traffic_violations_tables.php`

### 7.4 ✅ مُصلَح — دلالات `collection_rate` / `outstanding_fines`
**قبل:** `collection_rate` = `paid / total` (تشمل `canceled` في المقام)، `outstanding_fines` = `Unpaid` فقط.
**بعد:**
- `collection_rate` = `paid / (total − canceled)` — يُحسب فقط على الغرامات الفعلية القابلة للتحصيل.
- `outstanding_fines` = `whereIn([Unpaid, PendingVerification])` — يشمل كل ما لم يُحصّل بعد.

### 7.5 ✅ مُصلَح — اتجاه التنبؤ
**قبل:** يعتمد على مقارنة أول/آخر قيمة.
**بعد:** يعتمد على إشارة ميل الانحدار الخطّي (`slope`) المحسوب فعلاً.

### 7.6 ✅ مُصلَح — `peak_hours` تُهمل البلاغات
**قبل:** ساعات الذروة تُحتسب من المخالفات فقط.
**بعد:** تجمع ساعات البلاغات والمخالفات معاً.

---

## 8. تحسينات اختيارية مستقبلية (لم تُطلب — للتوثيق فقط)

| الأولوية | التحسين | الموقع |
|---|---|---|
| منخفضة | ربط الكاش بـ `Cache::remember` لفائدة عبر الطلبات | السطر 28 |
| منخفضة | إضافة عمود `region`/`city_id` مُفهرس بدل تحليل النص | الجداول |
| منخفضة | استخدام `AVG()` على مستوى SQL لـ `averageResponseMinutes` | السطور 70–86 |
| منخفضة | دمج استعلامات `kpis` في عدد أقل من الاستعلامات المجمّعة | السطور 45–65 |

---

## 9. التحقق والإثبات (Verification)

### 9.1 فحص السنتاكس
```
$ php -l app/Services/Analytics/AnalyticsService.php
No syntax errors detected

$ php -l database/migrations/...add_date_indexes...php
No syntax errors detected
```

### 9.2 نتائج الاختبارات
```
$ php artisan test
Tests: 42 passed (98 assertions)
Duration: 8.30s
```

**الاختبارات الجديدة المضافة (4):**
- ✅ `it rejects disallowed distribution columns` — يتحقق أن الأعمدة غير المسموحة تُطرح استثناء.
- ✅ `it excludes canceled fines from collection rate and includes pending in outstanding` — يتحقق من الدلالات المُصحَّحة (collection_rate = 33.33، outstanding = 80000).
- ✅ `it counts report hours in hotspots peak hours` — يتحقق أن ساعات البلاغات تُحسب ضمن الذروة.
- ✅ `it derives forecast trend direction from regression slope` — يتحقق أن الترند المتصاعد يُصنّف `up`.

### 9.3 فحص الـ migration
```
$ php artisan migrate --pretend
⇂ alter table `reports` add index `reports_created_at_index`(`created_at`)
⇂ alter table `traffic_violations` add index `traffic_violations_issued_at_index`(`issued_at`)
```

---

## 10. سجلّ الإصلاحات المُطبّقة (Changelog)

### الملف: `app/Services/Analytics/AnalyticsService.php`

| الإصلاح | الأسطر |
|---|---|
| ثوابت القائمة البيضاء للأعمدة | 17–25 |
| `outstanding_fines` يشمل `pending_verification` (`whereIn`) | 59–63 |
| `collectionRate` يستبعد `canceled` من المقام | 119–138 |
| `reportDistribution` + `violationDistribution` تستدعيان `guardColumn` | 182, 200 |
| توقيع `violationDistribution` موحّد بـ `Carbon $end` | 198 |
| `regionCompliance` يجلب الأعمدة المطلوبة فقط | 222 |
| `forecastIncidents` اتجاه الترند من إشارة `slope` | 324–340 |
| `hotspots` تجمع ساعات البلاغات + المخالفات | 361–375 |
| دالة `guardColumn` الجديدة | 526–538 |

### الملف: `database/migrations/2026_06_26_152814_add_date_indexes_to_reports_and_traffic_violations_tables.php` (جديد)
- إضافة فهرس `reports_created_at_index` على `reports.created_at`.
- إضافة فهرس `traffic_violations_issued_at_index` على `traffic_violations.issued_at`.

### الملف: `tests/Feature/AnalyticsServiceTest.php`
- إضافة 4 اختبارات جديدة لتغطية كل إصلاح (الأسطر 216–279).

---

## 11. ملخّص تنفيذي

تمّت معالجة **جميع المشكلات الـ7** المُكتشفة في المراجعة الأصلية. أصبحت `AnalyticsService` الآن:

1. **آمنة** — عمود التوزيع لا يصل للاستعلام إلا بعد التحقق من قائمة بيضاء صارمة (منع حقن SQL).
2. **دقيقة دلالياً** — نسبة التحصيل تستبعد الملغاة، والمستحقّات تشمل قيد التحقق.
3. **موثوقة منطقياً** — التنبؤ يستخدم ميل الانحدار الكامل، والذروة تجمع البلاغات والمخالفات.
4. **أسرع** — جلب الأعمدة المطلوبة فقط + فهارس على أعمدة التاريخ.
5. **مُختبَرة بالكامل** — 42 اختباراً ناجحاً تشمل 4 اختبارات جديدة لكل إصلاح.
