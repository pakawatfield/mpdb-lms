<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// 1. ตรวจสอบว่ากำลังแก้ไขอะไรอยู่ (lesson, announcement, material)
$type = $_GET['type'] ?? '';
$id = intval($_GET['id'] ?? 0);

// --- ส่วนจัดการการบันทึกข้อมูลเมื่อกด Submit ---
if (isset($_POST['update_lesson'])) {
    $week_no = $conn->real_escape_string($_POST['week_no']);
    $subject_title = $conn->real_escape_string($_POST['subject_title']);
    $conn->query("UPDATE lessons SET week_no = '$week_no', subject_title = '$subject_title' WHERE lesson_id = $id");
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['update_announcement'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $conn->query("UPDATE announcements SET title = '$title', content = '$content' WHERE announcement_id = $id");
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['update_material'])) {
    $category = $conn->real_escape_string($_POST['category']);
    $title = $conn->real_escape_string($_POST['title']);
    $url = $conn->real_escape_string($_POST['url']);
    $conn->query("UPDATE lesson_materials SET category = '$category', title = '$title', url = '$url' WHERE material_id = $id");
    header("Location: dashboard.php");
    exit();
}

// --- ดึงข้อมูลเดิมมาแสดงในฟอร์ม ---
$data = null;
if ($type == 'lesson') {
    $res = $conn->query("SELECT * FROM lessons WHERE lesson_id = $id");
    $data = $res->fetch_assoc();
} elseif ($type == 'announcement') {
    $res = $conn->query("SELECT * FROM announcements WHERE announcement_id = $id");
    $data = $res->fetch_assoc();
} elseif ($type == 'material') {
    $res = $conn->query("SELECT * FROM lesson_materials WHERE material_id = $id");
    $data = $res->fetch_assoc();
}

if (!$data) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูล - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <style>body { font-family: 'Prompt', sans-serif; font-weight: 300; background-color: #f8f9fa; }</style>
</head>
<body>
    <div class="container my-5" style="max-width: 700px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="m-0">แก้ไขข้อมูล (Edit Data)</h5>
            </div>
            <div class="card-body">
                
                <!-- ฟอร์มแก้ไขสัปดาห์ -->
                <?php if ($type == 'lesson'): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">ชื่อ/หัวข้อสัปดาห์</label>
                        <input type="text" name="week_no" class="form-control" value="<?= $data['week_no']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ชื่อหัวข้อบทเรียน (Subject Title)</label>
                        <input type="text" name="subject_title" class="form-control" value="<?= $data['subject_title']; ?>" required>
                    </div>
                    <button type="submit" name="update_lesson" class="btn btn-success">บันทึกการแก้ไข</button>
                    <a href="dashboard.php" class="btn btn-secondary">ย้อนกลับ</a>
                </form>

                <!-- ฟอร์มแก้ไขประกาศ -->
                <?php elseif ($type == 'announcement'): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">หัวข้อประกาศ</label>
                        <input type="text" name="title" class="form-control" value="<?= $data['title']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">เนื้อหาประกาศ</label>
                        <textarea name="content" id="summernote" class="form-control" required><?= $data['content']; ?></textarea>
                    </div>
                    <button type="submit" name="update_announcement" class="btn btn-success">บันทึกการแก้ไข</button>
                    <a href="dashboard.php" class="btn btn-secondary">ย้อนกลับ</a>
                </form>

                <!-- ฟอร์มแก้ไขลิงก์สื่อการสอน -->
                <?php elseif ($type == 'material'): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">ประเภทสื่อ</label>
                        <select name="category" class="form-control" required>
                            <option value="slide_video" <?= ($data['category'] == 'slide_video') ? 'selected' : ''; ?>>Slide & Video</option>
                            <option value="reading" <?= ($data['category'] == 'reading') ? 'selected' : ''; ?>>Reading</option>
                            <option value="animation" <?= ($data['category'] == 'animation') ? 'selected' : ''; ?>>Animation</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ชื่อลิงก์</label>
                        <input type="text" name="title" class="form-control" value="<?= $data['title']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL ลิงก์</label>
                        <input type="url" name="url" class="form-control" value="<?= $data['url']; ?>" required>
                    </div>
                    <button type="submit" name="update_material" class="btn btn-success">บันทึกการแก้ไข</button>
                    <a href="dashboard.php" class="btn btn-secondary">ย้อนกลับ</a>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
   <script>
        $('#summernote').summernote({
            height: 150,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']], // รองรับการทำไฮไลต์ข้อความและเปลี่ยนสี
                ['para', ['ul', 'ol', 'paragraph', 'height']], // เพิ่ม 'height' ตรงนี้เพื่อให้ปรับระยะห่างบรรทัดได้
                ['insert', ['link', 'picture']]
            ]
        });
    </script>
</body>
</html>