-- Fix profile photo paths in the database
-- Run this script in phpMyAdmin on your InfinityFree hosting

-- Update paths that start with 'storage/profile-photos/'
UPDATE users 
SET profile_photo_path = CONCAT('profile-photos/', SUBSTRING_INDEX(profile_photo_path, '/', -1)) 
WHERE profile_photo_path LIKE 'storage/profile-photos/%';

-- Update paths that have public/storage/profile-photos/
UPDATE users 
SET profile_photo_path = CONCAT('profile-photos/', SUBSTRING_INDEX(profile_photo_path, '/', -1)) 
WHERE profile_photo_path LIKE '%public/storage/profile-photos/%';

-- Update paths that have public/profile-photos/
UPDATE users 
SET profile_photo_path = CONCAT('profile-photos/', SUBSTRING_INDEX(profile_photo_path, '/', -1)) 
WHERE profile_photo_path LIKE '%public/profile-photos/%';

-- Make sure all paths are in the correct format
UPDATE users 
SET profile_photo_path = CONCAT('profile-photos/', SUBSTRING_INDEX(profile_photo_path, '/', -1)) 
WHERE profile_photo_path LIKE '%profile-photos/%' 
AND profile_photo_path != CONCAT('profile-photos/', SUBSTRING_INDEX(profile_photo_path, '/', -1));

-- Show the updated records
SELECT user_id, name, profile_photo_path 
FROM users 
WHERE profile_photo_path IS NOT NULL 
AND profile_photo_path != 'kofa.png' 
ORDER BY user_id; 