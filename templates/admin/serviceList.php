<?php $activeTab = "services"; include 'templates/admin/header.php'; ?>
    <h2>Services</h2>

    <p><a href="editService.php">Add new service</a></p>
    
    <table class="dataTable">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Is currently available?</th>
        </tr>
        <?php $i = 0; while ( ($service = $services->next()) !== null) { ?>
        <tr class="<?= ($i % 2 == 0) ? 'even' : 'odd';?>">
            <td><?= $service->id; ?></td>
            <td><a href="editService.php?id=<?= $service->id; ?>"><?= htmlspecialchars($service->name); ?></a></td>
            <td><?= $service->duration; ?></td>
            <td><?= $service->price; ?></td>
            <td><?= ($service->available) ? 'Yes' : 'No'; ?></td>
        </tr>
        <?php $i++; } ?>
    </table>
<?php include 'templates/admin/footer.php'; ?>
