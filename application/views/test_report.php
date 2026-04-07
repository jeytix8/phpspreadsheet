<!DOCTYPE html>
<html>

<head>
    <title>Test Report</title>
</head>

<body>

    <h2>PhpSpreadsheet Practice</h2>

    <form method="get" action="<?= site_url('TestReport/export') ?>">

        ```
        <label>Start Date:</label>
        <input type="date" name="start_date" required>

        <label>End Date:</label>
        <input type="date" name="end_date" required>

        <br><br>

        <label>Salon:</label>
        <select name="location_id">
            <option value="">All Salons</option>
            <?php foreach ($locations as $loc): ?>
                <option value="<?= $loc['location_id'] ?>">
                    <?= $loc['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label>Product:</label>
        <select name="item_id">
            <option value="">All Products</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?= $prod['item_id'] ?>">
                    <?= $prod['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label>Format:</label>
        <select name="format">
            <option value="excel">Excel</option>
            <option value="pdf">PDF</option>
        </select>

        <br><br>

        <button type="submit">Export Excel</button>
        ```

    </form>


</body>

</html>