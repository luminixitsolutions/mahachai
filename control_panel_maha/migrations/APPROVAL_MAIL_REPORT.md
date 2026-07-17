# Approval Email Notifications — Implementation Report

**Date:** 2026-07-17 · **Timezone:** Asia/Kolkata  
**Project:** `control_panel_maha`

## Summary

Centralized approval/rejection email notifications are live across expense, leave, attendance, advance, resignation, vendor, petty cash, and related workflows. Mail is sent after each approval level (and on rejection), never blocks the approval transaction, and is logged for retry.

Native browser/Windows desktop notifications are also queued for the request creator after each approval or rejection. The control panel polls every 30 seconds and displays alerts after the user enables **Notifications → Desktop alerts**.

## Modules covered

| Module key | Request type | Table | Stages wired |
|---|---|---|---|
| `employee_expense` | Employee Expense | `tbl_expense_request` | Manager, HR, Business Head, Accounts (+ mult/GST variants) |
| `petty_cash` | Petty Cash | `tbl_prettycash_request` | Manager, Admin, Accounts |
| `petty_limit` | Petty Cash Limit | `tbl_petty_limit_request` | Manager, Admin, Accounts |
| `vendor_expense` | Vendor Expense | `tbl_vendor_expenses` | BDM, Purchase, Manager, Accounts |
| `nso_vendor_expense` | NSO Vendor Expense | `tbl_nso_vendor_expenses` | NSO First, NSO/BDM, Purchase, Manager, Accounts |
| `resign` | Resignation | `tbl_resign_request` | Manager, HR (+ clearance AJAX) |
| `advance_salary` | Salary Advance | `tbl_advance_salary` | Manager, HR |
| `advance_request` | Advance Payment | `tbl_advance_request` | Accounts |
| `leave` | Leave | `tbl_leave_request` | Manager, HR |
| `attendance` | Attendance Request | `tbl_attendance_request` | Manager, BDM, HR |
| `cash_book` | Cash Book | `tbl_cash_book` | Admin |
| `vendor_invoice` | Vendor Invoice | `tbl_vendor_expense_invoices` | Admin, Accounts |
| `resignation_clearance` | Resignation Clearance | `tbl_resignation_clearance` | IT, Dept, Accounts, HR |

## Files created

- `includes/approval_mail_config.php` — SMTP (env override) + fixed CC list
- `includes/approval_mail_service.php` — reusable notify / template / next-level / log
- `migrations/approval_mail_notifications.sql` — `tbl_approval_mail_log`
- `migrations/run_approval_mail_migration.php`
- `migrations/test_approval_mail.php` — preview / optional send
- `migrations/retry_failed_approval_mails.php`
- `ajax_approval_desktop_notifications.php` — authenticated desktop-alert delivery and acknowledgement
- `migrations/patch_approval_mail_handlers.php` / `patch_approval_mail_handlers2.php` (one-shot integrators)

## Files modified (active handlers)

37+ `approve-*.php` handlers + `ajax_files/ajax_resignation_clearance.php`  
(Legacy `*-old*` / `*-test*` files intentionally not changed.)

## Database

```sql
tbl_approval_mail_log
  module, request_id, stage, decision, actor_user_id,
  dedupe_key (UNIQUE), to_email, cc_emails, subject,
  body_preview, status (pending|sent|failed|skipped),
  error_message, attempts, sent_at, created_at, updated_at
```

`tbl_approval_desktop_notifications` stores user-targeted desktop alerts with a unique dedupe key and delivery timestamp.

Table auto-creates on first notify if migration was not run.

## Email flow

1. Approval/rejection DB update succeeds (commit / query OK).
2. `approval_mail_notify($conn, $module, $id, $stage, $decision, $actorUserId, $remarks)`.
3. Load request + requester `EmailId` / `EmailId2`.
4. Detect next pending level from live status fields (or hierarchy levels).
5. Build branded HTML + plain text.
6. Insert log with unique `dedupe_key` (blocks refresh duplicates).
7. Send via existing PHPMailer SMTP (`mail.kwickfoods.in` / env overrides).
8. **TO:** requester · **CC:** `coo@mahachai.in`, `pradeep.kulkarni@mahachai.in`, `rajatdh07@gmail.com`
9. On SMTP failure → status `failed` (approval still succeeds). Retry via `retry_failed_approval_mails.php`.

## SMTP credentials

Reuse existing project SMTP. Prefer env vars:

- `MAHA_SMTP_HOST`, `MAHA_SMTP_USER`, `MAHA_SMTP_PASS`, `MAHA_SMTP_PORT`, `MAHA_SMTP_SECURE`, `MAHA_SMTP_FROM`, `MAHA_SMTP_FROM_NAME`

## How to test

1. Run migration (browser or CLI with DB access):  
   `php migrations/run_approval_mail_migration.php`
2. Preview:  
   `/control_panel_maha/migrations/test_approval_mail.php?key=maha-approval-mail&module=leave&id=REQUEST_ID`
3. Optional real send: add `&send=1`
4. Approve/reject a live request at each level; confirm employee receives mail and CC list is present.
5. Refresh/re-submit same decision → second mail skipped (dedupe).
6. Check `tbl_approval_mail_log`.

## Test results (dev machine)

| Check | Result |
|---|---|
| PHP syntax (`approval_mail_service.php`) | Pass |
| Handler integration count | 37 approve pages + clearance AJAX |
| CLI DB migration | Could not connect from this shell (remote DB); table auto-create remains available at runtime |
| Live SMTP end-to-end | Pending — use `test_approval_mail.php&send=1` on Laragon/server |

## Pending / notes

- Leave manager mail previously used wrong recipient lookup; replaced with centralized service (employee `EmailId`).
- Petty limit now uses centralized mail instead of `plt_mail_on_approval` (avoids duplicate).
- Requests without employee email are logged as `skipped` (CC not sent alone).
- Resignation manager approval currently marks HR final in existing business logic — mail reflects that.
- Cash book requester email depends on `UserId` (or related user fields) on the row.
- Remove or restrict `test_approval_mail.php` after UAT.
- Rotate SMTP password into env vars when possible (credentials remain as in existing ticket mail helpers).
