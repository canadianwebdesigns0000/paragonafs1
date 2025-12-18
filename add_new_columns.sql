-- Add columns for storing T4/T4A/T Slips files and passwords
-- These columns store file URLs and JSON-encoded arrays of passwords, indexed by file position
-- 
-- NOTE: If a column already exists, you'll get an error. You can safely ignore it or 
-- run each ALTER TABLE statement individually and skip any that fail.

-- Add column for spouse T4/T4A/T Slips files (stores file URLs separated by <br>)
ALTER TABLE `tax_information` 
ADD COLUMN `spouse_t_slips` TEXT NULL DEFAULT NULL 
COMMENT 'URLs for spouse T4/T4A/T Slips files (separated by <br>)';

-- Add column for applicant T4/T4A/T Slips passwords
ALTER TABLE `tax_information` 
ADD COLUMN `t_slips_passwords` TEXT NULL DEFAULT NULL 
COMMENT 'JSON array of passwords for T4/T4A/T Slips files (indexed by file position)';

-- Add column for spouse T4/T4A/T Slips passwords
ALTER TABLE `tax_information` 
ADD COLUMN `spouse_t_slips_passwords` TEXT NULL DEFAULT NULL 
COMMENT 'JSON array of passwords for spouse T4/T4A/T Slips files (indexed by file position)';

