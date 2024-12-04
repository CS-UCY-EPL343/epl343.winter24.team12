font-weight: bold;
        }

        .sidebar {
            width: 180px;
            background-color: #1a4f6e;
            height: 100vh;
            position: fixed;
            top: 60px;
            left: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
            padding-left: 20px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            gap: 10px;
            padding: 10px 0;
            width: 100%;
        }

        .sidebar a.active {
            background-color: #ffc107;
            color: #1a4f6e;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .search-bar input {
            padding: 10px;
            width: 250px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .content {
            margin-left: 200px;
            padding: 100px;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .action-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            color: white;
            background-color: #1a73e8;
        }

        .action-buttons .add-item-btn {
            background-color: #34a853;
        }

        .table {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #1a4f6e;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <strong>HIMAROS</strong>
        </div>
    </div>

    <div class="sidebar">
        <a href="nurse_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="#" class="active"><i class="fas fa-boxes"></i> Inventory</a>
        <a href="#"><i class="fas fa-users"></i> Users</a>
    </div>

    <div class="content">
        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search items...">
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button onclick="window.location.href='view_item_info.php';">Scan Item</button>
            <button class="add-item-btn" onclick="window.location.href='add_item.php';">Add Item</button>
        </div>

        <!-- Inventory Table -->
        <div class="table">
            <table id="inventoryTable">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Min Quantity</th>
                        <th>Supplier</th>
                        <th>Expiration Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['Item_Name']); ?></td>
                            <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                            <td><?php echo htmlspecialchars($item['Min_Quantity']); ?></td>
                            <td><?php echo htmlspecialchars($item['Suppleir_Name']); ?></td>
                            <td><?php echo htmlspecialchars($item['Expiration_Date']); ?></td>
                            <td><?php echo htmlspecialchars($item['Status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($inventory_items)): ?>
                        <tr>
                            <td colspan="6">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
