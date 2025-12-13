# Web Development Project Report  
### Instructor: Dr. Hasan Alhuthali  
### Course: CS3810T – Web Development  
### Project: Saudi Culture Annotation Platform  
### Submission: _(convert this Markdown to PDF; target length 3–6 pages excluding screenshots)_  
### Team Members: _(list names & emails)_  

---

## Section 1: Project Specification

### 1.1 Summary of the Website  
The Saudi Culture Annotation Platform (منصة إثراء الثقافة السعودية) is an Arabic, RTL-first web application that lets registered contributors document and review cultural knowledge about Saudi Arabia. Users browse cultural projects, start an annotation session, answer structured questions (MCQ, True/False, open, list), and track progress. Managers curate projects, author questions with media, review answers, manage users, and handle incoming contact messages. The platform uses a lightweight MVC structure in pure PHP with a MySQL backend and TailwindCSS styling.

### 1.2 Target Audience  
- **Contributors (general users):** Arabic-speaking volunteers and students who provide answers about regional culture.  
- **Content managers/reviewers:** Faculty or project leads who create projects, seed questions, review submissions, and manage data quality.  
- **Visitors:** Curious readers who land on the marketing pages (home/about/contact) and may later register.

### 1.3 Site Organization  
- **Public pages:** `home`, `about`, `contact`, `login`, `register`, `password.forgot`, `password.reset`.  
- **User pages (after login):** `dashboard`, `projects` (list), `projects.show` (details, start session, comments, stats), `questions` (answer flow), `profile` (view/edit, password update).  
- **Manager pages:** `manager.dashboard`, `manager.projects` (list/create/edit/show/history), `manager.questions` (per project), `manager.reviews` (review answers), `manager.users` (list/show/update roles/delete), `manager.messages` (contact inbox + reply), `manager.settings` (theme).  
- **Navigation & flow:** All routes are registered in `app/routes/web.php`; a sticky top nav (in `app/Views/layouts/app.php`) switches links based on auth/role. Auth middleware guards user and manager areas.  
- **Artifacts:** Images in `public/assets/images`; uploaded project/question media stored under `public/uploads`; Tailwind-generated CSS at `public/assets/css/styles.css`; theme toggle script `public/assets/js/theme.js`.  
- **Screenshots:** Place project screenshots under `public/assets/images/screenshots/` and reference them in the report (e.g., `public/assets/images/screenshots/home.png`).  
- **Screenshot placeholders:**  
  - _Landing page hero_ → `![Homepage Screenshot](screenshots/home.png)`  
  - _User dashboard_ → `![User Dashboard](screenshots/dashboard.png)`  
  - _Manager projects & questions_ → `![Manager Projects](screenshots/manager-projects.png)`  
  - _Answering flow_ → `![Question Interface](screenshots/questions.png)`

### 1.4 Tools & Technologies  
- **Backend:** PHP 7.4+ (custom MVC), session-based auth, password hashing.  
- **Database:** MySQL (schema + seed in `database/full_database.sql`), PDO prepared statements.  
- **Frontend:** HTML5, TailwindCSS via CDN config (`<script src="https://cdn.tailwindcss.com"></script>`), FontAwesome icons, Google Fonts (IBM Plex Sans Arabic), Leaflet assets included for map-ready pages, vanilla JS for theme toggle/mobile nav.  
- **Build/Dev:** No npm build required for Tailwind in current setup; app runs by serving `public/` through Apache/Nginx/XAMPP or PHP built-in server.  
- **Deployment target:** Localhost (XAMPP) by default; adaptable to shared hosting/VPS by serving `public/` as document root.

---

## Section 2: Website Organization & Design Decisions
- **Layout & structure:** RTL-first layout with a reusable app shell (`app/Views/layouts/app.php`), sticky glassmorphic navbar, footer with quick links, and card-based sections. Content pages extend this layout via the `view()` helper.  
- **Navigation & user flow:** Role-aware nav items; middleware-based access control; consistent flash messaging for success/error; session-aware redirects (e.g., login/register routes push authenticated users to dashboards).  
- **UI/UX patterns:** Gradient hero, statistic tiles, cards with image overlays, progress messaging, and form validation feedback. Reusable Tailwind component classes (`btn`, `card`, `input-field`, `glass`) keep styling consistent.  
- **Responsiveness:** Tailwind responsive utilities drive grids and stacks; mobile menu toggle provided; typography scales up on larger breakpoints. RTL direction declared on `<html dir="rtl">`.  
- **Accessibility considerations:** High-contrast primary palette, clear focus states via Tailwind rings, semantic headings and buttons, and text alternatives for images; further ARIA labeling can be added where needed.  
- **Design rationale:** Modern, culturally themed palette (teal/blue gradients), glassmorphism for depth, and motion (float/fade) to emphasize hero sections while keeping forms minimal and legible.

---

## Section 3: Data Integration & Functionality
- **Datasets used:**  
  - MySQL schema and seed data in `database/full_database.sql`.  
  - Regional and thematic question banks in `database/data/*.json` imported via `database/data.php` (maps project codes to categories and question types).  
