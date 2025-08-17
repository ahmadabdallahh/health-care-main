-- Step 1: A more robust method to clean up duplicate cities without disabling foreign key checks.

-- Create a temporary table to identify the correct (minimum) ID for each unique city.
CREATE TEMPORARY TABLE `city_ids_to_keep` AS
SELECT MIN(id) as id, name, governorate
FROM cities
GROUP BY name, governorate;

-- Create another temporary table to map old duplicate IDs to the correct new IDs.
CREATE TEMPORARY TABLE `city_id_map` AS
SELECT c.id as old_id, k.id as new_id
FROM cities c
JOIN city_ids_to_keep k ON c.name = k.name AND c.governorate = k.governorate
WHERE c.id != k.id;

-- Update the 'hospitals' table to point to the correct city ID.
UPDATE hospitals h
JOIN city_id_map m ON h.city_id = m.old_id
SET h.city_id = m.new_id;

-- (Add more UPDATE statements here for any other tables that reference city_id)

-- Now it's safe to delete the duplicate city rows.
DELETE c FROM cities c
JOIN city_id_map m ON c.id = m.old_id;

-- Clean up the temporary tables.
DROP TEMPORARY TABLE `city_ids_to_keep`;
DROP TEMPORARY TABLE `city_id_map`;

-- Add a unique constraint to prevent future duplicates.
ALTER TABLE `cities` ADD UNIQUE `unique_city`(`name`, `governorate`);

-- Step 2: Add a unique constraint to the users' email.
-- This will prevent multiple users from registering with the same email address.
ALTER TABLE `users` ADD UNIQUE `unique_email`(`email`);

-- Step 3: Create user accounts for existing doctors who are missing one.
INSERT INTO users (username, email, full_name, password, user_type, phone)
SELECT
    SUBSTRING_INDEX(d.email, '@', 1), -- Generate a username from the email prefix
    d.email,
    d.full_name,
    'default_password_123', -- Set a temporary default password. Users should reset it.
    'doctor',
    d.phone
FROM
    doctors d
LEFT JOIN
    users u ON d.email = u.email
WHERE
    d.user_id IS NULL AND u.id IS NULL; -- Only insert if no user with that email exists

-- Step 4: Update the doctors table to link existing doctors to their new user accounts.
UPDATE doctors d
JOIN users u ON d.email = u.email
SET d.user_id = u.id
WHERE d.user_id IS NULL;

-- Step 5: Now that all doctors are linked, modify the user_id column to be NOT NULL and add a foreign key constraint.
-- This will enforce data integrity moving forward.
ALTER TABLE `doctors` MODIFY `user_id` INT(11) NOT NULL;
ALTER TABLE `doctors` ADD CONSTRAINT `fk_doctors_user_id` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

SELECT 'Database cleanup and migration complete. All doctors now have linked user accounts.' AS `status`;
