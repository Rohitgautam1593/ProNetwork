# ProNetwork InfinityFree Deployment

This package is prepared for InfinityFree shared hosting.

## 1. Create the hosting account

1. Create or open an InfinityFree account.
2. Create a hosting account and choose a free subdomain or connect your own domain.
3. Open the account control panel.

## 2. Create the MySQL database

1. Go to MySQL Databases.
2. Create a database, for example `pronetwork`.
3. Copy these values:
   - MySQL host, usually like `sqlXXX.infinityfree.com`
   - Database name, usually like `if0_XXXXXXXX_pronetwork`
   - Username, usually like `if0_XXXXXXXX`
   - Database password

## 3. Configure the app

1. In the uploaded files, copy:
   `app/config/config.infinityfree.example.php`
2. Rename the copy to:
   `app/config/config.local.php`
3. Replace the placeholder database and SMTP values.

Do not upload your local `app/config/config.local.php` from XAMPP. It contains local/live credentials.

## 4. Import the database

1. Open phpMyAdmin from InfinityFree.
2. Select the new database.
3. Import:
   `database/infinityfree_schema.sql`

After import, register your real account on the website. To make it admin, open phpMyAdmin and run:

```sql
UPDATE users
SET is_admin = 1, status = 'Approved'
WHERE email = 'your-email@example.com';
```

## 5. Upload files

Upload the contents of the deployment ZIP into InfinityFree's `htdocs` folder.

Keep this structure in `htdocs`:

```text
htdocs/
  .htaccess
  index.php
  app/
  admin/
  company/
  database/
  public/
  user/
  vendor/
```

The root `.htaccess` sends requests into `public/`, so do not move only the `public` folder by itself.

## 6. Test

Open:

```text
https://your-domain.example/
https://your-domain.example/auth/login
https://your-domain.example/company/login
https://your-domain.example/admin/login
```

If CSS does not load, confirm `.htaccess` was uploaded and Apache rewrite is enabled on the account.
