-- Use the database
USE HIMAROS_DB;

-- Add User Procedure ---
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
    FROM USERS
    WHERE Username LIKE CONCAT(baseUsername, '%');

    -- Generate the unique username
    SET uniqueUsername = CONCAT(baseUsername, LPAD(usernameCount + 1, 2, '0'));

    -- Insert the new user with the generated username
    INSERT INTO USERS (First_Name, Last_Name, Username, PWD, Email, User_Role, Gender)
    VALUES (p_First_Name, p_Last_Name, uniqueUsername, p_PWD, p_Email, p_User_Role, p_Gender);
END$$

-- Delete User Procedure ---
DELIMITER $$
CREATE PROCEDURE DeleteUser(
    IN p_Username VARCHAR(50)
)
BEGIN
    DELETE FROM USERS WHERE Username = p_Username;
END$$

-- Activate/Deactivate User Procedure ---
DELIMITER $$
CREATE PROCEDURE UpdateUserStatus(
    IN p_Username VARCHAR(50),
    IN p_NewStatus ENUM('Active', 'Inactive')
)
BEGIN
    UPDATE USERS 
    SET User_Status = p_NewStatus
    WHERE Username = p_Username;
END$$

-- Add Stock Procedure ---
DELIMITER $$
CREATE PROCEDURE AddStock(
    IN p_ItemID INT,
    IN p_Quantity INT,
    IN p_Expiration_Date DATE,
    IN p_Barcode VARCHAR(128)
)
BEGIN
    -- Check if a matching row exists
    IF EXISTS (
        SELECT 1
        FROM CURRENT_STOCK
        WHERE ItemID = p_ItemID
          AND Expiration_Date = p_Expiration_Date
          AND Barcode = p_Barcode
    ) THEN
        -- Update the Quantity for the existing row
        UPDATE CURRENT_STOCK
        SET Quantity = Quantity + p_Quantity
        WHERE ItemID = p_ItemID
          AND Expiration_Date = p_Expiration_Date
          AND Barcode = p_Barcode;
    ELSE
        -- Insert a new row
        INSERT INTO CURRENT_STOCK (ItemID, Quantity, Expiration_Date, Barcode)
        VALUES (p_ItemID, p_Quantity, p_Expiration_Date, p_Barcode);
    END IF;
END$$

DELIMITER ;


-- Remove Stock Procedure ---
DELIMITER $$
CREATE PROCEDURE RemoveStock(
    IN p_ItemID INT,
    IN p_Quantity INT
)
BEGIN
    DECLARE remainingQuantity INT;
    
    -- Calculate current stock quantity
    SELECT SUM(Quantity) INTO remainingQuantity
    FROM CURRENT_STOCK
    WHERE ItemID = p_ItemID;
    
    IF remainingQuantity >= p_Quantity THEN
        -- Reduce quantity from CURRENT_STOCK
        WHILE p_Quantity > 0 DO
            SET @rowID = (
                SELECT Current_StockID
                FROM CURRENT_STOCK
                WHERE ItemID = p_ItemID
                ORDER BY Current_StockID ASC
                LIMIT 1
            );

            SET @rowQuantity = (
                SELECT Quantity
                FROM CURRENT_STOCK
                WHERE Current_StockID = @rowID
            );

            IF @rowQuantity <= p_Quantity THEN
                DELETE FROM CURRENT_STOCK WHERE Current_StockID = @rowID;
                SET p_Quantity = p_Quantity - @rowQuantity;
            ELSE
                UPDATE CURRENT_STOCK
                SET Quantity = Quantity - p_Quantity
                WHERE Current_StockID = @rowID;
                SET p_Quantity = 0;
            END IF;
        END WHILE;

        -- Update ITEM table with the remaining stock quantity
        UPDATE ITEM
        SET Quantity = Quantity - p_Quantity
        WHERE ItemID = p_ItemID;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Not enough stock to remove the specified quantity.';
    END IF;
END$$

-- Add New Item Procedure ---
DELIMITER $$
CREATE PROCEDURE AddNewItem(
    IN p_Category VARCHAR(255),
    IN p_Item_Name VARCHAR(50),
    IN p_Picture_URL VARCHAR(255),
    IN p_Cost DECIMAL(10, 2),
    IN p_Item_Type ENUM('Disposable', 'Reusable', 'Other'),
    IN p_Item_Description TEXT,
    IN p_Min_Quantity INT,
    IN p_SupplierID INT
)
BEGIN
    INSERT INTO ITEM (Category, Item_Name, Picture_URL, Cost, Item_Type, Item_Description, Min_Quantity, SupplierID)
    VALUES (p_Category, p_Item_Name, p_Picture_URL, p_Cost, p_Item_Type, p_Item_Description, p_Min_Quantity, p_SupplierID);
END$$

-- Update Item Information ---
DELIMITER $$
CREATE PROCEDURE UpdateItemInfo(
    IN p_ItemID INT,
    IN p_Category VARCHAR(255),
    IN p_Item_Name VARCHAR(50),
    IN p_Picture_URL VARCHAR(255),
    IN p_Cost DECIMAL(10, 2),
    IN p_Item_Type ENUM('Disposable', 'Reusable', 'Other'),
    IN p_Item_Description TEXT,
    IN p_Min_Quantity INT,
    IN p_SupplierID INT
)
BEGIN
    -- Update item information in ITEM table
    UPDATE ITEM
    SET Category = p_Category,
        Item_Name = p_Item_Name,
        Picture_URL = p_Picture_URL,
        Cost = p_Cost,
        Item_Type = p_Item_Type,
        Item_Description = p_Item_Description,
        Min_Quantity = p_Min_Quantity,
        SupplierID = p_SupplierID
    WHERE ItemID = p_ItemID;
END$$

-- Delete an Item from Inventory ---
DELIMITER $$
CREATE PROCEDURE DeleteItem(
    IN p_ItemID INT
)
BEGIN
    -- Delete item from CURRENT_STOCK
    DELETE FROM CURRENT_STOCK
    WHERE ItemID = p_ItemID;
    
    -- Delete item from ITEM table
    DELETE FROM ITEM
    WHERE ItemID = p_ItemID;
END$$


--- Add a new Supplier ---
DELIMITER $$
CREATE PROCEDURE AddSupplier(
    IN p_Supplier_Name VARCHAR(255),
    IN p_Contact_Info VARCHAR(50),
    IN p_Email VARCHAR(255),
    IN p_Supplier_Address VARCHAR(255)
)
BEGIN
    INSERT INTO SUPPLIER (Suppleir_Name, Contact_Info, Email, Supplier_Address)
    VALUES (p_Supplier_Name, p_Contact_Info, p_Email, p_Supplier_Address);
END$$


-- Check Low Stock Items ---
DELIMITER $$
CREATE PROCEDURE CheckLowStockItems()
BEGIN
    -- Select items with stock lower than the minimum quantity
    SELECT i.Item_Name, cs.Quantity, i.Min_Quantity
    FROM ITEM i
    JOIN CURRENT_STOCK cs ON i.ItemID = cs.ItemID
    WHERE cs.Quantity < i.Min_Quantity;
END$$

-- Reset delimiter
DELIMITER ;

-- Fetch Data For All Users For Admin Dashboard ---
DELIMITER $$
CREATE PROCEDURE GetAllUsers()
BEGIN
    SELECT UserID, First_Name, Last_Name, Username, PWD, Email, User_Role, Gender, Created_At, User_Status FROM USERS;
END$$
DELIMITER ;
