# تقرير شامل عن مشروع إدارة المرور (Traffic App)

---

## 1. نظرة عامة على المشروع
هذا المشروع عبارة عن نظام متكامل لإدارة المرور والبلاغات (Traffic Injaz App)، يهدف إلى ربط المواطنين بضباط الشرطة وإدارة المرور المركزية في **سوريا**. يعتمد النظام على توزيع الصلاحيات والأدوار، ويدعم تعدد اللغات (العربية والإنجليزية). العملة المستخدمة هي **الليرة السورية (SYP / ل.س)**.

ينقسم المشروع إلى ثلاث واجهات رئيسية:
1. **واجهة المواطن (Frontend):** تتيح للمواطن تسجيل مركباته، استعراض المخالفات، ورفع البلاغات المرورية أو الحوادث.
2. **لوحة تحكم الشرطة (Police Panel):** مخصصة للضباط لاستقبال البلاغات المُحالة إليهم ومعالجتها، وتسجيل المخالفات المرورية على المركبات.
3. **لوحة تحكم الإدارة (Admin Panel):** واجهة مركزية للمديرين للإشراف على كافة أقسام النظام (المستخدمين، البلاغات، المخالفات، سجلات الأنشطة) واستعراض الإحصائيات.

---

## 2. التقنيات المستخدمة (Tech Stack)
- **إطار العمل الأساسي:** Laravel 12 (الإصدار الحالي 12.58.0).
- **لوحات التحكم:** Filament v3 (المبني على Livewire و Tailwind CSS).
- **تعدد اللغات (Localization):** استخدام `mcamara/laravel-localization` للواجهة الأمامية للمواطن، و `bezhansalleh/filament-language-switch` للوحات التحكم.
- **إدارة الجلسات وتسجيل الدخول:** مصادقة مركزية (Centralized Authentication) مبنية عبر إطار عمل Laravel ومُخصصة لتوجيه المستخدمين حسب أدوارهم.
- **العملة:** الليرة السورية (SYP).

---

## 3. نتائج فحص الجودة (QA & Syntax Check)

### فحص الأخطاء النحوية (Syntax Check)
تم فحص جميع ملفات PHP في المشروع (`app/`, `database/`, `routes/`, `resources/views/`, `lang/`) باستخدام `php -l`:
- **النتيجة: ✅ لا توجد أي أخطاء نحوية في جميع الملفات.**

### فحص المسارات (Routes)
تم التحقق من جميع المسارات المسجلة (54 مسار) وتعمل بشكل صحيح:
- مسارات المصادقة (`login`, `register`, `logout`)
- مسارات لوحة الإدارة (`admin/*`)
- مسارات لوحة الشرطة (`police/*`)
- مسارات واجهة المواطن (`citizen/*`)

### فحص بنية قاعدة البيانات
- 15 ملف تهجير (Migration) لتأسيس جميع الجداول
- 9 ملفات سيدر (Seeder) لتعبئة البيانات التجريبية

---

## 4. الهيكلية العامة للمشروع
يتبع المشروع هيكل Laravel القياسي، ولكن تم تخصيصه ليتماشى مع متطلبات النظام:
- `routes/web.php`: ملف المسارات الرئيسي.
- `database/migrations/`: 15 ملف تهجير لبناء جداول قواعد البيانات.
- `database/seeders/`: 9 ملفات سيدر لتعبئة البيانات التجريبية السورية.
- `resources/views/`: ملفات العرض (Blade Templates) الخاصة بواجهة المواطن.
- `lang/ar/` و `lang/en/`: ملفات الترجمة العربية والإنجليزية.
- `app/`: **القلب النابض للمشروع**، وفيه تتواجد كافة ملفات المنطق البرمجي.

---

## 5. تحليل مفصل لمجلد `app/` (الهرمية والمنطق)
تم بناء مجلد `app` بطريقة منظمة جداً تتبع مبدأ فصل الاهتمامات (Separation of Concerns). يحتوي على 10 مجلدات فرعية:

```
app/
├── Enums/           → الثوابت والحالات (Enums)
├── Events/          → الأحداث (Events)
├── Exceptions/      → الاستثناءات المخصصة
├── Filament/        → لوحات تحكم Filament
│   ├── Admin/       → لوحة الإدارة
│   ├── Pages/       → صفحات مشتركة
│   └── Police/      → لوحة الشرطة
├── Http/            → المتحكمات والطلبات
│   ├── Controllers/ → المتحكمات (Auth + Citizen)
│   ├── Middleware/   → الوسطاء (فارغ حالياً)
│   └── Requests/    → طلبات التحقق (Form Requests)
├── Listeners/       → مستمعو الأحداث
├── Models/          → نماذج قاعدة البيانات (Eloquent)
│   └── Scopes/      → نطاقات الاستعلام
├── Policies/        → سياسات الصلاحيات
├── Providers/       → مزودو الخدمات
│   └── Filament/    → مزودو لوحات Filament
└── Services/        → منطق الأعمال (Business Logic)
```

