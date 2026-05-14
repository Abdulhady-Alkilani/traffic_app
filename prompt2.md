# 🤖 System Prompt Extension: Traffic Violations Module
**Context:** This is an extension to the "Traffic Reports & Road Safety System" currently being developed.
**Task:** Implement the "Traffic Violations" (المخالفات المرورية) module. This module allows Police Officers to issue fines to Citizens/Vehicles, either independently or linked to a specific Report.

---

## 🗄️ 1. Database Schema Update (DBML)
*Generate Migrations and Models for the following new table and relations. Ensure it follows the existing `snake_case` naming convention.*

```dbml
Table traffic_violations {
  id integer [primary key, increment]
  citizen_id integer [not null, note: 'The citizen receiving the fine']
  vehicle_id integer [note: 'Nullable, if the violation is tied to a specific vehicle']
  police_id integer [not null, note: 'The officer who issued the fine']
  report_id integer [note: 'Nullable, if the violation was a result of investigating a report']
  violation_type varchar [not null, note: 'e.g., Speeding, Reckless Driving, Red Light']
  description text [note: 'Officer notes']
  fine_amount decimal(8, 2) [not null]
  status enum('unpaid', 'paid', 'canceled') [default: 'unpaid']
  issued_at timestamp [default: `now()`]
  due_date date [not null]
  created_at timestamp
  updated_at timestamp
}

// New Relationships
Ref: citizens_data.id < traffic_violations.citizen_id
Ref: vehicles.id < traffic_violations.vehicle_id
Ref: police_data.id < traffic_violations.police_id
Ref: reports.id < traffic_violations.report_id
```

---

## ⚙️ 2. Backend Execution (Filament Panels)

### A. Police Operations Panel (`/police`) updates:
1.  **New Resource:** `TrafficViolationResource`.
    *   **Logic:** Officers can only see violations *they* issued (Use Eloquent Global Scope filtering by `auth()->user()->police_data->id`).
    *   **Create Form:** `citizen_id` (Searchable Select), `vehicle_id` (Dependent on selected citizen), `violation_type`, `fine_amount`, `due_date`.
    *   **List Table:** Show Violations with Status badges (`unpaid` = danger, `paid` = success).
2.  **Action Integration (Crucial UX):**
    *   Inside the existing `AssignedReportResource` (View page), add a custom **Filament Action** at the top named "Issue Violation" (إصدار مخالفة).
    *   When clicked, it opens a modal form to create a violation. It MUST auto-fill the `report_id`, `citizen_id`, and `vehicle_id` based on the current report's data.

### B. Super Admin Panel (`/admin`) updates:
1.  **Resource Update:** Add `TrafficViolationResource` to the Admin Panel. Admins can view ALL violations across the system, search by Citizen ID, filter by Status, and see which officer issued it. Admins can change status to 'canceled' if a mistake was made.
2.  **Relation Manager:** Add a `ViolationsRelationManager` inside the `UserResource` (Citizen view) so the Admin can see all fines a specific citizen has.
3.  **Dashboard Widgets:**
    *   Add a Stat Widget: "Total Unpaid Fines Amount ($/SAR)".
    *   Add a Stat Widget: "Violations Issued This Week".

---

## 🌐 3. Frontend Execution (Citizen Portal)

1.  **New Tab / Page:** Add a new section in the Citizen Dashboard called **"My Violations" (مخالفاتي)**.
2.  **UI Implementation (Tailwind + Alpine):**
    *   Create a clean, paginated data table showing the user's violations.
    *   **Columns:** ID, Date, Type, Amount, Linked Vehicle (Plate Number), and Status.
    *   **Badges:** Use clear visual cues (Red for Unpaid, Green for Paid).
    *   **Action:** Add a placeholder "Pay Now" (دفع الآن) button next to 'unpaid' violations. *(Note: Do not integrate an actual payment gateway, just mock a Javascript alert or a route that changes the status to 'paid' for testing purposes).*

---

## 🛡️ 4. Strict Requirements
1.  **Validation:** Ensure `fine_amount` is always a positive number. `due_date` must be a future date from `issued_at`.
2.  **i18n:** All new strings (Violation Type, Fine Amount, Unpaid, Paid) MUST be localized in `lang/en` and `lang/ar`.
3.  **Seeding:** Update the `DatabaseSeeder` to generate at least 20 random `traffic_violations` linked to the seeded citizens, vehicles, and police officers.

**Agent Execution Command:**
*Please acknowledge this module extension. Generate the Migration, Model, Filament Resources (for both Admin and Police), and the Blade views for the Citizen portal step-by-step.*
