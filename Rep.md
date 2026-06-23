# تقرير شامل ومحدّث عن مشروع Traffic App
## النظام الذكي للبلاغات المرورية والسلامة على الطرق — Laravel 12 + Filament 3.3

> **ملاحظة:** هذا التقرير يوثّق الحالة **الحالية** للمشروع بعد التطويرات والإضافات الكبرى (نظام الذكاء الاصطناعي، منصة التحليلات المتقدمة، التحقق من المدفوعات، السجلات الشاملة للنشاطات). تم الحفاظ على البنية الأصلية للتقرير مع تحديث كل قسم وإضافة أقسام جديدة للأنظمة المستحدثة.

---

## جدول المحتويات

1. [نبذة عامة عن المشروع](#1-نبذة-عامة-عن-المشروع)
2. [بنية المشروع (الجذر)](#2-بنية-المشروع-الجذر)
3. [مجلد `app/` بالتفصيل](#3-مجلد-app-بالتفصيل)
4. [شرح مفصل لكل ملف ضمن `app/`](#4-شرح-مفصل-لكل-ملف-ضمن-app)
5. [المسارات (Routes)](#5-المسارات-routes)
6. [قاعدة البيانات (Database)](#6-قاعدة-البيانات-database)
7. [العروض (Views)](#7-العروض-views)
8. [نظام الصلاحيات والأمان](#8-نظام-الصلاحيات-والأمان)
9. [سير العمل (Workflow)](#9-سير-العمل-workflow)
10. [ميزات تقنية مهمة](#10-ميزات-تقنية-مهمة)
11. [🧠 نظام الذكاء الاصطناعي (AI Analysis Engine)](#11--نظام-الذكاء-الاصطناعي-ai-analysis-engine)
12. [📊 منصة التحليلات المتقدمة (Advanced Analytics)](#12--منصة-التحليلات-المتقدمة-advanced-analytics)
13. [💳 التحقق من المدفوعات (Payment Verification)](#13--التحقق-من-المدفوعات-payment-verification)
14. [📝 التقرير والتصدير (Reporting & Exports)](#14--التقرير-والتصدير-reporting--exports)
15. [🗺️ التكامل مع الخرائط (Map Integration)](#15--التكامل-مع-الخرائط-map-integration)

---

## 1. نبذة عامة عن المشروع

**Traffic App** هو نظام ويب متكامل وذكي لإدارة البلاغات المرورية والمخالفات والسلامة على الطرق. النظام في نسخته الحالية لم يعد مجرد أداة CRUD أساسية، بل تطوّر إلى **منصة مؤسسية متقدمة** تدمج تقنيات الذكاء الاصطناعي، والتحليلات التنبؤية، وأنظمة التحقق، مع معمارية صارمة (Service Layer + Observers + Events).

يدعم المشروع ثلاثة أنواع من المستخدمين:

| الدور | الوصف | الواجهة |
|-------|-------|---------|
| **مواطن (Citizen)** | يقدّم البلاغات (مع GPS ووسائط)، يدير مركباته، يعرض مخالفاته ويسدّد الغرامات (برفع إيصال) | واجهة Blade + Tailwind CSS + Alpine.js |
| **شرطة (Police)** | يستقبل البلاغات المحوّلة لقسمه، يراجع تحليل الذكاء الاصطناعي، يصدر المخالفات، يحدّث حالة البلاغات | لوحة تحكم Filament (Police Panel) |
| **مدير (Admin)** | يدير كل بيانات النظام، ويستعرض **التحليلات المتقدمة** والتنبؤات والتصدير، وسجل النشاطات الشامل | لوحة تحكم Filament (Admin Panel) |

### التقنيات المستخدمة (محدّثة)
- **الخلفية (Backend):** Laravel 12 (PHP 8.2+، مع `declare(strict_types=1)` في كل الملفات)
- **لوحات التحكم:** Filament 3.3 (لوحتان منفصلتان: Admin + Police)
- **الواجهة الأمامية للمواطن:** Blade Templates + Tailwind CSS 4 + Alpine.js + Chart.js
- **قاعدة البيانات:** MySQL/SQLite عبر Eloquent ORM
- **🧠 الذكاء الاصطناعي:** تكامل مع بوابة LiteLLM Proxy (نموذج Gemini) — تحليل نصي وبصري متعدد الوسائط (Multimodal)
- **📊 التحليلات والتنبؤ:** محرك تحليلات مخصّص (KPIs، انحدار خطي للتنبؤ، تحديد النقاط الساخنة)
- **📑 التصدير:** Maatwebsite/Excel (متعدد الأوراق)، mPDF (PDF عربي RTL)، Barryvdh DomPDF
- **🗺️ الخرائط:** Leaflet.js + OpenStreetMap لعرض إحداثيات البلاغات
- **تعدد اللغات:** mcamara/laravel-localization (عربي/إنجليزي)
- **تبديل اللغة في Filament:** bezhansalleh/filament-language-switch
- **الصلاحيات:** spatie/laravel-permission + Policies + Gates
- **معالجة المهام:** طوابير Laravel (Queues) لتنفيذ تحليل الذكاء الاصطناعي بشكل غير متزامن
- **البناء:** Vite 7 + Laravel Vite Plugin

---

## 2. بنية المشروع (الجذر)

```
traffic_app/
├── app/                    # الكود الأساسي للتطبيق (انظر القسم 3)
├── bootstrap/              # إقلاع Laravel (يسجّل FilamentLocaleServiceProvider)
├── config/                 # ملفات الإعداد (app, auth, database, ai, permission, ...)
├── database/               # التهجيرات (Migrations) والبذور (Seeders)
├── lang/                   # ملفات الترجمة (ar/en) + analytics + filament + messages
├── node_modules/           # حزم Node.js
├── public/                 # الملفات العامة (CSS, JS, logo.png, sh.png, صور)
├── resources/              # العروض (Views)، CSS، JS، مكوّنات Filament المخصّصة
├── routes/                 # مسارات التطبيق (web.php)
├── storage/                # التخزين (الملفات المرفوعة، اللوغات، تقارير mPDF المؤقتة)
├── tests/                  # اختبارات Pest
├── vendor/                 # حزم Composer
├── artisan                 # أداة سطر أوامر Laravel
├── composer.json           # إعدادات PHP وحزم Composer (+ سكربتات dev/setup/test)
├── package.json            # إعدادات Node.js
├── vite.config.js          # إعدادات Vite
├── phpunit.xml             # إعدادات اختبار PHPUnit/Pest
├── .env / .env.example     # متغيرات البيئة (بما فيها إعدادات الذكاء الاصطناعي AI_*)
├── README.md               # وثائق المشروع
└── Rep.md                  # هذا التقرير
```

### سكربتات Composer الهامة
| الأمر | الوظيفة |
|-------|---------|
| `composer dev` | يشغّل ثلاث عمليات معاً بالتوازي: خادم Laravel + مستمع الطابور (`queue:listen`) + Vite (لتطوير الواجهة) |
| `composer setup` | تثبيت كامل: composer + key + migrate + npm + build |
| `composer test` | تنظيف الإعداد ثم تشغيل اختبارات Pest |

---

## 3. مجلد `app/` بالتفصيل

هذا هو المجلد الأساسي الذي يحتوي على كل منطق العمل. تمت إعادة هيكلته بمعمارية مؤسسية صارمة:

```
app/
├── Enums/                  # التعداد (Enums) لأنواع الحالات والأقسام
├── Events/                 # أحداث Laravel (ReportCreated)
├── Exceptions/             # الاستثناءات المخصصة (ReportRoutingException)
├── Exports/                # 🆕 فئات التصدير (Excel/CSV) لمنصة التحليلات
│   └── AnalyticsSheets/    # أوراق مخصّصة لملف Excel متعدد الأوراق
├── Filament/               # موارد وصفحات لوحات تحكم Filament
│   ├── Admin/              # لوحة تحكم المدير
│   │   ├── Pages/          # 🆕 صفحات مخصّصة (التحليلات المتقدمة + منشئ التقارير)
│   │   ├── Resources/      # موارد CRUD + RelationManagers
│   │   └── Widgets/        # أدوات لوحة المعلومات (إحصائيات + رسوم + تحليلات)
│   ├── Police/             # لوحة تحكم الشرطة
│   │   ├── Resources/      # موارد CRUD + RelationManagers
│   │   └── Widgets/        # أدوات لوحة المعلومات
│   └── Pages/Auth/         # صفحة تسجيل الدخول المخصّصة لـ Filament
├── Http/                   # طبقة HTTP (متحكمات نحيفة + Form Requests)
│   ├── Controllers/        # المتحكمات (لا تحتوي منطق أعمال — Zero-Logic)
│   └── Requests/           # طلبات النموذج المُحقَّقة (Form Requests)
├── Listeners/              # 🆕 مستمعو الأحداث (تسجيل النشاط + تحليل AI غير المتزامن)
├── Models/                 # نماذج Eloquent (ORM)
│   └── Scopes/             # نطاقات الاستعلام (DepartmentScope)
├── Observers/              # 🆕 مراقبو النماذج (ReportObserver لتسجيل تغيير الحالة)
├── Policies/               # سياسات التفويض (VehiclePolicy, ReportPolicy)
├── Providers/              # مزودو الخدمة (+ FilamentLocaleServiceProvider)
│   └── Filament/           # مزودو لوحات Filament
└── Services/               # 🆕 طبقة الخدمة الغنية (Business Logic + AI + Analytics)
    ├── Analytics/          # 🆕 محرك التحليلات والتنبؤ
    ├── ActivityLogger.php  # 🆕 مسجّل النشاطات المركزي
    ├── AiService.php       # 🆕 عميل بوابة الذكاء الاصطناعي
    ├── ReportAiAnalyzer.php# 🆕 محرك تحليل البلاغات بالذكاء الاصطناعي
    ├── ReportRoutingService.php # خدمة التوجيه الذكي
    ├── VehicleService.php  # خدمة المركبات
    └── ViolationService.php# خدمة المخالفات (مع تسجيل النشاط)
```

---

## 4. شرح مفصل لكل ملف ضمن `app/`

---

### 4.1 النماذج (Models) — `app/Models/`

#### 4.1.1 `User.php` — نموذج المستخدم

يطبّق `FilamentUser` و `HasName` لدعم لوحات Filament.

**الحقول القابلة للتعبئة:** `username`, `email`, `password` (مشفّرة عبر cast `hashed`), `role_id`, `is_active` (boolean).

**العلاقات:**
- `role()` → `BelongsTo`
- `citizenData()` / `policeData()` / `adminData()` → `HasOne`
- `violations()` / `reports()` / `vehicles()` → `HasManyThrough` (عبر CitizenData)

**الدوال المساعدة:**
- `isCitizen()` / `isPolice()` / `isAdmin()` — تتحقق من نوع الدور عبر `role->slug`
- `canAccessPanel(Panel $panel)` — تحكم صارم بالوصول:
  - لوحة `admin` → فقط المديرون
  - لوحة `police` → فقط الشرطة
  - يجب أن يكون الحساب فعّالاً (`is_active = true`)
- `getFilamentName()` — يُرجع الاسم الكامل حسب الدور

---

#### 4.1.2 `Role.php` — نموذج الدور

**الحقول:** `name`, `slug` (citizen, police, admin). **العلاقة:** `users()` → `HasMany`.

---

#### 4.1.3 `CitizenData.php` — نموذج بيانات المواطن

**الجدول:** `citizens_data`. **الحقول:** `user_id`, `national_id`, `full_name`, `phone`, `blood_type`.
**العلاقات:** `user()` → `BelongsTo`، `vehicles()` / `reports()` / `violations()` → `HasMany`.

---

#### 4.1.4 `PoliceData.php` — نموذج بيانات الشرطة

**الجدول:** `police_data`. **الحقول:** `user_id`, `badge_number`, `full_name`, `rank`, `department` (cast إلى Enum `Department`).
**العلاقات:** `user()` → `BelongsTo`، `violations()` → `HasMany`.

---

#### 4.1.5 `AdminData.php` — نموذج بيانات المدير

**الحقول:** `user_id`, `full_name`. **العلاقات:** `user()` → `BelongsTo`، `activityLogs()` → `HasMany`.

---

#### 4.1.6 `Report.php` — نموذج البلاغ ⭐ (محدّث بدعم الذكاء الاصطناعي)

يمثل بلاغاً مرورياً مع **بيانات تحليل الذكاء الاصطناعي المدمجة**.

**الحقول الأساسية:** `citizen_id`, `vehicle_id`, `reported_vehicle_plate`, `assigned_department` (Enum), `report_type`, `description`, `latitude`/`longitude` (decimal:7), `location_text`, `image_url`, `video_url`, `status` (Enum).

**🆕 حقول تحليل الذكاء الاصطناعي:**
- `ai_detected_plate` — رقم اللوحة المُستخرَج من الصورة
- `ai_incident_type` — نوع الحادثة المُكتشَف
- `ai_severity_score` — درجة الخطورة (1–5، integer)
- `ai_damage_assessment` — تقييم الأضرار (text)
- `ai_summary` — ملخص شامل (text)
- `ai_is_duplicate` — هل البلاغ مكرّر؟ (boolean)
- `ai_duplicate_of` — معرّف البلاغ الأصلي (مفتاح أجنبي ذاتي)
- `ai_analyzed_at` — وقت إجراء التحليل (datetime)

**Casts:** `status` → `ReportStatus`, `assigned_department` → `Department`, `ai_is_duplicate` → boolean, `ai_severity_score` → integer, `ai_analyzed_at` → datetime.

**العلاقات:** `citizen()`, `vehicle()`, `violations()`, و **🆕 `duplicateOf()`** → `BelongsTo` (نفس الجدول عبر `ai_duplicate_of`).

---

#### 4.1.7 `Vehicle.php` — نموذج المركبة

**الحقول:** `citizen_id`, `plate_number` (فريد), `vehicle_type`, `make`, `model_name`, `model_year`, `chassis_number`, `engine_number`, `color`, `registration_expiry` (cast date), `insurance_status`.
**العلاقات:** `citizen()`, `reports()`, `violations()`.

---

#### 4.1.8 `TrafficViolation.php` — نموذج المخالفة ⭐ (محدّث بدعم إيصال الدفع)

**الحقول:** `citizen_id`, `vehicle_id`, `police_id`, `report_id`, `violation_type`, `description`, `fine_amount` (decimal:2), `status` (Enum), `issued_at`, `due_date`, **🆕 `payment_receipt_path`** (مسار إيصال الدفع المرفوع).

**Casts:** `status` → `ViolationStatus`, `fine_amount` → decimal:2, `issued_at` → datetime, `due_date` → date.
**العلاقات:** `citizen()`, `vehicle()`, `police()`, `report()`.

---

#### 4.1.9 `ActivityLog.php` — نموذج سجل النشاط ⭐ (محدّث لدعم كل المنفّذين)

**الجدول:** `activity_logs`. أصبح يسجّل نشاطات **كل أنواع المستخدمين** وليس المدير فقط.

**الحقول:** `admin_id` (أصبح nullable), **🆕 `actor_type`** (admin/police/citizen/system), **🆕 `actor_name`**, `action_type` (create/update/delete/view/payment/status_change), `target_table`, `description`.
**العلاقة:** `admin()` → `BelongsTo`.

---

#### 4.1.10 `Scopes/DepartmentScope.php` — نطاق القسم

نطاق استعلام عالمي يقيّد النتائج تلقائياً بحسب قسم الشرطي المسجّل دخوله.

---

### 4.2 التعداد (Enums) — `app/Enums/`

> جميع الـ Enums أصبحت تستخدم مفاتيح الترجمة `filament.enums.*` بدلاً من `messages.*` لدعم أفضل لتعدد اللغات.

#### 4.2.1 `Department.php`
القيم: `HighwayPatrol` = `highway_patrol`، `TrafficPolice` = `traffic_police`، `LocalPolice` = `local_police`. **الدوال:** `label()`.

#### 4.2.2 `ReportStatus.php`
القيم: `New`، `InProgress`، `Resolved`، `Rejected`. **الدوال:** `label()`، `color()` (gray/warning/success/danger).

#### 4.2.3 `ViolationStatus.php` ⭐ (محدّث)
القيم:
- `Unpaid` = `unpaid` — غير مدفوعة
- `Paid` = `paid` — مسدّدة
- **🆕 `PendingVerification` = `pending_verification`** — قيد المراجعة (بانتظار التحقق من الإيصال)
- `Canceled` = `canceled` — ملغاة

**الدوال:** `label()`، `isUnpaid()`، `color()` (danger/success/warning/gray).

---

### 4.3 المتحكمات (Controllers) — `app/Http/Controllers/` ⭐ (Zero-Logic)

> جميع المتحكمات نحيفة: تستقبل الطلب وتمرّره إلى طبقة الخدمة (Service Layer). لا تحتوي على منطق أعمال.

#### 4.3.1 `Auth/LoginController.php`
- `showLoginForm()` / `login()` / `logout()` — إعادة توجيه حسب الدور (Admin→`/admin`, Police→`/police`, Citizen→لوحة المواطن).

#### 4.3.2 `Auth/RegisterController.php`
- `register()` — ينشئ المستخدم ضمن **معاملة قاعدة بيانات**: المواطن يُفعّل فوراً، الشرطة `is_active=false` (تحتاج تفعيلاً من المدير).

#### 4.3.3 `Citizen/DashboardController.php`
يعرض لوحة معلومات المواطن مع إحصائيات ورسوم بيانية (Chart.js): عدد المركبات/البلاغات/المخالفات، توزيع المخالفات حسب الحالة والنوع، البلاغات حسب الحالة، الغرامات، الاتجاه الشهري (6 أشهر)، المركبات حسب النوع.

#### 4.3.4 `Citizen/ReportController.php` ⭐
- `index()` — قائمة البلاغات مع بحث/تصفية/ترتيب/تقسيم صفحات (10/صفحة).
- `create()` — نموذج الـ Wizard مع قائمة مركبات المواطن.
- `store(StoreReportRequest)` — يرفع الصورة والفيديو، يستخدم `ReportRoutingService` (الذي يطلق حدث `ReportCreated` → يبدأ تحليل AI تلقائياً)، يُرجع رقم تتبّع `RPT-XXXXXX`.
- `show()` — عرض مع التحقق من الملكية (المُبلّغ أو المخالف).
- `searchVehicles()` — بحث AJAX عن مركبات مستخدمين آخرين.

#### 4.3.5 `Citizen/ViolationController.php` ⭐ (محدّث بدعم إيصال الدفع)
- `index()` / `show()` — قائمة وعرض المخالفات.
- **`mockPay()`** — 🆕 الآن يتطلب **رفع إيصال دفع** (صورة حتى 5MB). بدلاً من تغيير الحالة إلى `paid` مباشرة:
  1. يخزّن الإيصال في `receipts/`
  2. يضبط `payment_receipt_path`
  3. يغيّر الحالة إلى `pending_verification` (قيد المراجعة)
  4. يسجّل النشاط عبر `ActivityLogger`

#### 4.3.6 `Citizen/VehicleController.php`
CRUD كامل للمركبات عبر `VehicleService` مع حماية `VehiclePolicy`.

#### 4.3.7 `Citizen/ProfileController.php`
- `edit()` / `updateInfo()` / `updatePassword()` — إدارة الملف الشخصي.

---

### 4.4 طلبات النموذج (Form Requests) — `app/Http/Requests/`

| الملف | القواعد الرئيسية |
|-------|------------------|
| `RegisterRequest` | بيانات مشتركة + بيانات المواطن (national_id فريد) أو الشرطة |
| `StoreReportRequest` ⭐ | أضاف `location_type` (in_city/highway)، `video` (mp4/mov/avi/webm حتى 50MB)، `unknown_plate` |
| `StoreVehicleRequest` | بيانات المركبة الجديدة |
| `UpdateVehicleRequest` | مثل Store مع استثناء المركبة الحالية من شرط التفرّد |

---

### 4.5 طبقة الخدمة (Services) — `app/Services/` ⭐ (موسّعة بشكل كبير)

#### 4.5.1 `ActivityLogger.php` 🆕 — مسجّل النشاطات المركزي
خدمة موحّدة لتسجيل كل النشاطات مع تحديد المنفّذ تلقائياً:
- `log(action, table, description, ?user)` — يحدد المنفّذ من المستخدم المصادق عليه ويحلّل نوعه (admin/police/citizen) واسمه.
- `system(action, table, description)` — لتسجيل العمليات التي ينفّذها النظام تلقائياً.
- يحلّ `admin_id` فقط إذا كان المنفّذ مديراً.
- محميّ بـ try-catch لمنع فشل التسجيل من كسر العملية الأساسية.

#### 4.5.2 `ReportRoutingService.php` ⭐ — محرك التوجيه الذكي
- `determineDepartment(reportType, locationType): Department` — خوارزمية التوجيه.
- `createReport(array $data): Report` — ينشئ البلاغ ضمن **معاملة DB** ثم **يُطلق حدث `ReportCreated`** (مما يُفعّل تلقائياً تحليل الذكاء الاصطناعي).

| نوع البلاغ | الموقع | القسم |
|-------------|--------|-------|
| `security_threat` | — | LocalPolice |
| `traffic_jam` | — | TrafficPolice |
| `accident` / `hazard` | inside city | TrafficPolice |
| `accident` / `hazard` | highway | HighwayPatrol |

#### 4.5.3 `ViolationService.php` ⭐ (محدّث)
- `issueFromReport()` — يُصدر مخالفة من بلاغ ضمن معاملة DB، **ثم يسجّل النشاط** عبر `ActivityLogger`.
- `pay()` — يحدّث الحالة إلى `Paid` ويسجّل نشاط الدفع.

#### 4.5.4 `VehicleService.php`
- `create()` / `update()` / `delete()`.

#### 4.5.5 نظام الذكاء الاصطناعي 🆕 (انظر القسم 11 للتفصيل)
- `AiService.php` — عميل HTTP لبوابة LiteLLM.
- `ReportAiAnalyzer.php` — محرك التحليل الشامل.

#### 4.5.6 محرك التحليلات 🆕 (انظر القسم 12 للتفصيل)
- `Analytics/AnalyticsService.php` — KPIs + تنبؤ + نقاط ساخنة.

---

### 4.6 المراقبون (Observers) — `app/Observers/` 🆕

#### `ReportObserver.php` 🆕
مراقب لنموذج `Report`، مسجّل في `AppServiceProvider`:
- `updated()` — عند تغيير حقل `status` (يفعله الشرطي عادةً)، يسجّل نشاط `status_change` يصف الانتقال من الحالة القديمة إلى الجديدة عبر `ActivityLogger`.

---

### 4.7 الأحداث والمستمعون (Events & Listeners) ⭐

#### `Events/ReportCreated.php`
حدث يُطلق عند إنشاء بلاغ جديد. يحمل نسخة `Report`.

#### المستمعون (يُكتشفون تلقائياً من `app/Listeners` — **غير مسجّلين يدوياً** لتجنب التكرار):
| المستمع | الوظيفة | النوع |
|---------|---------|-------|
| `LogReportCreation` | يسجّل إنشاء البلاغ في `activity_logs` | متزامن |
| **🆕 `AnalyzeReportWithAi`** | يُجري تحليل الذكاء الاصطناعي على البلاغ | **غير متزامن (ShouldQueue)** |

> ملاحظة معمارية: التعليقات في `AppServiceProvider` توضّح أن المستمعين يُكتشفون تلقائياً، والتسجيل اليدوي كان يسبب **تنفيذاً مزدوجاً**.

---

### 4.8 السياسات (Policies) — `app/Policies/`
- **`VehiclePolicy`** — حماية مركبات المواطن (view/update/delete لمالكها فقط).
- **`ReportPolicy`** — تحديث البلاغ مسموح فقط للشرطي الذي يطابق قسمه القسم المُسنَد.

---

### 4.9 الاستثناءات (Exceptions) — `app/Exceptions/`
`ReportRoutingException` — استثناء مخصّص عند نوع بلاغ غير معروف (`unknownReportType()`).

---

### 4.10 مزودو الخدمة (Providers) — `app/Providers/`

#### `AppServiceProvider.php` ⭐
- **لا يسجّل مستمعي `ReportCreated` يدوياً** (يُكتشفون تلقائياً).
- يسجّل `ReportObserver` لنموذج `Report`.
- يسجّل مستمع حدث تغيير اللغة في Filament (`LocaleChanged`).
- `Gate::before` يمنح المديرين صلاحية مطلقة.

#### `Filament/AdminPanelProvider.php`
- المسار `/admin`، اللون **Amber**، الوضع الداكن مفعّل، شعار `sh.png`.
- يكتشف الموارد + **الصفحات** (`Admin/Pages`) + الأدوات تلقائياً.

#### `Filament/PolicePanelProvider.php`
- المسار `/police`، اللون **Purple** (محدّث من Blue)، شعار `sh.png`.

#### `FilamentLocaleServiceProvider.php`
- يهيّئ مبدّل اللغة: لغتان (en/ar)، يظهر خارج اللوحات (في صفحات الدخول).

---

### 4.11 موارد وصفحات Filament — `app/Filament/`

#### 4.11.1 لوحة المدير (Admin Panel)

##### الموارد (Resources):

| المورد | النموذج | الميزات المميّزة |
|--------|---------|------------------|
| `UserResource` | User | CRUD كامل + **🆕 6 RelationManagers** لعرض 360° للمواطن (Reports, Vehicles, Violations, CitizenData, PoliceData, AdminData) + أقسام ديناميكية حسب الدور + تحديث `is_active` تلقائياً عند اختيار دور الشرطة |
| `VehicleResource` | Vehicle | عرض/تعديل/حذف + 🆕 `CitizenRelationManager` + 🆕 صفحة View + ColorPicker للون |
| `ReportResource` | Report | عرض فقط + **🆕 قسم تحليل AI غني** + 🆕 عارض الخريطة + عارض الفيديو + 🆕 `CitizenRelationManager` + أعمدة AI (severity badge, duplicate) |
| `TrafficViolationResource` | TrafficViolation | CRUD كامل + **🆕 عرض إيصال الدفع** + 🆕 RelationManagers (Citizen, Police) + 🆕 صفحة View + فلترة بالتاريخ |
| `ActivityLogResource` | ActivityLog | قراءة فقط + **🆕 عرض المنفّذ (actor_display)** مع لون حسب النوع + ألوان أنواع الإجراءات (create/update/status_change/payment/delete) |

##### 🆕 الصفحات المخصّصة (Pages) — `Admin/Pages/`:

| الصفحة | الوصف |
|--------|-------|
| **`AdvancedAnalytics`** | منصة التحليلات المتقدمة الكاملة (انظر القسم 12) |
| **`CustomReportBuilder`** | منشئ التقارير المخصّصة + تصدير (انظر القسم 14) |

##### الأدوات (Widgets) — `Admin/Widgets/`:

| الملف | النوع | الوصف |
|-------|-------|-------|
| `StatsOverview` | إحصائيات | مستخدمون، بلاغات، غير محلولة، غرامات معلقة، مخالفات الأسبوع |
| **🆕 `AnalyticsKpiOverview`** | إحصائيات | معدّل الحل، متوسط زمن الاستجابة، نسبة المخالفات/سائق، أسوأ منطقة |
| `ReportsByDepartmentChart` | Doughnut | توزيع البلاغات حسب الأقسام |
| `ViolationsOverTimeChart` | Line | المخالفات خلال آخر 7 أيام |
| `LatestUnresolvedReportsTable` | جدول | آخر 5 بلاغات غير محلولة |

---

#### 4.11.2 لوحة الشرطة (Police Panel)

##### `AssignedReportResource` ⭐
- `canCreate(): false` — البلاغات تأتي من المواطنين فقط.
- **تصفية تلقائية** (`modifyQueryUsing`): يُظهر فقط بلاغات القسم.
- **تحديث تلقائي** `poll('10s')`.
- **🆕 قسم تحليل AI** كامل (severity, plate, summary, duplicate).
- **🆕 عارض الخريطة + الفيديو + الصور**.
- `VehicleRelationManager` مرفق.
- 🆕 **في صفحة العرض (`ViewAssignedReport`)** إجراءّان في الترويسة:
  - **"إعادة التحليل" (Re-analyze)** — يعيد تشغيل `ReportAiAnalyzer`.
  - **"إصدار مخالفة" (Issue Violation)** — Modal يعبّئ تلقائياً citizen_id/vehicle_id/report_id (مرئي عند status=resolved ووجود مركبة)، يستخدم `ViolationService`.

##### `TrafficViolationResource` ⭐
- `canDelete(): false`.
- **تصفية تلقائية**: مخالفات الشرطي نفسه فقط.
- نموذج إنشاء (`CreateTrafficViolation`) يضبط `police_id` تلقائياً من المستخدم الحالي.
- 🆕 عرض إيصال الدفع.

##### الأدوات (Widgets) — `Police/Widgets/`:
`PoliceStatsOverview` (بلاغات القسم/قيد الانتظار/مخالفاتي)، `ReportsChart` (Doughnut حسب الحالة)، `LatestAssignedReports` (آخر 5 بلاغات محوّلة).

---

## 5. المسارات (Routes) — `routes/web.php`

### Rate Limiting
```php
RateLimiter::for('reports', fn ($request) => Limit::perMinute(3)->by($request->ip()));
```

### ملخص المسارات:

| المسار | الطريقة | المتحكم | الوصف |
|--------|---------|---------|-------|
| `/` | GET | — | الصفحة الرئيسية |
| `/login`, `/register`, `/logout` | GET/POST | Auth | المصادقة |
| `/citizen/dashboard` | GET | DashboardController | لوحة المواطن |
| `/citizen/vehicles` `{id}` | GET/POST/PUT/DELETE | VehicleController | المركبات |
| `/citizen/reports` `/create` `{id}` | GET/POST | ReportController | البلاغات |
| **🆕 `/citizen/reports/search-vehicles`** | GET | ReportController@searchVehicles | بحث AJAX عن المركبات |
| `/citizen/violations` `{id}` | GET | ViolationController | المخالفات |
| `/citizen/violations/{id}/pay` | POST | ViolationController@mockPay | رفع إيصال الدفع |
| `/citizen/profile` `/info` `/password` | GET/PUT | ProfileController | الملف الشخصي |

**ملاحظات:** جميع المسارات ملفوفة بـ `LaravelLocalization`؛ مسارات الدخول/التسجيل محمية بـ `guest`؛ مسارات المواطن محمية بـ `auth`؛ إنشاء البلاغات محدود بـ `throttle:reports` (3/دقيقة).

---

## 6. قاعدة البيانات (Database)

### 6.1 المخطط العام

```
users (1) ──→ (N) roles
users (1) ──→ (1) citizens_data / police_data / admins_data
citizens_data (1) ──→ (N) vehicles / reports / traffic_violations
vehicles (1) ──→ (N) reports / traffic_violations
reports (1) ──→ (N) traffic_violations
reports (1) ──→ (1) reports [علاقة ذاتية: ai_duplicate_of] 🆕
police_data (1) ──→ (N) traffic_violations
admins_data (1) ──→ (N) activity_logs
```

### 6.2 التهجيرات (Migrations) ⭐ (مع المستحدثة)

| التهجيرة | الوصف |
|----------|-------|
| `0001_..._create_*` | الجداول الأساسية (users, roles, citizens_data, police_data, admins_data, vehicles, reports, activity_logs, traffic_violations) |
| `2026_06_04_..._add_video_url_to_reports` | 🆕 حقل الفيديو في البلاغات |
| `2026_06_04_..._add_reported_plate_to_reports` | 🆕 لوحة المركبة المُبلَّغ عنها |
| `2026_06_08_..._add_new_fields_to_vehicles` | 🆕 حقول المركبة الإضافية |
| **`2026_06_23_..._add_ai_analysis_to_reports`** | **🆕 حقول تحليل AI الثمانية** (plate, type, severity, damage, summary, duplicate, duplicate_of FK, analyzed_at) |
| **`2026_06_23_..._improve_activity_logs_for_all_actors`** | **🆕 إضافة actor_type, actor_name + جعل admin_id nullable** |
| **`2026_06_23_..._add_payment_receipt_path_to_traffic_violations`** | **🆕 مسار إيصال الدفع** |

### 6.3 البذور (Seeders)
| الملف | الوصف |
|-------|-------|
| `DatabaseSeeder` | المنسّق الرئيسي (ينفّذ البذور بالترتيب مع تمرير المراجع بينها) |
| `RoleSeeder` | الأدوار الافتراضية |
| `AdminDataSeeder`, `CitizenDataSeeder`, `PoliceDataSeeder` | بيانات تجريبية |
| `VehicleSeeder`, `ReportSeeder`, `TrafficViolationSeeder` | بيانات تجريبية |
| `ActivityLogSeeder` | سجلات نشاط تجريبية |

---

## 7. العروض (Views) — `resources/views/`

```
resources/views/
├── layouts/app.blade.php           # القالب الأساسي (RTL/LTR + Toasts + Dark mode)
├── components/ (navbar, footer, dark-mode-toggle, language-switcher)
├── auth/ (login, register)
├── citizen/
│   ├── dashboard.blade.php
│   ├── report-wizard.blade.php     # Wizard متعدد الخطوات (GPS + معاينة الصورة)
│   ├── profile/edit.blade.php
│   ├── vehicles/ (index, show)
│   ├── reports/ (index, show)
│   └── violations/ (index, show)   # 🆕 مع رفع إيصال الدفع
├── errors/403.blade.php
├── welcome.blade.php
└── filament/                       # 🆕 عروض Filament المخصّصة
    ├── admin/pages/                # 🆕 صفحات التحليلات ومنشئ التقارير
    ├── analytics/report-pdf.blade.php  # 🆕 قالب تقرير PDF (RTL)
    └── components/map-viewer.blade.php # 🆕 عارض الخريطة (Leaflet)
```

---

## 8. نظام الصلاحيات والأمان

- **المصادقة:** بريد + كلمة مرور (hashed)، remember me، إعادة توليد معرّف الجلسة.
- **التفويض:** أدوار + Policies (Vehicle/Report) + `Gate::before` للمدير + `canAccessPanel()`.
- **Rate Limiting:** 3 بلاغات/دقيقة لكل IP.
- **التحقق من المدخلات:** Form Requests صارمة + CSRF.
- **🆕 التحقق من المدفوعات:** الدفع لا يُعتمد فوراً، بل يدخل في حالة `pending_verification` بانتظار المراجعة.
- **🆕 عزل فشل الذكاء الاصطناعي:** تحليل AI يُنفّذ في طابور منفصل ومحاط بـ try-catch؛ فشله لا يكسر إنشاء البلاغ.

---

## 9. سير العمل (Workflow)

### 9.1 سير عمل البلاغ (محدّث بالذكاء الاصطناعي)

```
المواطن يُنشئ بلاغ (GPS + صورة/فيديو)
        ↓
ReportRoutingService يحدد القسم + يحفظ البلاغ + يطلق ReportCreated
        ↓ (متوازي)
   ┌────────────────────────────────┬───────────────────────────────┐
   ↓                                ↓                               ↓
LogReportCreation يسجّل النشاط   🆕 AnalyzeReportWithAi (طابور)   البلاغ يظهر للشرطة
                                 يحدّد: severity, plate, summary,
                                 duplicate detection
        ↓
البلاغ يظهر في لوحة الشرطة (poll 10s) مع بيانات AI
        ↓
الشرطي يراجع (ويستطيع إعادة التحليل) → in_progress
        ↓
يُصدر مخالفة (Issue Violation) أو يحل (resolved) أو يرفض (rejected)
        ↓
ReportObserver يسجّل تغيير الحالة تلقائياً
```

### 9.2 سير عمل المخالفة (محدّث بالتحقق)

```
الشرطي يُصدر مخالفة (status = unpaid) ← ViolationService يسجّل النشاط
        ↓
تظهر للمواطن
        ↓
المواطن يرفع إيصال الدفع (receipt) ← status = pending_verification
        ↓
المراجعة (Admin/Police) → paid أو رفض
```

---

## 10. ميزات تقنية مهمة

1. **🧠 تحليل البلاغات بالذكاء الاصطناعي** — تحليل بصري ونصي متعدد الوسائط.
2. **📊 منصة تحليلات متقدمة** — KPIs + تنبؤ بالانحدار الخطي + نقاط ساخنة.
3. **📑 تصدير متعدد الصيغ** — Excel (متعدد الأوراق) + CSV + PDF عربي.
4. **💳 نظام تحقق من المدفوعات** — برفع الإيصالات.
5. **📝 سجلات نشاط شاملة** — لكل المنفّذين (admin/police/citizen/system) عبر Observers + Listeners.
6. **🗺️ تكامل الخرائط** — Leaflet + OpenStreetMap.
7. **نظام توجيه تلقائي** — `ReportRoutingService`.
8. **لوحتان Filament منفصلتان** — Admin + Police.
9. **عرض 360° للمواطن** — عبر RelationManagers في UserResource.
10. **معمارية مؤسسية صارمة** — Zero-Logic Controllers + Service Layer + strict_types.
11. **معالجة غير متزامنة** — طوابير Laravel لتحليل AI.
12. **تعدد اللغات كامل** — عربي/إنجليزي مع RTL/LTR.

---

## 11. 🧠 نظام الذكاء الاصطناعي (AI Analysis Engine)

نظام ذكاء اصطناعي متكامل يحلّل البلاغات تلقائياً عند إنشائها.

### 11.1 المكوّنات

#### `AiService.php` — عميل بوابة LiteLLM
- يتصل ببوابة **LiteLLM Proxy** عبر HTTP (مهلة 60 ثانية).
- يقرأ الإعداد من `config/ai.php` و `.env` (`AI_API_URL`, `AI_API_KEY`, `AI_MODEL` افتراضي `gemini-3-flash-preview`).
- **دعم متعدد الوسائط (Multimodal):**
  - `chat(messages, temperature)` — محادثة نصية تُرجع المحتوى.
  - `buildImageContent(path)` — يحوّل صورة من التخزين إلى base64 data-URL.
  - `buildVideoContent(path)` — يحوّل فيديو (يتخطّاه إذا تجاوز 20MB لتجنّب مشاكل الذاكرة).
- محميّ بالكامل بـ try-catch + تسجيل أخطاء مفصّل.

#### `ReportAiAnalyzer.php` — محرك التحليل
يُجرى تحليل شامل في استدعاء واحد يُرجع JSON منظّم:
- **درجة الخطورة (severity_score):** 1–5 (يُحصر بين 1 و5).
- **رقم اللوحة المكتشَف (detected_plate):** من الصورة.
- **نوع الحادثة المكتشَف (incident_type).**
- **تقييم الأضرار (damage_assessment).**
- **الملخص الشامل (summary).**

**🆕 كشف التكرار (Duplicate Detection):**
1. بحث جغرافي: بلاغات من نفس النوع خلال 24 ساعة ضمن ~500 متر.
2. تأكيد بالذكاء الاصطناعي: يقارن وصفَي البلاغين ويقرّر إن كانا لنفس الحادثة.
3. يضبط `ai_is_duplicate` و `ai_duplicate_of`.

### 11.2 آلية التشغيل
```
ReportCreated (event)
   → AnalyzeReportWithAi (ShouldQueue — غير متزامن)
      → ReportAiAnalyzer::analyze()
         → بناء محتوى الوسائط + استدعاء AI + كشف التكرار + تحديث البلاغ
```
- يُتجاهل التحليل إذا لم يوجد وصف ولا وسائط.
- الفشل مُسجَّل في اللوغات ولا يكسر التدفق.

### 11.3 الواجهة في Filament
- قسم "تحليل AI" قابل للطي في كل من Admin وPolice (مع أيقونة `heroicon-o-cpu-chip`).
- عرض درجة الخطورة بشريط ملوّن تفاعلي + ألوان حسب الدرجة (1=أخضر … 5=أحمر).
- عرض اللوحة المكتشَفة بشارة monospace.
- روابط للبلاغ الأصلي عند التكرار.
- أعمدة جدول: `ai_severity_score` (badge) و `ai_is_duplicate` (boolean icon).
- إجراء **"إعادة التحليل"** في صفحة عرض البلاغ (Police).

---

## 12. 📊 منصة التحليلات المتقدمة (Advanced Analytics)

منصة تحليلات بيانات كاملة تدعم اتخاذ القرارات المبنية على الأدلة.

### 12.1 `AnalyticsService.php` — المحرك
خدمة غنية مع **تخزين مؤقت داخلي** (memoization) لتفادي إعادة الحساب:

| الدالة | الوصف |
|--------|-------|
| `kpis(start, end)` | مؤشرات الأداء: عدد البلاغات/المخالفات، متوسط زمن الاستجابة (دقائق)، معدّل الحل %، مخالفة/سائق، معدّل التحصيل %، إجمالي/مُحصَّل/معلّق الغرامات |
| `monthlyTrend()` | اتجاه شهري (بلاغات + مخالفات) عبر النطاق |
| `reportDistribution()` / `violationDistribution()` | توزيع حسب عمود معيّن |
| `regionCompliance()` | ترتيب الامتثال حسب المنطقة (يستخرج المدينة من النص: دمشق، حلب، حمص، ...) |
| `bestRegions()` / `worstRegions()` | أفضل/أسوأ المناطق |
| **`forecastIncidents()`** | **🆕 تنبؤ بالحوادث المستقبلية** عبر انحدار خطي (least-squares) على بيانات 12 شهراً + توجّه (صاعد/هابط/مستقر) |
| **`hotspots()`** | **🆕 ساعات الذروة** (توزيع المخالفات حسب الساعة) + **أخطر 5 مناطق** |
| `compareWithPrevious()` | مقارنة النطاق الحالي بنطاق مكافئ سابق (مطلق + نسبة %) |
| `customReport()` | بناء مجموعة بيانات حسب معايير المستخدم |

### 12.2 صفحة `AdvancedAnalytics`
- **فلاتر:** نطاق تاريخ + خيارات سريعة (آخر 30 يوماً، هذا الشهر، الشهر الماضي، آخر 3 أشهر، هذا العام).
- **مقارنة** مع الفترة السابقة (toggle).
- **أقسام:** KPIs، الاتجاهات، المقارنة، التحليل الإقليمي، **التنبؤات الذكية**، النقاط الساخنة، التوزيعات.
- **تصدير:** Excel (متعدد الأوراق) + CSV + **PDF عربي RTL** (عبر mPDF).

### 12.3 صفحة `CustomReportBuilder`
- بناء تقرير حسب: نوع البيانات (بلاغات/مخالفات/مجتمعين) + حالة + نطاق تاريخ.
- عرض النتائج + تصدير Excel/CSV.

---

## 13. 💳 التحقق من المدفوعات (Payment Verification)

نظام دفع محاكى مع طبقة تحقق:

1. المواطن يضغط "ادفع الآن" ويُرفع **إيصال دفع** (صورة حتى 5MB).
2. `ViolationController::mockPay` يخزّن الإيصال في `receipts/`.
3. الحالة تتحول إلى **`pending_verification`** (قيد المراجعة) — وليست `paid`.
4. يُسجَّل نشاط "رفع إيصال دفع ... بانتظار التحقق".
5. يظهر الإيصال في لوحتي Admin و Police (ImageColumn + FileUpload معطّل للعرض).
6. يتم التحقق وتغيير الحالة إلى `paid` أو رفضها.

---

## 14. 📝 التقرير والتصدير (Reporting & Exports)

طبقة تصدير احترافية في `app/Exports/`:

| الملف | الوصف |
|-------|-------|
| `AnalyticsExport` | ملف **Excel متعدد الأوراق** (KPIs + Trend + Region) عبر `WithMultipleSheets` |
| `AnalyticsSheets/KpiSheet` | ورقة المؤشرات مع المقارنة |
| `AnalyticsSheets/TrendSheet` | ورقة الاتجاه الشهري |
| `AnalyticsSheets/RegionSheet` | ورقة الامتثال الإقليمي |
| `AnalyticsCsvExport` | تصدير CSV مُسطّح (قسم/مقياس/قيمة) |
| `CustomReportExport` | تصدير التقرير المخصّص (`FromCollection` + `WithHeadings` + `WithMapping`) |

**تصدير PDF:** عبر **mPDF** مع دعم RTL (`SetDirectionality('rtl')`)، خط `dejavusans`، وقالب `filament/analytics/report-pdf.blade.php` — يحلّ مشكلة العربية في DomPDF التقليدي.

---

## 15. 🗺️ التكامل مع الخرائط (Map Integration)

`resources/views/filament/components/map-viewer.blade.php`:
- مكوّن Blade مخصّص يُحمَّل ديناميكياً في نماذج البلاغ (Admin + Police).
- يستخدم **Leaflet.js 1.9.4** + بلاط **OpenStreetMap**.
- يعرض علامة على إحداثيات البلاغ (lat/lng) مع تكبير افتراضي 15.
- يُحمّل CSS/JS مرة واحدة فقط (تحقّق من وجودهما).
- يظهر رسالة لطيفة عند غياب الإحداثيات.

---

## الخلاصة

تطوّر مشروع **Traffic App** من نظام بلاغات أساسي إلى **منصة مرورية مؤسسية متكاملة** تجمع:
- **الذكاء الاصطناعي** لتحليل البلاغات وكشف التكرار تلقائياً.
- **التحليلات التنبؤية** لدعم اتخاذ القرار.
- **التصدير الاحترافي** متعدد الصيغ (Excel/CSV/PDF عربي).
- **نظام تحقق من المدفوعات** ومعمارية أمان صارمة.
- **معمارية نظيفة** (Zero-Logic Controllers، Service Layer، Observers، strict_types) تضمن القابلية للصيانة والتوسّع.