---

### 5.1 المجلد `Models/` (نماذج قواعد البيانات والعلاقات)

يحتوي على 9 نماذج بيانات (Eloquent Models) تعكس جداول قاعدة البيانات:

#### `User.php` (النموذج الرئيسي للمستخدمين)
- **الجدول:** `users`
- **الحقول القابلة للتعبئة:** `username`, `email`, `password`, `role_id`, `is_active`
- **الواجهات المنفذة:** `FilamentUser` (للتحكم بصلاحية الوصول للوحات Filament), `HasName` (لعرض اسم المستخدم في لوحات Filament)
- **العلاقات:**
  - `role()` → ينتمي إلى `Role` (BelongsTo)
  - `citizenData()` → ملف مواطن واحد `CitizenData` (HasOne)
  - `policeData()` → ملف ضابط واحد `PoliceData` (HasOne)
  - `adminData()` → ملف مدير واحد `AdminData` (HasOne)
  - `violations()` → مخالفات عبر `CitizenData` (HasManyThrough)
  - `reports()` → بلاغات عبر `CitizenData` (HasManyThrough)
  - `vehicles()` → مركبات عبر `CitizenData` (HasManyThrough)
- **الدوال المساعدة:**
  - `isCitizen()`: يتحقق إن كان دور المستخدم "مواطن"
  - `isPolice()`: يتحقق إن كان دور المستخدم "شرطة"
  - `isAdmin()`: يتحقق إن كان دور المستخدم "إدارة"
  - `canAccessPanel(Panel $panel)`: يحدد صلاحية الوصول للوحات (Admin→admin, Police→police)
  - `getFilamentName()`: يعيد الاسم الكامل حسب الدور

#### `Role.php` (جدول الأدوار)
- **الجدول:** `roles`
- **الحقول:** `name`, `slug` (citizen, admin, police)
- **العلاقات:** `users()` →_MANY User

#### `CitizenData.php` (بيانات المواطن)
- **الجدول:** `citizens_data`
- **الحقول:** `user_id`, `national_id`, `full_name`, `phone`, `blood_type`
- **العلاقات:**
  - `user()` → BelongsTo User
  - `vehicles()` → HasMany Vehicle (عبر `citizen_id`)
  - `reports()` → HasMany Report (عبر `citizen_id`)
  - `violations()` → HasMany TrafficViolation (عبر `citizen_id`)

#### `PoliceData.php` (بيانات الضابط)
- **الجدول:** `police_data`
- **الحقول:** `user_id`, `badge_number`, `full_name`, `rank`, `department`
- **Casting:** `department` → `Department` Enum
- **العلاقات:**
  - `user()` → BelongsTo User
  - `violations()` → HasMany TrafficViolation (عبر `police_id`)

#### `AdminData.php` (بيانات المدير)
- **الجدول:** `admins_data`
- **الحقول:** `user_id`, `full_name`
- **العلاقات:**
  - `user()` → BelongsTo User
  - `activityLogs()` → HasMany ActivityLog (عبر `admin_id`)

#### `Vehicle.php` (المركبات)
- **الجدول:** `vehicles`
- **الحقول:** `citizen_id`, `plate_number`, `vehicle_type`, `make`, `model_name`, `model_year`, `chassis_number`, `engine_number`, `color`, `registration_expiry`, `insurance_status`
- **Casting:** `registration_expiry` → date
- **العلاقات:**
  - `citizen()` → BelongsTo CitizenData
  - `reports()` → HasMany Report
  - `violations()` → HasMany TrafficViolation

#### `TrafficViolation.php` (المخالفات المرورية)
- **الجدول:** `traffic_violations`
- **الحقول:** `citizen_id`, `vehicle_id`, `police_id`, `report_id`, `violation_type`, `description`, `fine_amount`, `status`, `issued_at`, `due_date`
- **Casting:** `status` → ViolationStatus Enum, `fine_amount` → decimal:2, `issued_at` → datetime, `due_date` → date
- **العلاقات:**
  - `citizen()` → BelongsTo CitizenData
  - `vehicle()` → BelongsTo Vehicle
  - `police()` → BelongsTo PoliceData
  - `report()` → BelongsTo Report

