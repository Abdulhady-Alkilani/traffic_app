# 🤖 System Prompt for AI Coding Agent
**Project Name:** Traffic Reports & Road Safety System
**Tech Stack:** Laravel 12, PHP 8.2+, MySQL, FilamentPHP v3, Tailwind CSS, Alpine.js, HTML5.
**Task:** Build a complete, scalable, and secure web application from A to Z based on the following comprehensive specifications.

---

## 🏗️ 1. Global System Rules (Strictly Follow)
1.  **Localization (i18n):** The system MUST fully support English (LTR) and Arabic (RTL). Do NOT hardcode any strings in Blade or Filament. Always use `__('messages.key')` and create `lang/en` and `lang/ar` files. Handle RTL rendering using Tailwind's `rtl:` and `ltr:` variants or HTML `dir` attribute.
2.  **Theming:** Full support for Light and Dark modes. Use Tailwind's `dark:` classes for all frontend UI. Filament natively supports this, ensure it's enabled.
3.  **Data Tables:** ALL tables (Filament and Frontend) MUST include: Global Search, Column Sorting, Filters (Date, Status, Type), and Pagination.
4.  **Coding Standards:** Use modern PHP 8 features, Laravel 12 syntax, strict typing, Action classes for business logic, and Form Requests for validation.

---

## 🗄️ 2. Database Architecture (DBML)
*Generate Laravel Migrations, Models, and Relationships exactly matching this DBML. Use `snake_case` for everything.*

```dbml
Table roles {
  id integer [primary key, increment]
  name varchar [not null]
  slug varchar [unique, not null]
  created_at timestamp
  updated_at timestamp
}

Table users {
  id integer [primary key, increment]
  role_id integer [not null]
  username varchar [unique, not null]
  email varchar [unique, not null]
  password varchar [not null]
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table citizens_data {
  id integer [primary key, increment]
  user_id integer [unique, not null] 
  national_id varchar [unique, not null]
  full_name varchar [not null]
  phone varchar [unique, not null]
  blood_type varchar
}

Table police_data {
  id integer [primary key, increment]
  user_id integer [unique, not null] 
  badge_number varchar [unique, not null]
  full_name varchar [not null]
  rank varchar
  department enum('highway_patrol', 'traffic_police', 'local_police') [not null] 
}

Table admins_data {
  id integer [primary key, increment]
  user_id integer [unique, not null] 
  full_name varchar [not null]
}

Table vehicles {
  id integer [primary key, increment]
  citizen_id integer [not null] 
  plate_number varchar [unique, not null] 
  vehicle_type varchar [not null]
  make varchar [not null] 
  model_year year [not null]
  color varchar [not null]
  created_at timestamp
  updated_at timestamp
}

Table reports {
  id integer [primary key, increment]
  citizen_id integer [not null] 
  vehicle_id integer 
  assigned_department enum('highway_patrol', 'traffic_police', 'local_police') 
  report_type varchar [not null]
  description text
  latitude decimal(10, 8) [not null]
  longitude decimal(11, 8) [not null]
  location_text varchar 
  image_url varchar
  status enum('new', 'in_progress', 'resolved', 'rejected') [default: 'new']
  created_at timestamp
  updated_at timestamp
}

Table activity_logs {
  id integer [primary key, increment]
  admin_id integer [not null] 
  action_type varchar [not null] 
  target_table varchar [not null] 
  description text
  created_at timestamp
}

Ref: roles.id < users.role_id
Ref: users.id - citizens_data.user_id
Ref: users.id - police_data.user_id
Ref: users.id - admins_data.user_id
Ref: citizens_data.id < vehicles.citizen_id
Ref: citizens_data.id < reports.citizen_id
Ref: vehicles.id < reports.vehicle_id
Ref: admins_data.id < activity_logs.admin_id
```

---

## 🛠️ 3. Execution Plan (Step-by-Step for AI)

### Phase 1: Setup & Core Packages
1. Install Laravel 12.
2. Install & configure `filament/filament` v3.3 -w.
3. Install `spatie/laravel-permission` (Link it with the `roles` table).
4. Install `mcamara/laravel-localization` (For URL-based AR/EN routing).

