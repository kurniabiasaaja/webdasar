<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$login_count = $_SESSION['login_count'];

$register_success = '';
$register_error = '';

if (!isset($_SESSION['daftar'])) {
    $_SESSION['daftar'] = [];
}

if (isset($_GET['hapus'])) {
    $index = (int)$_GET['hapus'];
    if (isset($_SESSION['daftar'][$index])) {
        unset($_SESSION['daftar'][$index]);
        $_SESSION['daftar'] = array_values($_SESSION['daftar']); // reset index
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $umur = trim($_POST['umur'] ?? '');

    $umur_int = (int)$umur;
    if ($umur_int < 18) {
        $keterangan = 'Remaja';
    } elseif ($umur_int < 60) {
        $keterangan = 'Dewasa';
    } else {
        $keterangan = 'Tua';
    }

    $edit_index = isset($_POST['edit_index']) ? (int)$_POST['edit_index'] : null;

    if ($nama && $umur && $keterangan !== '') {
        $daftar = [
            "nama" => $nama,
            "umur" => $umur,
            "keterangan" => $keterangan
        ];

        if ($edit_index !== null && isset($_SESSION['daftar'][$edit_index])) {
            $_SESSION['daftar'][$edit_index] = $daftar;
            $register_success = "Data berhasil <strong>diubah</strong>.";
        } else {
            $_SESSION['daftar'][] = $daftar;
            $register_success = "Pengguna <strong>" . htmlspecialchars($nama) . "</strong> berhasil didaftarkan.";
        }
    } else {
        $register_error = "Semua field harus diisi.";
    }
}

$target = null;
$edit_mode = false;
if (isset($_GET['edit'])) {
    $index = (int)$_GET['edit'];
    if (isset($_SESSION['daftar'][$index])) {
        $target = $_SESSION['daftar'][$index];
        $target['index'] = $index;
        $edit_mode = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7f9fc;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            text-align: center;
            width: 100%;
            max-width: 450px;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .highlight {
            font-weight: 600;
            color: #007bff;
        }

        .section-title {
            font-weight: 600;
            margin: 20px 0 10px;
            font-size: 16px;
            text-align: left;
        }

        form {
            margin-top: 10px;
            text-align: left;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        button {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .logout-btn {
            margin-top: 15px;
            background-color: #e74c3c;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
        }

        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .error {
            background-color: #fdecea;
            color: #c62828;
        }

        .user-list {
            margin-top: 20px;
            text-align: left;
        }

        .user-list ul {
            list-style: none;
            padding-left: 0;
        }

        .user-list li {
            padding: 6px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>👋 Selamat Datang, <span class="highlight"><?= htmlspecialchars($username); ?></span></h1>
        <p>Login ke-<span class="highlight"><?= $login_count; ?></span></p>

        <div class="section-title">📝 Pendaftaran Pengguna</div>
        <form method="post">
            <input type="text" name="nama" placeholder="Nama Lengkap" value="<?= $target['nama'] ?? '' ?>" required>
            <input type="number" name="umur" placeholder="Umur" value="<?= $target['umur'] ?? '' ?>" required>
            <?php if ($edit_mode): ?>
                <input type="hidden" name="edit_index" value="<?= $target['index'] ?>">
            <?php endif; ?>
            <button type="submit"><?= $edit_mode ? 'Update' : 'Daftar' ?></button>

            <?php if ($register_success): ?>
                <div class="message success"><?= $register_success ?></div>
            <?php elseif ($register_error): ?>
                <div class="message error"><?= $register_error ?></div>
            <?php endif; ?>
        </form>


        <div class="user-list">
            <div class="section-title">👥 Daftar Pengguna:</div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr>
                        <th style="text-align: left;">Nama</th>
                        <th>Umur</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['daftar'] as $i => $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['nama']) ?></td>
                            <td style="text-align: center;"><?= htmlspecialchars($user['umur']) ?> th</td>
                            <td><?= htmlspecialchars($user['keterangan']) ?></td>
                            <td style="text-align: center;">
                                <a href="?edit=<?= $i ?>">Ubah</a> |
                                <a href="?hapus=<?= $i ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>

</html>