#### `Report.php` (البلاغات)
- **الجدول:** `reports`
- **الحقول:** `citizen_id`, `vehicle_id`, `reported_vehicle_plate`, `assigned_department`, `report_type`, `description`, `latitude`, `longitude`, `location_text`, `image_url`, `video_url`, `status`
- **Casting:** `status` → ReportStatus Enum, `assigned_department` → Department Enum, `latitude/longitude` → decimal:7
- **العلاقات:**
  - `citizen()` → BelongsTo CitizenData
  - `vehicle()` → BelongsTo Vehicle
  - `violations()` → HasMany TrafficViolation

#### `ActivityLog.php` (سجل النشاط)
- **الجدول:** `activity_logs`
- **الحقول:** `admin_id`, `action_type`, `target_table`, `description`
- **العلاقات:** `admin()` → BelongsTo AdminData

#### `Scopes/DepartmentScope.php` (نطاق قسم الشرطة)
- **نطاق Eloquent مخصص** يقوم تلقائياً بتصفية النتائج حسب قسم الضابط المسجل دخوله.
- عند تطبيقه على نموذج، يضيف شرط `WHERE assigned_department = <قسم الضابط>` تلقائياً لجميع الاستعلامات.
- يعمل فقط إذا كان المستخدم الحالي ضابط شرطة (`isPolice()`).

---

### 5.2 المجلد `Enums/` (الثوابت والحالات)

#### `Department.php` (أقسام الشرطة)
```php
enum Department: string
```
- `HighwayPatrol = 'highway_patrol'` → دورية الطرق السريعة
- `TrafficPolice = 'traffic_police'` → شرطة المرور
- `LocalPolice = 'local_police'` → الشرطة المحلية
- يحتوي على دالة `label()` تعيد الترجمة من ملفات اللغة

#### `ReportStatus.php` (حالات البلاغ)
```php
enum ReportStatus: string
```
- `New = 'new'` → جديد
- `InProgress = 'in_progress'` → قيد المعالجة
- `Resolved = 'resolved'` → تم الحل
- `Rejected = 'rejected'` → مرفوض
- يحتوي على `label()` للترجمة و `color()` للون الشارة (gray, warning, success, danger)

#### `ViolationStatus.php` (حالة المخالفة)
```php
enum ViolationStatus: string
```
- `Unpaid = 'unpaid'` → غير مدفوعة
- `Paid = 'paid'` → مدفوعة
- `Canceled = 'canceled'` → ملغاة
- يحتوي على `isUnpaid()` للتحقق، `label()` للترجمة، `color()` للون الشارة

---

### 5.3 المجلد `Events/` (الأحداث)

#### `ReportCreated.php`
- حدث يُطلق عند إنشاء بلاغ جديد
- يحمل كائن `Report` كمعامل (`readonly Report $report`)
- يستخدم خصائص `Dispatchable`, `InteractsWithSockets`, `SerializesModels`

### 5.4 المجلد `Listeners/` (مستمعو الأحداث)

#### `LogReportCreation.php`
- يستمع لحدث `ReportCreated`
- يقوم بإنشاء سجل نشاط `ActivityLog` يوثق إنشاء البلاغ
- يسجل: `admin_id`, `action_type` = 'create', `target_table` = 'reports', ووصف يحتوي على رقم البلاغ ونوعه

### 5.5 المجلد `Exceptions/` (الاستثناءات)

#### `ReportRoutingException.php`
- استثناء مخصص يُطلق عند عدم القدرة على توجيه بلاغ بسبب نوع غير معروف
- يحتوي على دالة `unknownReportType(string $type)` لإنشاء الرسالة

---

### 5.6 المجلد `Services/` (منطق الأعمال)

#### `ReportRoutingService.php` (خدمة توجيه البلاغات)
**الدالة الرئيسية `determineDepartment()`:**
- `security_threat` → الشرطة المحلية (LocalPolice)
- `traffic_jam` → شرطة المرور (TrafficPolice)
- `accident` أو `hazard` على طريق سريع → دورية الطرق (HighwayPatrol)
- `accident` أو `hazard` داخل المدينة → شرطة المرور (TrafficPolice)

**الدالة `createReport()`:**
- تستقبل مصفوفة بيانات البلاغ
- تحدد القسم المناسب تلقائياً باستخدام `determineDepartment()`
- تنشئ البلاغ داخل معاملة قاعدة بيانات (DB Transaction)
- تُطلق حدث `ReportCreated` بعد الإنشاء
- تعيد كائن `Report`

#### `VehicleService.php` (خدمة المركبات)
- `create(CitizenData, array)`: إنشاء مركبة جديدة مرتبطة بمواطن
- `update(Vehicle, array)`: تحديث بيانات مركبة
- `delete(Vehicle)`: حذف مركبة

#### `ViolationService.php` (خدمة المخالفات)
- `issueFromReport(Report, PoliceData, array)`: إصدار مخالفة من بلاغ موجود (داخل معاملة قاعدة بيانات)
- `pay(TrafficViolation)`: تسجيل دفع مخالفة (تحديث الحالة إلى Paid)

