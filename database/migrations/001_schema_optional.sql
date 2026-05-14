-- Optional one-time updates for existing databases (run manually if upgrading).
-- Fresh installs should use database/pronetwork.sql instead.

-- Role enum and legacy recruiter rows (MySQL)
-- ALTER TABLE users MODIFY COLUMN role ENUM('Student','Professional','Company','Recruiter','Admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Professional';
-- UPDATE users SET role = 'Company' WHERE role = 'Recruiter';

-- Admin jobs table extension
-- ALTER TABLE jobs ADD COLUMN applicant_limit INT NULL DEFAULT NULL;
