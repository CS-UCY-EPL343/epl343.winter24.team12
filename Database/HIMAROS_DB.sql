-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS HIMAROS_DB;

-- Switch to the HIMAROS database
USE HIMAROS_DB;

-- USER TABLE
CREATE TABLE USERS_NEW (
  UserID INT NOT NULL AUTO_INCREMENT,
  First_Name VARCHAR(50) NOT NULL,
  Last_Name VARCHAR(50) NOT NULL,
  Username VARCHAR(101) GENERATED ALWAYS AS (CONCAT(First_Name, ' ', Last_Name)) STORED,
  PWD VARCHAR(255) NOT NULL,
  Email VARCHAR(255) NOT NULL UNIQUE,
  User_Role ENUM('admin', 'doctor', 'storekeeper', 'nurse', 'secretary') NOT NULL,
  Gender ENUM('Male', 'Female', 'Other') NOT NULL,
  Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  User_Status ENUM('Active', 'Inactive') DEFAULT 'Active',
  PRIMARY KEY (UserID)
);

-- OPERATION TABLE
CREATE TABLE OPERATION (
  OperationID INT NOT NULL AUTO_INCREMENT,
  Operation_Type VARCHAR(255) NOT NULL,
  Performed_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (OperationID)
);

-- SUPPLIER TABLE
CREATE TABLE SUPPLIER (
  SupplierID INT NOT NULL AUTO_INCREMENT,
  Suppleir_Name VARCHAR(255) NOT NULL,
  Contact_Info VARCHAR(50) NOT NULL,
  Email VARCHAR(255) NOT NULL UNIQUE,
  Supplier_Address VARCHAR(255) NOT NULL,
  PRIMARY KEY (SupplierID)
);

-- ITEM TABLE
CREATE TABLE ITEM (
  ItemID INT NOT NULL AUTO_INCREMENT,
  Category VARCHAR(255) NOT NULL,
  Item_Name VARCHAR(50) NOT NULL UNIQUE,
  Picture_URL VARCHAR(255),
  Cost DECIMAL(10, 2) NOT NULL,
  Item_Type ENUM('Disposable', 'Reusable', 'Other') NOT NULL,
  Item_Description TEXT NOT NULL,
  Min_Quantity INT NOT NULL,
  SupplierID INT NOT NULL,
  Being_Used ENUM('Yes', 'No') DEFAULT 'Yes',
  PRIMARY KEY(ItemID),
  FOREIGN KEY(SupplierID) REFERENCES SUPPLIER(SupplierID)
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- OPERATION_ITEM_USAGE TABLE
CREATE TABLE OPERATION_ITEM_USAGE (
  Operation_Item_UsageID INT NOT NULL AUTO_INCREMENT,
  OperationID INT NOT NULL,
  ItemID INT NOT NULL,
  Quantity_Used INT NOT NULL,
  Date_Used DATE NOT NULL,
  PRIMARY KEY (Operation_Item_UsageID),
  FOREIGN KEY(OperationID) REFERENCES OPERATION(OperationID)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(ItemID) REFERENCES ITEM(ItemID)
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- CURRENT_STOCK TABLE
CREATE TABLE CURRENT_STOCK (
  Current_StockID INT NOT NULL AUTO_INCREMENT,
  ItemID INT NOT NULL,
  Quantity INT NOT NULL,
  Expiration_Date DATE NOT NULL,
  Barcode BIGINT NOT NULL,
  PRIMARY KEY(Current_StockID),
  FOREIGN KEY(ItemID) REFERENCES ITEM(ItemID)
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- PERFORMES TABLE
CREATE TABLE PERFORMES (
  UserID INT NOT NULL,
  OperationID INT NOT NULL,
  PRIMARY KEY(UserID, OperationID),
  FOREIGN KEY(UserID) REFERENCES USERS(UserID)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(OperationID) REFERENCES OPERATION(OperationID)
    ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE UPDATES (
  UserID INT NOT NULL,
  User_FirstName VARCHAR(50),
  User_LastName VARCHAR(50),
  ItemID INT NOT NULL,
  Date_Updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(UserID, ItemID, Date_Updated),
  FOREIGN KEY(UserID) REFERENCES USERS(UserID)
     ON UPDATE CASCADE,
  FOREIGN KEY(ItemID) REFERENCES ITEM(ItemID)
     ON UPDATE CASCADE
);

--- ADD USERS ---

INSERT INTO USERS (First_Name, Last_Name, Email, Role, Username, Password, Gender)
VALUES
('Admin', 'User', 'admin@example.com', 1, 'admin', SHA1('adminpass'), 'Male'),
('Doctor', 'User', 'doctor@example.com', 2, 'doctor', SHA1('doctorpass'), 'Male'),
('Nurse', 'User', 'nurse@example.com', 3, 'nurse', SHA1('nursepass'), 'Female'),
('Secretary', 'User', 'secretary@example.com', 4, 'secretary', SHA1('secretarypass'), 'Female');