---

### 5.7 المجلد `Http/` (المتحكمات والطلبات)

#### 5.7.1 المتحكمات (Controllers)

##### `Controller.php` (المتحكم الأساسي)
- متحكم مجرد (abstract) ترث منه جميع المتحكمات الأخرى.

##### `Auth/LoginController.php` (تسجيل الدخول)
- `showLoginForm()`: عرض صفحة تسجيل الدخول
- `login(Request)`: التحقق من بيانات الدخول وتوجيه المستخدم:
  - مدير → `/admin`
  - شرطة → `/police`
  - مواطن → `citizen.dashboard`
- `logout(Request)`: تسجيل الخروج وإعادة التوجيه للرئيسية

##### `Auth/RegisterController.php` (التسجيل)
- `showRegistrationForm()`: عرض نموذج التسجيل
- `register(RegisterRequest)`: إنشاء حساب مواطن جديد داخل معاملة قاعدة بيانات:
  - إنشاء `User` بدور "مواطن"
  - إنشاء `CitizenData` مرتبط به
  - تسجيل الدخول تلقائياً بعد التسجيل

##### `Citizen/DashboardController.php` (لوحة تحكم المواطن)
- `index()`: يجمع ويعرض إحصائيات شاملة للمواطن:
  - عدد المركبات، البلاغات، المخالفات
  - المخالفات حسب الحالة (لرسم دائري)
  - البلاغات حسب الحالة (لرسم أعمدة)
  - المخالفات حسب النوع (لرسم قطبي)
  - إجمالي الغرامات والغرامات غير المدفوعة (ل.س)
  - المخالفات الشهرية لآخر 6 أشهر (لرسم خطي)
  - المركبات حسب النوع (لرسم أعمدة أفقية)

##### `Citizen/ReportController.php` (إدارة البلاغات)
- `index(Request)`: عرض قائمة البلاغات مع بحث وفلترة وترتيب
- `create()`: عرض نموذج البلاغ (Wizard) مع قائمة مركبات المواطن
- `store(StoreReportRequest)`: إنشاء بلاغ جديد:
  - رفع الصور والفيديو إلى `storage/reports/`
  - معالجة حالة "بدون لوحة"
  - توجيه البلاغ عبر `ReportRoutingService`
  - إنشاء رقم تتبع (RPT-XXXXXX)
- `show(Report)`: عرض تفاصيل بلاغ مع التحقق من الملكية
- `searchVehicles(Request)`: بحث AJAX عن مركبات مواطنين آخرين (للبلاغ عنهم)

##### `Citizen/VehicleController.php` (إدارة المركبات)
- `index(Request)`: عرض قائمة مركبات المواطن مع بحث وفلترة
- `store(StoreVehicleRequest)`: إضافة مركبة جديدة عبر `VehicleService`
- `update(UpdateVehicleRequest, Vehicle)`: تحديث بيانات مركبة مع التحقق من الصلاحية عبر `Gate::authorize`
- `show(Vehicle)`: عرض تفاصيل مركبة مع آخر 10 مخالفات و5 بلاغات
- `destroy(Vehicle)`: حذف مركبة مع التحقق من الصلاحية

##### `Citizen/ViolationController.php` (إدارة المخالفات)
- `index(Request)`: عرض قائمة المخالفات مع بحث وفلترة وترتيب
- `show(TrafficViolation)`: عرض تفاصيل مخالفة مع التحقق من الملكية
- `mockPay(TrafficViolation)`: دفع تجريبي لمخالفة (يعيد JSON):
  - يتحقق من الملكية والحالة
  - يستدعي `ViolationService::pay()`
  - يعيد استجابة نجاح/فشل

#### 5.7.2 طلبات التحقق (Form Requests)

##### `RegisterRequest.php`
- التحقق من: `username` (فريد), `email` (فريد), `password` (مؤكد, 8 حروف), `national_id` (فريد), `full_name`, `phone`, `blood_type` (A+,A-,B+,B-,AB+,AB-,O+,O-)

##### `StoreReportRequest.php`
- التحقق من: `vehicle_id` (اختياري), `reported_vehicle_plate`, `unknown_plate`, `report_type` (accident/hazard/traffic_jam/security_threat), `description` (10 حروف), `latitude/longitude`, `location_type` (in_city/highway), `location_text`, `image` (صورة, 5MB), `video` (mp4/mov/avi/webm, 50MB)

##### `StoreVehicleRequest.php`
- التحقق من: `plate_number` (فريد), `vehicle_type`, `make`, `model_year`, `color`