- **Data processing & storage:**  
  - PDO with prepared statements (`app/Database/DB.php`).  
  - Models handle CRUD and aggregation: `Project`, `Question`, `QuestionOption`, `Answer`, `Session`, `Review`, `ContactMessage`, `ProjectCommit`, `Setting`, `User`.  
  - Import script deduplicates projects by name, maps JSON types to enum (`mcq`, `true_false`, `open`, `list`), and records categories and options.  
- **Core functional flows:**  
  - **Authentication:** Register/login/logout with hashed passwords; role check via `App\Auth\Auth`.  
  - **Project participation:** Users start or resume sessions per project (`Session::getOrCreate`), answer questions with type-aware validation, and progress is recalculated (`Session::calculateProgress`). Comments on projects are stored in `project_comments`.  
  - **Question authoring:** Managers create questions with optional media uploads; MCQ/True-False options stored in `question_options`; project question counts increment/decrement automatically.  
  - **Review workflow:** Managers pull unreviewed answers, approve/reject with scoring (`Review::create/update`), and track per-answer review uniqueness.  
  - **Management tools:** Commit history per project, user role updates, and contact inbox with statuses and admin replies.  
- **Challenges & resolutions:**  
  - Ensuring single session per user/project → unique constraint + `getOrCreate`.  
  - Preventing duplicate answers → unique key on `(user_id, question_id)` and `Answer::hasAnswered`.  
  - Safe uploads → server-side type/size checks recommended; directories auto-created if missing.  
  - Progress tracking → derived from answered/total question counts per project.

---

## Section 4: Testing
- **Approach:** Manual functional smoke tests are expected; no automated test suite is present. Use seed accounts (`user@test.com` / `manager@test.com`, password `password123`) after loading `database/full_database.sql`.  
- **Functional testing checklist (to execute and note results):**  
  - Auth flows: register, login redirects by role, logout, password reset.  
  - User journeys: start project session, answer each question type, progress updates, add/delete project comments, view/edit profile and password change.  
  - Manager journeys: create/edit/delete project with image upload; add/edit/delete questions with media and options; review answers approve/reject; update user roles; reply to contact messages.  
  - Data integrity: unique answers enforced; session progress recalculates; commit log entries appear after project/question changes.  
- **Usability & interface testing:**  
  - Record observations for form clarity, RTL alignment, button affordances, and readability.  
  - _Notes/results placeholder:_ `...`  
- **Responsive testing on different devices:**  
  - Use devtools/device farm to verify navbar, cards, grids, and forms at common breakpoints (mobile portrait 360px, tablet 768px, desktop 1280px+).  
  - _Device matrix placeholder:_  
    - Mobile (360×720): `Result/Notes`  
    - Tablet (768×1024): `Result/Notes`  
    - Desktop (1440×900): `Result/Notes`  
- **Bugs discovered and fixed:**  
  - _Document any defects found and resolutions. Current known gaps from codebase:_ No CSRF protection; no pagination for large lists; upload validation is minimal.  

---

## Section 5: Contributions of Each Group Member
| Member | Responsibilities | Pages/Features Owned | Data/Design Tasks |
|--------|------------------|----------------------|-------------------|
| _(Name)_ | e.g., Backend, DB seeding | e.g., Auth, Sessions, Answers | e.g., Imported JSON datasets |
| _(Name)_ | e.g., Frontend & UX | e.g., Layout, Home/About/Contact | e.g., Styling, responsiveness |
| _(Name)_ | e.g., Manager tools | e.g., Project/Question CRUD, Reviews | e.g., Commit history, dashboards |

_(Ensure accuracy; this section is used for grading fairness.)_

---

## Section 6: Use of External Resources (ChatGPT, Copilot, StackOverflow, etc.)
- **AI tools used:** ChatGPT was used to analyze the repository structure and draft this report. Reviewers should verify code understanding before submission.  
- **Libraries/SDKs:** TailwindCSS (CDN/CLI), FontAwesome CDN, Google Fonts (IBM Plex Sans Arabic), Leaflet assets, PDO (built-in).  
- **Online references/templates:** _(Add links to any tutorials, snippets, or templates consulted during development.)_  
- **Verification:** All generated suggestions were cross-checked against the codebase; sensitive operations (auth, SQL) rely on prepared statements.  
- **Ethical use statement:** External assistance was used to summarize and document the project; final implementation understanding remains with the team. Disclose any additional AI or third-party code/text that influenced development.

---

## Appendix (Optional)
- **Build steps:** `npm install`; `npm run build:css`; serve `public/` (e.g., `php -S localhost:8000 -t public`).  
- **Deployment note:** Update `route()` base path in `app/helpers/helpers.php` if the public folder name or host path changes; configure MySQL credentials in `app/Database/DB.php`.  
- **Future improvements:** Add CSRF tokens, pagination and search filters, stronger upload validation, email notifications, and analytics dashboards.  
- **Extra screenshot slots:**  
  - `![Manager Dashboard](screenshots/manager-dashboard.png)`  
  - `![Contact Inbox](screenshots/messages.png)`  
  - `![Review Workflow](screenshots/review.png)`  
