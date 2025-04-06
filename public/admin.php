<?php
// admin.php
include 'db.php';

$databasePath = __DIR__ . '/../data.sqlite';

$db;

try {
    // Connect to the SQLite database
    $db = new PDO("sqlite:$databasePath");

    // Set PDO to throw exceptions on errors
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$db = new DB($db);

if (isset($_GET['sub_id'])) {
    $sub_id = $_GET['sub_id'];
    $db->deleteSub($sub_id);
    header("Location: admin.php");
    exit;
}


?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bot Admin panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</head>

<body>
    <h1>Naya VPN bot admin panel</h1>
    <div class="container mt-5 border border-primary p-4 rounded shadow">
        <h2>Users & subs</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Sub URL</th>
                    <th>Expire</th>
                    <th>Data Limit</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users = $db->getAllUsers();
                foreach ($users as $user) {
                    $subs = $db->getSub($user->id);
                    foreach ($subs as $sub) {
                        ?>
                            <tr>
                                <td><?php echo $user->username; ?></td>
                                <td><?php echo $sub->sub_url; ?></td>
                                <td><?php echo $sub->exp_date; ?></td>
                                <td><?php echo $sub->data_limit; ?></td>
                                <td><a href="admin.php?sub_id=<?php echo $sub->id; ?>" class="btn btn-danger">Delete</a></td>
                            </tr>
                        <?php
                    }
                }
                ?>
            </tbody>

    </div>

</body>

</html>