##### `UpdateVehicleRequest.php`
- نفس StoreVehicleRequest لكن يتجاهل المركبة الحالية في قاعدة التفرد لـ `plate_number`

---

### 5.8 المجلد `Policies/` (سياسات الصلاحيات)

#### `ReportPolicy.php`
- `update(User, Report)`: يسمح فقط لضابط ينتمي لنفس قسم البلاغ بتعديله

#### `VehiclePolicy.php`
- `view(User, Vehicle)`: يسمح فقط لصاحب المركبة بعرضها
- `update(User, Vehicle)`: يسمح فقط لصاحب المركبة بتعديلها
- `delete(User, Vehicle)`: يسمح فقط لصاحب المركبة بحذفها

---

### 5.9 المجلد `Providers/` (مزودو الخدمات)

#### `AppServiceProvider.php`
- يسجل مستمع الحدث `ReportCreated` ← `LogReportCreation`
- يسجل مستمع لتغيير اللغة في Filament (حفظ الجلسة)

#### `FilamentLocaleServiceProvider.php`
- يهيئ مبدل اللغة في Filament:
  - اللغات المتاحة: `en` (English), `ar` (العربية)
  - يظهر خارج اللوحات أيضاً
  - يحدد المسارات التي يظهر فيها خارج اللوحات

#### `Filament/AdminPanelProvider.php` (لوحة الإدارة)
- **المعرف:** `admin`
- **المسار:** `/admin`
- **اللون الأساسي:** Amber (ذهبي)
- **يدعم الوضع الداكن**
- يكتشف تلقائياً:
  - الموارد من `app/Filament/Admin/Resources/`
  - الصفحات من `app/Filament/Admin/Pages/`
  - الويدجات من `app/Filament/Admin/Widgets/`
- الوسطاء: تشفير الكوكيز، الجلسات، CSRF، ربط المسارات

#### `Filament/PolicePanelProvider.php` (لوحة الشرطة)
- **المعرف:** `police`
- **المسار:** `/police`
- **اللون الأساسي:** Blue (أزرق)
- نفس هيكل AdminPanelProvider لكن للموارد الشرطية

---

### 5.10 المجلد `Filament/` (لوحات تحكم Filament)

#### 5.10.1 لوحة الإدارة (`Filament/Admin/`)

##### الموارد (Resources):

**1. `UserResource.php`** — إدارة المستخدمين
- عرض جدول: اسم المستخدم، البريد، الدور (شارة)، الحالة (أيقونة)، تاريخ الإنشاء
- نموذج إدخال: اسم مستخدم، بريد، كلمة مرور، دور، حالة النشاط
- عمليات: إنشاء، تعديل، حذف، حذف جماعي
- **5 مديري علاقات (RelationManagers):**
  - `CitizenDataRelationManager`: عرض/تعديل بيانات المواطن (رقم هوية، اسم، هاتف، فصيلة دم)
  - `PoliceDataRelationManager`: عرض/تعديل بيانات الضابط (رقم شارة، اسم، رتبة، قسم)
  - `ReportsRelationManager`: عرض بلاغات المستخدم
  - `VehiclesRelationManager`: عرض مركبات المستخدم
  - `ViolationsRelationManager`: عرض مخالفات المستخدم (المبلغ بالـ SYP)

**2. `VehicleResource.php`** — عرض المركبات (للقراءة فقط)
- عرض جدول: رقم اللوحة، النوع، الشركة، الطراز، السنة، حالة التأمين (شارة)، اللون، المالك
- لا يمكن إنشاء مركبات من لوحة الإدارة (يتم إنشاؤها من واجهة المواطن)

**3. `ReportResource.php`** — عرض البلاغات (للقراءة فقط)
- عرض جدول: الرقم، المواطن، نوع البلاغ (شارة)، القسم المسند (شارة)، الحالة (شارة ملونة)، التاريخ
- فلاتر: الحالة، القسم، نوع البلاغ
- عرض تفصيلي يتضمن: النوع، الوصف، الموقع، الإحداثيات

**4. `TrafficViolationResource.php`** — إدارة المخالفات المرورية
- عرض جدول: الرقم، المواطن، رقم الهوية، رقم اللوحة، الضابط، نوع المخالفة (شارة)، مبلغ الغرامة (SYP)، الحالة (شارة ملونة)، تاريخ الإصدار، تاريخ الاستحقاق
- يمكن تعديل حالة المخالفة فقط
- فلاتر: الحالة، فترة الإصدار (من-إلى)

**5. `ActivityLogResource.php`** — سجل النشاط (للقراءة فقط)
- عرض جدول: اسم المدير، نوع الإجراء (شارة ملونة: create=أخضر, update=أصفر, delete=أحمر)، الجدول المستهدف، الوصف (50 حرف)، التاريخ
- فلاتر: نوع الإجراء، الجدول المستهدف
- ترتيب افتراضي: الأحدث أولاً