### Phase 2: Database, Models & Seeders
1. Create Migrations based on the DBML. Apply Indexes to `latitude`, `longitude`, and `status`.
2. Create Models with strict `$fillable`, Casts (e.g., `status` to Enum), and implement Eloquent Relationships.
3. **Seeders:** Create an intelligent `DatabaseSeeder`.
   - Seed 3 fixed Roles (Citizen, Admin, Police).
   - Seed 1 Super Admin, 3 Police Officers (one for each department), and 5 Citizens.
   - Seed 10 Vehicles.
   - Seed 50 realistic Reports with fake GPS coordinates, linking them randomly to citizens.

### Phase 3: Filament Panels Architecture (Backend)
Configure 2 separate Filament Panels using `php artisan filament:panel`.

**Panel 1: Super Admin (`/admin`)**
*   **Access:** Only `role_id` matching 'admin'.
*   **Resources:**
    *   `UserResource`: Manage all users. Include a relation manager for Profiles (Citizen/Police data).
    *   `VehicleResource`: View all vehicles (Search by plate_number).
    *   `ReportResource`: Read-only view of all reports.
    *   `ActivityLogResource`: Read-only audit trail.
*   **Widgets:**
    *   `StatsOverview`: Total Users, Total Reports, Unresolved Reports.
    *   `ReportsChart`: Line chart showing reports created per month.

**Panel 2: Police Operations (`/police`)**
*   **Access:** Only `role_id` matching 'police'.
*   **Logic (Crucial):** Apply Eloquent Global Scopes so the logged-in police officer ONLY sees reports where `reports.assigned_department` matches their `police_data.department`.
*   **Resources:**
    *   `AssignedReportResource`: 
        *   *List:* Status Badges with colors (new=danger, resolved=success).
        *   *Filters:* Filter by status, date range.
        *   *Action:* Officers can ONLY edit the `status` field.
*   **Relation Managers:** Inside `AssignedReportResource`, show `VehicleRelationManager` to display details of the car involved in the report.

### Phase 4: Frontend Development (Citizen Portal)
Build the public-facing pages using Blade, Tailwind CSS, and Alpine.js.
1. **Layout:** Responsive Navbar, Footer, Language Switcher (AR/EN toggle), Dark Mode Toggle.
2. **Auth Pages:** Login & Register (Capture Citizen_Data during registration).
3. **Citizen Dashboard:**
   - **Vehicles Tab:** CRUD operations for user's vehicles (Paginated list).
   - **My Reports Tab:** List of user's past reports with status badges (Paginated, Searchable).
4. **Smart Reporting Wizard (The Core Feature):**
   - Build a multi-step form using Alpine.js (`x-data="{ step: 1 }"`).
   - **Step 1:** Select Report Type (Accident, Hazard, Traffic Jam). Use JS `navigator.geolocation.getCurrentPosition()` to capture `latitude` and `longitude` implicitly.
   - **Step 2:** Select associated vehicle (Dropdown), upload an image, and add an optional text description.
   - **Step 3:** Review and submit.
   - *UI Requirement:* Must look like a modern mobile app flow. Max 3 clicks.

### Phase 5: Business Logic (Smart Auto-Routing)
Create a `ReportCreationService` class. When a citizen submits a report:
1. Determine `assigned_department` based on logic:
   - If `report_type` == 'accident' or 'hazard' AND distance is outside city bounds (simulate with mock logic or specific coordinate ranges) -> `highway_patrol`.
   - If `report_type` == 'traffic_jam' -> `traffic_police`.
   - If `report_type` == 'security_threat' -> `local_police`.
2. Save the report to the DB.
3. (Simulated) Dispatch a Laravel Event `ReportCreated` to log the action in `activity_logs`.

### Phase 6: Final Polish
1. Ensure all Filament pages use Arabic translations from `lang/ar/filament.php` when the app locale is Arabic.
2. Ensure proper validation messages (in both languages).
3. Test dark mode visibility on all text and backgrounds.

---
**Agent Execution Command:**
*Please start by acknowledging these requirements. Then, begin with Phase 1 and output the terminal commands and configuration files needed to bootstrap the project.*
