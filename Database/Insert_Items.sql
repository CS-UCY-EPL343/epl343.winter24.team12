-- Use the database
USE HIMAROS_DB;

-- Add a new item to the ITEM table
CALL AddNewItem(
    'Medical Supplies',           -- Category
    'RADIFOCUS INTRODUCER  II FR.6',              -- Item Name
    ' ', -- Picture URL
    112.45,                       -- Cost
    'Disposable',                 -- Item Type
    'Medical device used in interventional procedures to facilitate catheter insertion, providing vascular access with minimal trauma.', -- Item Description
    5,                            -- Minimum Quantity
    6                             -- Supplier ID
);

CALL AddNewItem(
    'Medical Supplies',           -- Category
    'MEDRAD Avanta',              -- Item Name
    ' ', -- Picture URL
    123.66,                       -- Cost
    'Disposable',                 -- Item Type
    'A sterile, single-use kit designed for pressure monitoring in medical settings. It connects to compatible transducers for accurate measurements, ensuring safety and minimizing cross-contamination.', -- Item Description
    10,                          -- Minimum Quantity
    12                             -- Supplier ID
);

CALL AddNewItem(
    'Medical Supplies',           -- Category
    'SWC-IS-0611',                -- Item Name
    ' ', -- Picture URL
    46.5,                         -- Cost
    'Disposable',                 -- Item Type
    'Medical kit for vascular access, aiding catheter insertion with components like a sheath, dilator, and guidewire.', -- Item Description
    10,                          -- Minimum Quantity
    5                             -- Supplier ID
);

CALL AddNewItem(
    'Medical Supplies',           -- Category
    'RADIFOCUS INTRODUCER  II FR.7',              -- Item Name
    ' ', -- Picture URL
    122.25,                         -- Cost
    'Disposable',                 -- Item Type
    'A vascular access device designed to assist with catheter placement during interventional procedures. It features an introducer sheath and dilator for smooth and precise catheter insertion.', -- Item Description
    30,                          -- Minimum Quantity
    6                             -- Supplier ID
);

CALL AddNewItem(
    'Medical Supplies',           -- Category
    'RADIFOCUS INTRODUCER  II FR.5',              -- Item Name
    ' ', -- Picture URL
    99.5,                         -- Cost
    'Disposable',                 -- Item Type
    'Medical device used in interventional procedures. It facilitates the insertion of catheters into blood vessels with minimal trauma.', -- Item Description
    30,                          -- Minimum Quantity
    6                             -- Supplier ID
);

CALL AddNewItem(
    'Medical Supplies',           -- Category
    'SWC-IS-0511',              -- Item Name
    ' ', -- Picture URL
    60,                         -- Cost
    'Disposable',                 -- Item Type
    'Medical kit used for vascular access.', -- Item Description
    5,                          -- Minimum Quantity
    5                           -- Supplier ID
);

CALL AddNewItem(
    'Medical Supplies',           -- Category
    'SWC-IS-0711',              -- Item Name
    ' ', -- Picture URL
    72.5,                         -- Cost
    'Disposable',                 -- Item Type
    'Medical device kit designed to facilitate catheter insertion for vascular access.', -- Item Description
    5,                          -- Minimum Quantity
    5                           -- Supplier ID
);