##### الويدجات (Widgets):

**1. `StatsOverview.php`** — بطاقات إحصائية
- إجمالي المستخدمين
- إجمالي البلاغات
- البلاغات غير المحلولة
- إجمالي الغرامات غير المدفوعة (ل.س)
- مخالفات هذا الأسبوع

**2. `ReportsByDepartmentChart.php`** — رسم بياني دائري
- يعرض توزيع البلاغات حسب الأقسام (دورية الطرق، شرطة المرور، الشرطة المحلية)
- ألوان مختلفة لكل قسم

---

#### 5.10.2 لوحة الشرطة (`Filament/Police/`)

##### الموارد (Resources):

**1. `AssignedReportResource.php`** — البلاغات المسندة
- **تصفية تلقائية:** يعرض فقط بلاغات القسم الذي ينتمي إليه الضابط (عبر `modifyQueryUsing`)
- عرض جدول: الرقم، صورة البلاغ، المواطن، نوع البلاغ، الحالة (شارة ملونة)، التاريخ
- يدعم عرض الصور والفيديو المرفق في صفحة التفاصيل
- يمكن تعديل حالة البلاغ فقط
- تحديث تلقائي كل 10 ثوانٍ (`poll('10s')`)
- **مدير علاقات:** `VehicleRelationManager` لعرض تفاصيل المركبة المرتبطة

**صفحة `ViewAssignedReport.php`:**
- تحتوي على زر **"إصدار مخالفة"** يفتح نموذج (Modal) لإصدار مخالفة مرورية من البلاغ:
  - نوع المخالفة (6 أنواع)
  - الوصف
  - مبلغ الغرامة (SYP)
  - تاريخ الاستحقاق
  - يستدعي `ViolationService::issueFromReport()`

**2. `TrafficViolationResource.php`** — المخالفات المرورية
- **تصفية تلقائية:** يعرض فقط المخالفات التي أصدرها الضابط الحالي
- نموذج إصدار مخالفة:
  - اختيار المواطن (قائمة منسدلة مع بحث)
  - اختيار المركبة (يتغير ديناميكياً حسب المواطن المختار عبر `live()->reactive()`)
  - نوع المخالفة، الوصف، مبلغ الغرامة (SYP)، تاريخ الاستحقاق
- عرض جدول: الرقم، المواطن، رقم اللوحة، نوع المخالفة، المبلغ (SYP)، الحالة، تاريخ الإصدار

##### الويدجات (Widgets):

**1. `PoliceStatsOverview.php`** — إحصائيات الضابط
- إجمالي بلاغات القسم
- بلاغات قيد الانتظار (مع لون تحذيري إذا > 0)
- المخالفات التي أصدرها الضابط

**2. `ReportsChart.php`** — رسم بياني دائري لحالة البلاغات في القسم
- ألوان مختلفة لكل حالة (جديد=أصفر, قيد المعالجة=أزرق, محلول=أخضر, مرفوض=أحمر)

**3. `LatestAssignedReports.php`** — جدول آخر 5 بلاغات محولة للقسم
- يعرض رقم البلاغ (بتنسيق RPT-XXXXXX)، النوع، الحالة، التاريخ
- زر "عرض التفاصيل" للانتقال لصفحة البلاغ

---

## 6. ملف المسارات `routes/web.php`

### هيكل المسارات:

**مجموعة اللغة:**
- جميع المسارات ملفوفة في مجموعة `LaravelLocalization` لدعم تعدد اللغات

**المسارات العامة:**
- `GET /` → صفحة الترحيب

**مسارات المصادقة (guest فقط):**
- `GET /login` → نموذج تسجيل الدخول
- `POST /login` → عملية تسجيل الدخول
- `GET /register` → نموذج التسجيل
- `POST /register` → عملية التسجيل

**مسارج تسجيل الخروج:**
- `POST /logout` → تسجيل الخروج

**مسارات المواطن (auth فقط، prefix: `citizen`):**
- `GET /citizen/dashboard` → لوحة التحكم
- `GET /citizen/vehicles` → قائمة المركبات
- `GET /citizen/vehicles/{vehicle}` → تفاصيل مركبة
- `POST /citizen/vehicles` → إضافة مركبة
- `PUT /citizen/vehicles/{vehicle}` → تحديث مركبة
- `DELETE /citizen/vehicles/{vehicle}` → حذف مركبة
- `GET /citizen/reports` → قائمة البلاغات
- `GET /citizen/reports/create` → نموذج البلاغ
- `POST /citizen/reports` → إرسال بلاغ **(محدود بـ 3 بلاغات/دقيقة عبر throttle:reports)**
- `GET /citizen/reports/search-vehicles` → بحث AJAX عن مركبات
- `GET /citizen/reports/{report}` → تفاصيل بلاغ
- `GET /citizen/violations` → قائمة المخالفات
- `GET /citizen/violations/{violation}` → تفاصيل مخالفة
- `POST /citizen/violations/{violation}/pay` → دفع تجريبي
- `GET /citizen/profile` → الملف الشخصي
- `PUT /citizen/profile/info` → تحديث المعلومات
- `PUT /citizen/profile/password` → تغيير كلمة المرور

