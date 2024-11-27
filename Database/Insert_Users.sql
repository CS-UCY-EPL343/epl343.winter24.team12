-- Use the database
USE HIMAROS;

-- Add 1 Admin
CALL AddUser('Admin', 'User', SHA1('adminpass'), 'admin@example.com', 'admin', 'Male');

-- Add 15 Doctors
CALL AddUser('John', 'Smith', SHA1('docpass1'), 'john.smith@example.com', 'doctor', 'Male');
CALL AddUser('Jane', 'Doe', SHA1('docpass2'), 'jane.doe@example.com', 'doctor', 'Female');
CALL AddUser('Emily', 'Brown', SHA1('docpass3'), 'emily.brown@example.com', 'doctor', 'Female');
CALL AddUser('Michael', 'Johnson', SHA1('docpass4'), 'michael.johnson@example.com', 'doctor', 'Male');
CALL AddUser('Chris', 'White', SHA1('docpass5'), 'chris.white@example.com', 'doctor', 'Male');
CALL AddUser('Sarah', 'Miller', SHA1('docpass6'), 'sarah.miller@example.com', 'doctor', 'Female');
CALL AddUser('Robert', 'Taylor', SHA1('docpass7'), 'robert.taylor@example.com', 'doctor', 'Male');
CALL AddUser('Jessica', 'Wilson', SHA1('docpass8'), 'jessica.wilson@example.com', 'doctor', 'Female');
CALL AddUser('David', 'Moore', SHA1('docpass9'), 'david.moore@example.com', 'doctor', 'Male');
CALL AddUser('Laura', 'Thomas', SHA1('docpass10'), 'laura.thomas@example.com', 'doctor', 'Female');
CALL AddUser('Daniel', 'Anderson', SHA1('docpass11'), 'daniel.anderson@example.com', 'doctor', 'Male');
CALL AddUser('Anna', 'Clark', SHA1('docpass12'), 'anna.clark@example.com', 'doctor', 'Female');
CALL AddUser('Paul', 'Harris', SHA1('docpass13'), 'paul.harris@example.com', 'doctor', 'Male');
CALL AddUser('Sophia', 'Martinez', SHA1('docpass14'), 'sophia.martinez@example.com', 'doctor', 'Female');
CALL AddUser('James', 'Garcia', SHA1('docpass15'), 'james.garcia@example.com', 'doctor', 'Male');

-- Add 5 Nurses
CALL AddUser('Linda', 'Scott', SHA1('nursepass1'), 'linda.scott@example.com', 'nurse', 'Female');
CALL AddUser('Barbara', 'Lee', SHA1('nursepass2'), 'barbara.lee@example.com', 'nurse', 'Female');
CALL AddUser('Susan', 'Walker', SHA1('nursepass3'), 'susan.walker@example.com', 'nurse', 'Female');
CALL AddUser('Nancy', 'Allen', SHA1('nursepass4'), 'nancy.allen@example.com', 'nurse', 'Female');
CALL AddUser('Margaret', 'Young', SHA1('nursepass5'), 'margaret.young@example.com', 'nurse', 'Female');

-- Add 5 Storekeepers
CALL AddUser('Mark', 'King', SHA1('storepass1'), 'mark.king@example.com', 'storekeeper', 'Male');
CALL AddUser('Steven', 'Wright', SHA1('storepass2'), 'steven.wright@example.com', 'storekeeper', 'Male');
CALL AddUser('Kevin', 'Hill', SHA1('storepass3'), 'kevin.hill@example.com', 'storekeeper', 'Male');
CALL AddUser('Brian', 'Green', SHA1('storepass4'), 'brian.green@example.com', 'storekeeper', 'Male');
CALL AddUser('Edward', 'Adams', SHA1('storepass5'), 'edward.adams@example.com', 'storekeeper', 'Male');

-- Add 3 Secretaries
CALL AddUser('Karen', 'Baker', SHA1('secpass1'), 'karen.baker@example.com', 'secretary', 'Female');
CALL AddUser('Helen', 'Carter', SHA1('secpass2'), 'helen.carter@example.com', 'secretary', 'Female');
CALL AddUser('Betty', 'Perez', SHA1('secpass3'), 'betty.perez@example.com', 'secretary', 'Female');
