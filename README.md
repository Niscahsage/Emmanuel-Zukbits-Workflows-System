# ZukBits Workflows System

The **ZukBits Workflows System** is an internal web application for managing projects, documentation, approvals, weekly schedules, weekly reports, and performance tracking within **ZukBits Online – "The Home of Innovations"**.

This system is designed to act as a **central operational hub** for:

- Project and campaign tracking  
- Technical documentation and credentials  
- Weekly planning and reporting  
- Approval workflows  
- Role-based dashboards and visibility  

---

## Core Roles

The platform is built around a small, clear set of roles:

- **Super Admin** – Full system visibility and control, manages user accounts and roles.  
- **Director** – Executive view of all projects, staff reports, and approvals.  
- **System Admin** – Handles configuration, categories, and operational setup.  
- **Developer** – Manages technical projects, documentation, and weekly work.  
- **Marketer** – Manages marketing tasks, campaigns, and weekly work.

Each role receives a tailored dashboard and access level based on responsibilities.

---

## Technology Stack

- **Backend:** PHP (custom lightweight MVC-style structure)  
- **Frontend:** PHP views with inline HTML, Bootstrap-based layout, and vanilla JavaScript  
- **Database (development):** PostgreSQL via Supabase  
- **Database (production):** PostgreSQL or MySQL on AWS (e.g., RDS)  
- **Hosting (development):** Vercel or similar PHP-capable environment  
- **Hosting (production):** AWS (EC2, Lightsail, or containerized workloads)

---

## Project Structure (High Level)

```text
workflows/
├── app/          # Core application code (MVC: controllers, models, views, services, repositories)
├── public/       # Web root with front controller (index.php)
├── database/     # SQL migrations and seed scripts
├── storage/      # Logs and uploaded files
├── tests/        # Unit, integration, and end-to-end tests
├── docs/         # Internal documentation (SRS, SDS, architecture, etc.)
├── composer.json # PHP dependency configuration (to be defined)
└── README.md     # This file

- **app/core**: Application kernel, routing, base controller, base model, database, and helper classes.  
- **app/controllers**: Controllers for each module (auth, dashboards, projects, documentation, schedules, reports, approvals, notifications, settings).  
- **app/models**: Models representing database entities.  
- **app/services**: Business logic independent from HTTP and views.  
- **app/repositories**: Database access abstraction for each model.  
- **app/views**: Layouts, partials, and views for each role and module.

---

## Modules

### Authentication & User Management
- Login, logout, and basic account management.

### Role-Based Dashboards
- Dashboards for Super Admin, Director, System Admin, Developer, and Marketer.

### Project Management
- Create, track, and complete projects or campaigns.  
- Assign developers and marketers.  
- Progress logs and archival.

### Documentation & Credentials
- Project documentation and knowledge hub.  
- Encrypted storage of credentials and integration keys.

### Weekly Schedules & Targets
- Weekly planning of tasks and goals for each staff member.

### Weekly Reports & Performance
- Weekly reporting of achievements, non-completed work, and challenges.

### Approvals
- Approval requests for project completion and key decisions.

### Notifications
- In-system notifications related to assignments, approvals, and reminders.

### Settings
- System-level configuration and account preferences.

---

## Development Flow

1. **Set up the project structure**  
   Use the provided Windows CMD script to create folders and files.

2. **Configure the environment**  
   Fill in `app/config/config.php` and `app/config/env.php` with local database and environment details.

3. **Define the database schema**  
   Implement SQL in `database/migrations` based on the planned schema.

4. **Implement core components**  
   - Routing and front controller  
   - Authentication and RBAC  
   - Dashboards and project flows  

5. **Iterate module by module**  
   Start with Authentication and Dashboard.  
   Then move to Projects, Documentation, Schedules, Reports, Approvals, and Notifications.

---

## Status

This repository currently contains:

- A complete folder and file skeleton with comments describing each file’s purpose.  
- Placeholder documentation files in the `docs/` directory.  

Actual implementation of routing, controllers, models, migrations, and views will be done iteratively following the master specification and design.

---

## License

This project is intended for internal use within **ZukBits Online** and is not licensed for external distribution unless explicitly specified by the organization.