---

## 7. ملفات قاعدة البيانات

### التهجيرات (Migrations) - 15 ملف:

| الرقم | الملف | الوصف |
|-------|-------|-------|
| 1 | `create_users_table` | جدول المستخدمين (username, email, password, role_id, is_active) |
| 2 | `create_cache_table` | جدول التخزين المؤقت |
| 3 | `create_jobs_table` | جدول الطوابير |
| 4 | `create_roles_table` | جدول الأدوار (name, slug) |
| 5 | `create_citizens_data_table` | بيانات المواطنين (user_id, national_id, full_name, phone, blood_type) |
| 6 | `create_police_data_table` | بيانات الضباط (user_id, badge_number, full_name, rank, department) |
| 7 | `create_admins_data_table` | بيانات المديرين (user_id, full_name) |
| 8 | `create_vehicles_table` | المركبات (citizen_id, plate_number, vehicle_type, make, model_name, model_year, chassis_number, engine_number, color, registration_expiry, insurance_status) |
| 9 | `create_reports_table` | البلاغات (citizen_id, vehicle_id, assigned_department, report_type, description, latitude, longitude, location_text, image_url, video_url, status) |
| 10 | `create_traffic_violations_table` | المخالفات (citizen_id, vehicle_id, police_id, report_id, violation_type, description, fine_amount (decimal:8,2), status, issued_at, due_date) |
| 11 | `add_role_foreign_to_users_table` | إضافة مفتاح أجنبي role_id → roles |
| 12 | `create_activity_logs_table` | سجل النشاط (admin_id, action_type, target_table, description) |
| 13 | `add_video_url_to_reports_table` | إضافة حقل video_url للبلاغات |
| 14 | `add_reported_plate_to_reports_table` | إضافة حقل reported_vehicle_plate |
| 15 | `add_new_fields_to_vehicles_table` | إضافة حقول جديدة للمركبات |

### السيدرات (Seeders) - 9 ملفات:

| الملف | الوصف |
|-------|-------|
| `DatabaseSeeder` | الموزع الرئيسي - يربط جميع السيدرات ببعضها |
| `RoleSeeder` | ينشئ 3 أدوار: مواطن، إدارة، شرطة |
| `AdminDataSeeder` | ينشئ حساب مدير النظام (مدير النظام العام) |
| `PoliceDataSeeder` | ينشئ 3 ضباط بأقسام مختلفة (رائد طارق الزعبي، نقيب لينا الشريف، ملازم أول زيد الرميحي) |
| `CitizenDataSeeder` | ينشئ 5 مواطنين سوريين بأسماء عربية وأرقام هواتف +963 |
| `VehicleSeeder` | ينشئ 11 مركبة بلوحات سورية (دمشق، حلب، حمص، طرطوس، اللاذقية، حماة، درعا، السويداء) |
| `ReportSeeder` | ينشئ 50 بلاغاً بمواقع وإحداثيات سورية (دمشق، حلب، حمص، حماة، اللاذقية، طرطوس) |
| `TrafficViolationSeeder` | ينشئ 22 مخالفة بمبالغ بالليرة السورية (10,000 - 150,000 ل.س) |
| `ActivityLogSeeder` | ينشئ 15 سجل نشاط بالعربية |

---

## 8. ملفات الترجمة (Localization)

### `lang/ar/messages.php` — 129 سطر
ترجمات عربية لجميع النصوص المستخدمة في واجهة المواطن:
- أزرار وتسميات النماذج
- أنواع البلاغات (حادث، خطر، ازدحام، تهديد أمني)
- أنواع المخالفات (تجاوز سرعة، قيادة متهورة، إشارة حمراء، وقوف غير نظامي، حزام الأمان، استخدام الهاتف)
- حالات البلاغ والمخالفة
- أقسام الشرطة
- رسائل النجاح والخطأ

### `lang/ar/filament.php` — 137 سطر
ترجمات عربية للوحات تحكم Filament:
- `direction: 'rtl'` — اتجاه النص من اليمين لليسار
- قوائم التنقل
- أسماء الموارد (مفرد وجمع)
- عناوين الأقسام
- أعمدة الجداول
- أزرار الإجراءات
- الفلاتر
- الويدجات
- الترقيم
- مديري العلاقات
- ترجمات الـ Enums

