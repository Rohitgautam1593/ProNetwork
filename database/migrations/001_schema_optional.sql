-- Optional one-time updates for existing databases (run manually if upgrading).
-- Fresh installs should use database/pronetwork.sql instead.

-- Role enum and legacy recruiter rows (MySQL)
-- ALTER TABLE users MODIFY COLUMN role ENUM('Student','Professional','Company','Recruiter','Admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Professional';
-- UPDATE users SET role = 'Company' WHERE role = 'Recruiter';

-- Admin jobs table extension
-- ALTER TABLE jobs ADD COLUMN applicant_limit INT NULL DEFAULT NULL;

-- Company page banners
-- ALTER TABLE companies ADD COLUMN banner_path VARCHAR(255) NULL DEFAULT NULL AFTER logo_path;
-- ALTER TABLE companies ADD COLUMN user_id INT NULL DEFAULT NULL AFTER created_at;
-- ALTER TABLE companies ADD INDEX idx_company_user (user_id);
-- CREATE TABLE company_followers (
--   id INT NOT NULL AUTO_INCREMENT,
--   company_id INT NOT NULL,
--   user_id INT NOT NULL,
--   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (id),
--   UNIQUE KEY uk_company_follower (company_id, user_id),
--   KEY idx_company_followers_user (user_id),
--   CONSTRAINT fk_company_followers_company FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE ON UPDATE CASCADE
-- );
