-- Use the database
USE HIMAROS;

-- Add procedure for adding users with unique usernames
DELIMITER $$

CREATE PROCEDURE AddUser(
    IN p_First_Name VARCHAR(50),
    IN p_Last_Name VARCHAR(50),
    IN p_PWD VARCHAR(255),
    IN p_Email VARCHAR(255),
    IN p_User_Role ENUM('admin', 'doctor', 'storekeeper', 'nurse', 'secretary'),
    IN p_Gender ENUM('Male', 'Female', 'Other')
)
BEGIN
    DECLARE baseUsername VARCHAR(50);
    DECLARE uniqueUsername VARCHAR(50);
    DECLARE usernameCount INT;

    -- Generate base username
    SET baseUsername = CONCAT(LEFT(p_First_Name, 1), p_Last_Name);

    -- Count existing usernames starting with the base
    SELECT COUNT(*) INTO usernameCount
    FROM USERS_NEW
    WHERE Username LIKE CONCAT(baseUsername, '%');

    -- Generate the unique username
    SET uniqueUsername = CONCAT(baseUsername, LPAD(usernameCount + 1, 2, '0'));

    -- Insert the new user
    INSERT INTO USERS_NEW (First_Name, Last_Name, Username, PWD, Email, User_Role, Gender)
    VALUES (p_First_Name, p_Last_Name, uniqueUsername, p_PWD, p_Email, p_User_Role, p_Gender);
END$$


-- Delete User Procedure
CREATE PROCEDURE DeleteUser(
    IN p_Username VARCHAR(50)
)
BEGIN
    DELETE FROM USERS_NEW WHERE Username = p_Username;
END$$

-- Activate/Deactivate User Procedure
CREATE PROCEDURE UpdateUserStatus(
    IN p_Username VARCHAR(50),
    IN p_NewStatus ENUM('Active', 'Inactive')
)
BEGIN
    UPDATE USERS_NEW 
    SET User_Status = p_NewStatus
    WHERE Username = p_Username;
END$$

DELIMITER ;
