-- COMPREHENSIVE SQL COMMANDS FOR FIXING ELECTION_CANDIDATES FOREIGN KEY ISSUES
-- Run these in PhpMyAdmin SQL tab

-- ======= STEP 1: EXAMINATION =======
-- Check structure of the tables
DESCRIBE users;
DESCRIBE election_candidates;

-- Check foreign key constraints
SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    CONSTRAINT_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    REFERENCED_TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'election_candidates';

-- List all election candidates
SELECT id, user_id, position_id, status FROM election_candidates;

-- List users that can be referenced (these IDs are valid for the user_id column)
SELECT id, user_id, name, email FROM users LIMIT 30;

-- ======= STEP 2: DIAGNOSIS =======
-- For each problematic candidate, check if the user_id exists in users.id
-- Replace X with actual user_id values from the election_candidates table
SELECT EXISTS(SELECT 1 FROM users WHERE id = X);

-- List all invalid candidates (where user_id doesn't exist in users.id)
SELECT c.* 
FROM election_candidates c
LEFT JOIN users u ON c.user_id = u.id
WHERE u.id IS NULL;

-- ======= STEP 3: FIX OPTIONS =======

-- OPTION 1: Update individual candidates with correct user_id values
-- Replace 1 with the candidate ID and 123 with a valid user ID from the users table
UPDATE election_candidates SET user_id = 123 WHERE id = 1;

-- OPTION 2: Batch update multiple candidates to the same user
-- This assigns all candidates with invalid user IDs to user ID 123
UPDATE election_candidates c
LEFT JOIN users u ON c.user_id = u.id
SET c.user_id = 123
WHERE u.id IS NULL;

-- OPTION 3: Delete problematic candidates
-- This removes all candidates with invalid user IDs
DELETE c FROM election_candidates c
LEFT JOIN users u ON c.user_id = u.id
WHERE u.id IS NULL;

-- OPTION 4: Temporarily disable foreign key checks (USE WITH CAUTION)
-- Only use this if you need to disable the constraints temporarily
SET FOREIGN_KEY_CHECKS = 0;
-- Make your changes here
SET FOREIGN_KEY_CHECKS = 1;

-- ======= STEP 4: VERIFICATION =======
-- Verify that all candidates now have valid user_id values
SELECT 
    c.id, 
    c.user_id, 
    c.position_id, 
    c.status, 
    u.name,
    u.email
FROM 
    election_candidates c
JOIN 
    users u ON c.user_id = u.id; 