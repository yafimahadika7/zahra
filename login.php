<?php
session_start();
require_once "koneksi/koneksi.php"; // pastikan path benar

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query cek user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {

        // Verifikasi password bcrypt
        if (password_verify($password, $data['password'])) {

            // Simpan session
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['role'] = $data['role'];

            // Redirect role
            if ($data['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($data['role'] === 'kasir') {
                header("Location: kasir/dashboard.php");
            } elseif ($data['role'] === 'owner') {
                header("Location: owner/dashboard.php");
            } else {
                header("Location: index.php"); // fallback
            }
            exit;

        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - Kedai Kopi 69</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: url('asset/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .overlay {
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.55);
            position: fixed;
            top: 0;
            left: 0;
        }

        .login-box {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 420px;
            padding: 30px 36px 24px;
            background: rgba(20, 20, 20, 0.80);
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(4px);
        }

        .login-box h2 {
            text-align: center;
            margin: 0 0 24px;
            font-size: 26px;
            letter-spacing: 2px;
            font-weight: 600;
            color: #e8c391;
        }

        .error-box {
            background: #ffdddd;
            color: #b30000;
            border-left: 4px solid red;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #f2f2f2;
            margin-bottom: 6px;
        }

        .login-input {
            display: block;
            width: 100%;
            padding: 11px 12px;
            border-radius: 8px;
            border: 1px solid #444;
            background: #f8f8f8;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }

        .login-input:focus {
            border-color: #c49153;
            box-shadow: 0 0 4px rgba(196, 145, 83, 0.8);
            background: #ffffff;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border: none;
            border-radius: 8px;
            background: #c49153;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.2s;
        }

        .login-btn:hover {
            background: #a7743e;
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.25);
            transform: translateY(-1px);
        }

        .footer-text {
            text-align: center;
            margin-top: 16px;
            font-size: 11px;
            color: #ddd;
            opacity: 0.85;
        }
    </style>
</head>

<body>
    <div class="overlay"></div>

    <div class="login-box">
        <h2>KEDAI KOPI 69</h2>

        <?php if (!empty($error)): ?>
            <div class="error-box">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="login-input" placeholder="Masukkan username"
                    required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="login-input" placeholder="Masukkan password"
                    required>
            </div>

            <button class="login-btn" type="submit">LOGIN</button>
        </form>

        <p class="footer-text">© 2025 Kedai Kopi 69</p>
    </div>

</body>

</html>