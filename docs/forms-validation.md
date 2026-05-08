# Forms Validation (HTML + JS)

All forms are validated client-side by JavaScript in:
- `linkedin-clone/assets/js/main.js`

## Validation Rules
- Required fields must not be empty.
- Email fields must match a valid email format.
- Password fields must be at least 6 characters.
- Confirm password must match password (where present).
- Phone fields must contain 10-15 digits.
- Search forms require a non-empty query (`name="q"`).

## Form Inventory
### `linkedin-clone/pages`
1. `apply-job.html` (search form)
2. `auth.html` (`form-signin`)
3. `auth.html` (`form-signup`)
4. `company.html` (search form)
5. `feed.html` (search form)
6. `jobs.html` (search form)
7. `messaging.html` (search form)
8. `network.html` (search form)
9. `notifications.html` (search form)
10. `profile.html` (search form)
11. `search.html` (search form)
12. `settings.html` (search form)

### `html_files`
13. `pronetwork_login_page.html`
14. `pronetwork_login_register.html`
15. `pronetwork_register_page.html`

## Notes
- PHP backend files were removed.
- Form submit actions now point to HTML pages only.
