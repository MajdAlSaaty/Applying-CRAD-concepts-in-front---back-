<?php
require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/crypto.php';
$config = require __DIR__ . '/../src/config.php';

$key = $config['xor_key'];
$action = $_GET['action'] ?? 'list';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO users (name_enc, email_enc, password_enc) VALUES (?, ?, ?)");
    $stmt->execute([
        encrypt_for_db($_POST['name'], $key),
        encrypt_for_db($_POST['email'], $key),
        encrypt_for_db($_POST['password'], $key)
    ]);
    header("Location: index.php");
    exit;
}

if ($action === 'delete') {
    $id = intval($_GET['id']);
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    header("Location: index.php");
    exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE users SET name_enc=?, email_enc=?, password_enc=? WHERE id=?");
    $stmt->execute([
        encrypt_for_db($_POST['name'], $key),
        encrypt_for_db($_POST['email'], $key),
        encrypt_for_db($_POST['password'], $key),
        $_POST['id']
    ]);
    header("Location: index.php");
    exit;
}

$rows = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Improved XOR CRUD</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #1f1f1f, #3a3a3a);
            color: #eee;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
        }

        .card {
            background: #2b2b2b;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
            margin-bottom: 25px;
        }

        h1, h2 {
            margin-top: 0;
            font-weight: 600;
        }

        input {
            width: 95%;
            padding: 10px;
            border-radius: 8px;
            border: none;
            margin-bottom: 10px;
            background: #3a3a3a;
            color: #fff;
        }

        button {
            background: #4e8cff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            color: white;
            font-weight: 600;
        }

        button:hover {
            background: #6a9dff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #2d2d2d;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #444;
            text-align: left;
        }

        th {
            background: #383838;
        }

        .actions button {
            margin-right: 5px;
        }

        .export-btn {
            padding: 5px;
            color:#fff;
            background: #0f4b2fff;
            margin-right: 10px;
        }

        a.export-btn {
              text-decoration: none;
        }

        .export-pdf{
            padding: 5px;
            color:#fff;
            background: #d22727ff;
            margin-right: 10px;
             cursor: pointer;
 
        }

        .modal-bg {
            display:none;
            position:fixed; 
            top:0; 
            left:0;
            width:100%; 
            height:100%;
            background:rgba(0,0,0,0.7);
        }

        .modal {
            background:#2b2b2b;
            width:350px;
            margin:100px auto;
            padding:20px;
            border-radius:12px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">
        <h1>Stream XOR Encryption CRUD</h1>

        <a  href="../export_excel.php" class="export-btn"><i class="fa-solid fa-file-excel"></i> Export Excel</a>
        <a onclick="exportPDF()" class="export-pdf"><i class="fa-solid fa-file-pdf"></i>  Export PDF</a>
        
    </div>


    <div class="card">
        <h2>Create User</h2>
        <form method="post" action="?action=create">
            <input name="name" placeholder="Name" required>
            <input name="email" placeholder="Email" required>
            <input name="password" placeholder="Password" required>
            <button type="submit"><i class="fa-solid fa-plus"></i> Add User</button>
        </form>
    </div>


    <div class="card">
        <h2>Users List</h2>
        <table id="recordsTable">
            <tr>
                <th>ID</th>
                <th>Name (dec)</th>
                <th>Email (dec)</th>
                <th>Password (dec)</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= decrypt_from_db($r['name_enc'], $key) ?></td>
                    <td><?= decrypt_from_db($r['email_enc'], $key) ?></td>
                    <td><?= decrypt_from_db($r['password_enc'], $key) ?></td>

                    <td class="actions">
                        <button style="background:#0984e3"
                                onclick="showEdit(<?= $r['id'] ?>,
                                '<?= rawurlencode(decrypt_from_db($r['name_enc'], $key)) ?>',
                                '<?= rawurlencode(decrypt_from_db($r['email_enc'], $key)) ?>',
                                '<?= rawurlencode(decrypt_from_db($r['password_enc'], $key)) ?>')">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>

                        <a href="?action=delete&id=<?= $r['id'] ?>">
                            <button style="background:#d63031"><i class="fa-solid fa-trash"></i></button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>


<div class="modal-bg" id="editModal">
    <div class="modal">
        <h2>Edit User</h2>

        <form method="post" action="?action=update">
            <input type="hidden" id="edit_id" name="id">

            <input id="edit_name" name="name" required>
            <input id="edit_email" name="email" required>
            <input id="edit_password" name="password">

            <button type="submit"><i class="fa-solid fa-check"></i> Update</button>
            <button type="button" onclick="hideEdit()" style="background:#555">Cancel</button>
        </form>
    </div>
</div>

<script>
function showEdit(id, name, email, password) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = decodeURIComponent(name);
    document.getElementById('edit_email').value = decodeURIComponent(email);
    document.getElementById('edit_password').value = decodeURIComponent(password);

    document.getElementById('editModal').style.display = "block";
}

function hideEdit() {
    document.getElementById('editModal').style.display = "none";
}
</script>
<script>
function exportPDF() {
    const element = document.getElementById('recordsTable');

    const options = {
        margin: 5,
        filename: 'users.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().from(element).set(options).save();
}
</script>

</body>
</html>