### `lang/en/messages.php` و `lang/en/filament.php`
النسخ الإنجليزية المقابلة لجميع الترجمات.

---

## 9. ملخص تدفق العمل (Workflow Summary)

### 9.1 تسجيل الدخول
1. يصل المستخدم إلى `/login`
2. يدخل البريد وكلمة المرور
3. `LoginController::login()` يتحقق من البيانات
4. يوجه المستخدم حسب دوره:
   - **مدير** → `/admin` (لوحة Filament الإدارية)
   - **شرطة** → `/police` (لوحة Filament الشرطية)
   - **مواطن** → `/citizen/dashboard` (واجهة المواطن)

### 9.2 رفع بلاغ (كمواطن)
1. يدخل المواطن إلى `/citizen/reports/create` (نموذج خطوات)
2. يختار نوع البلاغ والموقع (يُحدد تلقائياً عبر GPS)
3. يختار مركبته أو يبحث عن مركبة أخرى
4. يرفع صورة/فيديو ويكتب وصفاً
5. `ReportController::store()` يرسل البيانات إلى `ReportRoutingService`
6. الخدمة تحدد القسم المناسب وتنشئ البلاغ
7. يُطلق حدث `ReportCreated` لتوثيق العملية في سجل النشاط
8. يُعرض رقم تتبع للمواطن

### 9.3 توجيه البلاغ (Routing Logic)
```
security_threat  → LocalPolice (الشرطة المحلية)
traffic_jam      → TrafficPolice (شرطة المرور)
accident+highway → HighwayPatrol (دورية الطرق)
accident+city    → TrafficPolice (شرطة المرور)
hazard+highway   → HighwayPatrol (دورية الطرق)
hazard+city      → TrafficPolice (شرطة المرور)
```

### 9.4 معالجة البلاغ (كشرطي)
1. يدخل الشرطي إلى `/police`
2. يرى إحصائيات قسمه وآخر البلاغات المحولة
3. يفتح بلاغاً ويرى التفاصيل والصور/الفيديو
4. يغير حالة البلاغ (جديد → قيد المعالجة → محلول)
5. يمكنه إصدار مخالفة مرورية مباشرة من البلاغ

### 9.5 الرقابة والإحصائيات (كمدير)
1. يدخل المدير إلى `/admin`
2. يرى بطاقات إحصائية (مستخدمين، بلاغات، غرامات، مخالفات الأسبوع)
3. يرى رسماً بيانياً لتوزيع البلاغات حسب الأقسام
4. يدير المستخدمين (إنشاء، تعديل، حذف)
5. يراقب البلاغات والمخالفات وسجلات النشاط
6. يعدل حالة المخالفات (غير مدفوعة → مدفوعة/ملغاة)

---

## 10. هيكل قاعدة البيانات (ERD)

```
roles (1) ──── (N) users
                    │
         ┌──────────┼──────────┐
         │          │          │
    citizens_data  police_data  admins_data
         │          │
    (1)──┘    ──┘(1)
     │              │
     N              N
  vehicles    traffic_violations
     │              │
     N              │
  reports ──────────┘
     │
     N
  (violations from reports)

activity_logs ← admins_data
```

---

## 11. المميزات التقنية البارزة

1. **توجيه تلقائي ذكي:** البلاغات تُوجه تلقائياً للقسم المناسب حسب النوع والموقع
2. **نطاقات الاستعلام (Scopes):** تصفية تلقائية لبيانات الضابط حسب قسمه
3. **نظام الأحداث (Events/Listeners):** فك الارتباط بين المكونات
4. **سياسات الصلاحيات (Policies):** حماية الموارد على مستوى التطبيق
5. **معاملات قاعدة البيانات (DB Transactions):** ضمان تكامل البيانات في العمليات الحرجة
6. **Rate Limiting:** تحديد عدد البلاغات بـ 3 في الدقيقة
7. **دعم الوسائط:** رفع صور وفيديو مع البلاغات
8. **تحديث تلقائي:** لوحة الشرطة تتحدث كل 10 ثوانٍ
9. **اختيار ديناميكي:** مركبات المواطن تتغير ديناميكياً عند اختيار المواطن في نموذج المخالفة
10. **دعم تعدد اللغات:** عربي/إنجليزي مع RTL
11. **العملة السورية:** جميع المبالغ بالليرة السورية (SYP / ل.س)
12. **بيانات تجريبية سورية:** أسماء عربية، أرقام هواتف +963، لوحات سورية، مواقع وإحداثيات سورية
