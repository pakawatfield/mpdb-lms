<?php
session_start();
include 'db.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $conn->real_escape_string($_POST['username']);
    $pass = $conn->real_escape_string($_POST['password']);

    // เช็คชื่อผู้ใช้และรหัสผ่านแบบตรงๆ (ไม่ Hash) ตามความต้องการ
    $sql = "SELECT * FROM admins WHERE username = '$user' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ระบบจัดการสื่อการสอน</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Prompt', sans-serif;
            font-weight: 300; 
            color: #333;
            height: 100vh;
        }
        h3 {
            font-weight: 400 !important;
        }
        .card {
            border: none;
            border-radius: 10px;
        }
        .btn-primary {
            background-color: #b30000;
            border-color: #b30000;
        }
        .btn-primary:hover {
            background-color: #990000;
            border-color: #990000;
        }
        a {
            text-decoration: none;
            transition: 0.2s;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="d-flex align-items-center py-4">
    <div class="container" style="max-width: 400px;">
        <form method="POST" class="card p-4 shadow-sm bg-white">
            <div class="text-center mb-4">
                <div class="mb-2 text-danger" style="font-size: 2.5rem;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h3 class="mb-1">Admin Login</h3>
                <p class="text-muted small">ระบบจัดการสื่อการสอนหลังบ้าน</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger py-2 small text-center"><?= $error; ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">USERNAME</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="กรอกชื่อผู้ใช้" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-key text-muted"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="กรอกรหัสผ่าน" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 mb-3 fw-bold">เข้าสู่ระบบ</button>
            
            <!-- ปุ่มย้อนกลับไปหน้าแรก -->
            <div class="text-center">
                <a href="index.php" class="text-muted small">
                    <i class="fa-solid fa-arrow-left me-1"></i> กลับสู่หน้าแรกเว็บไซต์
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>