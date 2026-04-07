# AstroConnect Project Code Explanation

This document explains what the code does at a high level and where each feature is implemented.

## 1) Project Purpose

AstroConnect is a Laravel-based web app where:
- Users can browse astrologers, read blog content, and book appointments.
- Astrologers can apply, manage profile/appointments, and submit blog posts.
- Admins manage users, review astrologer applications, and moderate blog publication.

## 2) Main Flow by Role

### Public visitor
- Home and static pages are served from route views.
- Public blog list and blog detail only show approved + published posts.
- Public astrologer listing and profile pages are available.

### Logged-in user
- Accesses user dashboard.
- Can book appointments with astrologers.
- Can view personal appointment history.

### Astrologer
- Uses astrologer dashboard and profile pages.
- Sees only own appointments and can update appointment status.
- Creates/edits blogs, but submissions are marked pending and hidden until admin review.

### Admin
- Accesses admin dashboard and user list.
- Reviews astrologer applications (approve/reject).
- Manages blog lifecycle: create/edit, approve/reject submissions, publish/hide, delete.

## 3) Key Files and Responsibilities

### Route definitions
- routes/web.php
- Registers all public, user, astrologer, and admin route groups.
- Uses middleware to enforce role-based access.

### Blog feature controllers
- app/Http/Controllers/BlogController.php
- Public blog read-only flow for end users.

- app/Http/Controllers/AstrologerBlogController.php
- Astrologer blog authoring flow.
- Enforces ownership checks on edit/update.
- Resets edited posts back to pending review.

- app/Http/Controllers/Admin/AdminBlogController.php
- Admin blog moderation flow.
- Handles approval, rejection, visibility toggling, and deletion.

### Blog views
- resources/views/pages/user/blog.blade.php
- resources/views/pages/user/blog-show.blade.php
- Public blog list/detail pages.

- resources/views/pages/astrologer/blog.blade.php
- Single astrologer page with mode switching (list/create/edit).

- resources/views/pages/admin/blog.blade.php
- Single admin blog page with mode switching (list/create/edit).

## 4) Data and Status Rules for Blogs

Blog records are controlled by two key fields:
- review_status: pending, approved, rejected
- is_published: true/false

Visibility to public users requires both:
- review_status = approved
- is_published = true

Typical lifecycle:
1. Astrologer submits blog -> pending + hidden
2. Admin approves -> approved
3. Admin publishes -> visible to users
4. If astrologer edits again -> pending + hidden (requires re-approval)

## 5) Middleware and Access Control

- IsUser middleware protects user-only routes.
- IsAdmin middleware protects admin routes.
- EnsureUserIsAstrologer middleware protects astrologer dashboard routes.

This ensures each role can only access its own area and actions.

## 6) Appointment and Astrologer Modules

- AppointmentController handles user appointment booking/history.
- AstrologerAppointmentController handles astrologer-side appointment status updates.
- AstrologerApplicationController handles astrologer application creation/submission.
- AdminAstrologerController handles admin approval/rejection workflow for applications.

## 7) Frontend Stack

- Blade templates render server-side views.
- Tailwind and Bootstrap classes are both present in views/layouts.
- Vite is used for frontend asset build pipeline.

## 8) Notes for Future Development

- Keep role-based logic in middleware and dedicated controllers.
- Reuse mode-based Blade pages for closely related CRUD screens.
- Keep public content filters strict (approved + published) to avoid accidental exposure.
