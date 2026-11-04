<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// 1. จัดการเพิ่มสัปดาห์ใหม่
if (isset($_POST['add_lesson'])) {
    $course_id = 1; 
    $week_no = $conn->real_escape_string($_POST['week_no']);
    $subject_title = $conn->real_escape_string($_POST['subject_title']);
    
    $conn->query("INSERT INTO lessons (course_id, week_no, subject_title, sort_order, is_visible) VALUES ($course_id, '$week_no', '$subject_title', 0, 1)");
    header("Location: dashboard.php");
    exit();
}

// 2. จัดการเพิ่มลิงก์สื่อการสอน
if (isset($_POST['add_material'])) {
    $lesson_id = intval($_POST['lesson_id']);
    $category = $conn->real_escape_string($_POST['category']);
    $title = $conn->real_escape_string($_POST['title']);
    $url = $conn->real_escape_string($_POST['url']);
    
    $conn->query("INSERT INTO lesson_materials (lesson_id, category, title, url, is_visible) VALUES ($lesson_id, '$category', '$title', '$url', 1)");
    header("Location: dashboard.php");
    exit();
}

// 3. จัดการเพิ่มประกาศ
if (isset($_POST['add_announcement'])) {
    $lesson_id = intval($_POST['lesson_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    
    $conn->query("INSERT INTO announcements (lesson_id, title, content, sort_order, is_visible) VALUES ($lesson_id, '$title', '$content', 0, 1)");
    header("Location: dashboard.php");
    exit();
}

// จัดการเลื่อนลำดับสัปดาห์ ขึ้น/ลง
if (isset($_GET['move_up'])) {
    $id = intval($_GET['move_up']);
    $current_order = intval($_GET['order']);
    $prev_order = $current_order - 1;
    
    $conn->query("UPDATE lessons SET sort_order = $current_order WHERE sort_order = $prev_order AND course_id = 1");
    $conn->query("UPDATE lessons SET sort_order = $prev_order WHERE lesson_id = $id");
    header("Location: dashboard.php");
    exit();
}
if (isset($_GET['move_down'])) {
    $id = intval($_GET['move_down']);
    $current_order = intval($_GET['order']);
    $next_order = $current_order + 1;
    
    $conn->query("UPDATE lessons SET sort_order = $current_order WHERE sort_order = $next_order AND course_id = 1");
    $conn->query("UPDATE lessons SET sort_order = $next_order WHERE lesson_id = $id");
    header("Location: dashboard.php");
    exit();
}

// จัดการเลื่อนลำดับประกาศ ขึ้น/ลง
if (isset($_GET['move_up_ann'])) {
    $id = intval($_GET['move_up_ann']);
    $current_order = intval($_GET['order']);
    $prev_order = $current_order - 1;
    $lesson_id = intval($_GET['lesson_id']);
    
    $conn->query("UPDATE announcements SET sort_order = $current_order WHERE sort_order = $prev_order AND lesson_id = $lesson_id");
    $conn->query("UPDATE announcements SET sort_order = $prev_order WHERE announcement_id = $id");
    header("Location: dashboard.php");
    exit();
}
if (isset($_GET['move_down_ann'])) {
    $id = intval($_GET['move_down_ann']);
    $current_order = intval($_GET['order']);
    $next_order = $current_order + 1;
    $lesson_id = intval($_GET['lesson_id']);
    
    $conn->query("UPDATE announcements SET sort_order = $current_order WHERE sort_order = $next_order AND lesson_id = $lesson_id");
    $conn->query("UPDATE announcements SET sort_order = $next_order WHERE announcement_id = $id");
    header("Location: dashboard.php");
    exit();
}

// จัดการสลับสถานะ ซ่อน/แสดง
if (isset($_GET['toggle_lesson'])) {
    $id = intval($_GET['toggle_lesson']);
    $status = intval($_GET['status']);
    $conn->query("UPDATE lessons SET is_visible = " . ($status == 1 ? 0 : 1) . " WHERE lesson_id = $id");
    header("Location: dashboard.php");
    exit();
}
if (isset($_GET['toggle_announcement'])) {
    $id = intval($_GET['toggle_announcement']);
    $status = intval($_GET['status']);
    $conn->query("UPDATE announcements SET is_visible = " . ($status == 1 ? 0 : 1) . " WHERE announcement_id = $id");
    header("Location: dashboard.php");
    exit();
}
if (isset($_GET['toggle_material'])) {
    $id = intval($_GET['toggle_material']);
    $status = intval($_GET['status']);
    $conn->query("UPDATE lesson_materials SET is_visible = " . ($status == 1 ? 0 : 1) . " WHERE material_id = $id");
    header("Location: dashboard.php");
    exit();
}

// จัดการลบข้อมูล
if (isset($_GET['del_lesson'])) {
    $conn->query("DELETE FROM lessons WHERE lesson_id = " . intval($_GET['del_lesson']));
    header("Location: dashboard.php");
    exit();
}
if (isset($_GET['del_material'])) {
    $conn->query("DELETE FROM lesson_materials WHERE material_id = " . intval($_GET['del_material']));
    header("Location: dashboard.php");
    exit();
}
if (isset($_GET['del_announcement'])) {
    $conn->query("DELETE FROM announcements WHERE announcement_id = " . intval($_GET['del_announcement']));
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ระบบจัดการสื่อการสอน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Prompt', sans-serif; font-weight: 300; background-color: #f4f6f9; }
        .navbar-brand { font-weight: 400; }
        .card { border: none; border-radius: 8px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .card-header { font-weight: 400; background-color: #fff; border-bottom: 1px solid rgba(0,0,0,.05); }
        .btn-light-custom { background-color: #f8f9fa; border: 1px solid #dee2e6; }
        .btn-light-custom:hover { background-color: #e9ecef; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <span class="navbar-brand"><i class="fa-solid fa-gauge-high me-2"></i> Admin Dashboard</span>
        <div>
            <a href="index.php" target="_blank" class="btn btn-outline-light btn-sm me-2"><i class="fa-solid fa-globe me-1"></i> ดูหน้าเว็บ</a>
            <a href="logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i> ออกจากระบบ</a>
        </div>
    </nav>

    <div class="container my-4">
        
        <!-- แผงปุ่มเปิดฟอร์ม (Accordion Buttons) เพื่อไม่ให้หน้ายาวเกินไป -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <button class="btn btn-primary w-100 py-3 text-start shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formLesson">
                    <i class="fa-solid fa-calendar-plus me-2"></i> + เพิ่มหัวข้อสัปดาห์เรียนใหม่
                </button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-secondary w-100 py-3 text-start shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formMaterial">
                    <i class="fa-solid fa-link me-2"></i> + เพิ่มลิงก์สื่อการสอน
                </button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-warning text-dark w-100 py-3 text-start shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formAnnouncement">
                    <i class="fa-solid fa-bullhorn me-2"></i> + โพสต์ข้อความประกาศ
                </button>
            </div>
        </div>

        <!-- 1. ฟอร์มเพิ่มสัปดาห์ (ซ่อนไว้กดเปิด) -->
        <div class="collapse mb-4" id="formLesson">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">เพิ่มหัวข้อสัปดาห์เรียนใหม่</div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">ชื่อ/หัวข้อสัปดาห์</label>
                            <input type="text" name="week_no" class="form-control" placeholder="เช่น สัปดาห์ที่ 1" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">ชื่อหัวข้อบทเรียน</label>
                            <input type="text" name="subject_title" class="form-control" placeholder="เช่น Introduction" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="add_lesson" class="btn btn-success w-100">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 2. ฟอร์มเพิ่มลิงก์สื่อ (ซ่อนไว้กดเปิด) -->
        <div class="collapse mb-4" id="formMaterial">
            <div class="card border-secondary">
                <div class="card-header bg-secondary text-white">เพิ่มลิงก์สไลด์ / วิดีโอ / เอกสารอ่าน</div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">เลือกสัปดาห์</label>
                            <select name="lesson_id" class="form-control" required>
                                <option value="">-- เลือกบทเรียน --</option>
                                <?php 
                                $lessons_opt1 = $conn->query("SELECT * FROM lessons ORDER BY sort_order ASC");
                                while($l = $lessons_opt1->fetch_assoc()): 
                                ?>
                                    <option value="<?= $l['lesson_id']; ?>"><?= $l['week_no']; ?>: <?= $l['subject_title']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">ประเภท</label>
                            <select name="category" class="form-control" required>
                                <option value="slide_video">Slide & Video</option>
                                <option value="reading">Reading</option>
                                <option value="animation">Animation</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ชื่อลิงก์</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">URL ลิงก์</label>
                            <input type="url" name="url" class="form-control" placeholder="https://..." required>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="submit" name="add_material" class="btn btn-dark px-4">บันทึกลิงก์</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. ฟอร์มเพิ่มประกาศ (ซ่อนไว้กดเปิด) -->
        <div class="collapse mb-4" id="formAnnouncement">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark fw-bold">โพสต์ข้อความประกาศประจำสัปดาห์</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">เลือกสัปดาห์</label>
                                <select name="lesson_id" class="form-control" required>
                                    <option value="">-- เลือกบทเรียน --</option>
                                    <?php 
                                    $lessons_opt2 = $conn->query("SELECT * FROM lessons ORDER BY sort_order ASC");
                                    while($l2 = $lessons_opt2->fetch_assoc()): 
                                    ?>
                                        <option value="<?= $l2['lesson_id']; ?>"><?= $l2['week_no']; ?>: <?= $l2['subject_title']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">หัวข้อประกาศ (ถ้ามี)</label>
                                <input type="text" name="title" class="form-control" placeholder="เช่น สั่งงานโปรเจกต์">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">เนื้อหาประกาศ</label>
                            <textarea name="content" id="summernote" class="form-control" required></textarea>
                        </div>
                        <button type="submit" name="add_announcement" class="btn btn-warning text-dark fw-bold px-4">โพสต์ประกาศ</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- รายการเนื้อหาทั้งหมด (จัดระเบียบใหม่ให้สะอาดตา) -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-list-check me-2"></i> จัดการเนื้อหาบทเรียนทั้งหมด</h5>
            </div>
            <div class="card-body p-3">
                <?php 
                $lessons_list = $conn->query("SELECT * FROM lessons ORDER BY sort_order ASC, lesson_id ASC");
                if ($lessons_list->num_rows > 0):
                    while($row = $lessons_list->fetch_assoc()): 
                        $lid = $row['lesson_id'];
                        $is_visible = $row['is_visible'];
                        $sort_order = $row['sort_order'];
                ?>
                    <div class="card border mb-3 <?= ($is_visible == 0) ? 'bg-light opacity-75' : 'bg-white'; ?>">
                        <div class="card-body p-3">
                            <!-- แถวหัวข้อสัปดาห์ -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="btn-group btn-group-sm">
                                        <a href="dashboard.php?move_up=<?= $lid; ?>&order=<?= $sort_order; ?>" class="btn btn-outline-secondary" title="เลื่อนขึ้น"><i class="fa-solid fa-arrow-up"></i></a>
                                        <a href="dashboard.php?move_down=<?= $lid; ?>&order=<?= $sort_order; ?>" class="btn btn-outline-secondary" title="เลื่อนลง"><i class="fa-solid fa-arrow-down"></i></a>
                                    </div>
                                    <h5 class="m-0 fw-bold text-primary"><?= $row['week_no']; ?> : <?= $row['subject_title']; ?></h5>
                                    <?php if ($is_visible == 1): ?>
                                        <span class="badge bg-success">แสดง</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">ซ่อน</span>
                                    <?php endif; ?>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="edit.php?type=lesson&id=<?= $lid; ?>" class="btn btn-outline-primary"><i class="fa-solid fa-pen"></i> แก้ไข</a>
                                    <a href="dashboard.php?toggle_lesson=<?= $lid; ?>&status=<?= $is_visible; ?>" class="btn btn-outline-secondary">
                                        <?= ($is_visible == 1) ? '<i class="fa-solid fa-eye-slash"></i> ซ่อน' : '<i class="fa-solid fa-eye"></i> แสดง'; ?>
                                    </a>
                                    <a href="dashboard.php?del_lesson=<?= $lid; ?>" class="btn btn-outline-danger" onclick="return confirm('ลบสัปดาห์นี้ทั้งหมด?')"><i class="fa-solid fa-trash"></i> ลบ</a>
                                </div>
                            </div>
                            
                            <!-- ส่วนประกาศย่อย -->
                            <div class="mb-2 ps-2">
                                <span class="text-muted small fw-bold"><i class="fa-solid fa-bullhorn me-1"></i> ประกาศ:</span>
                                <?php 
                                $ann_list = $conn->query("SELECT * FROM announcements WHERE lesson_id = $lid ORDER BY sort_order ASC, announcement_id DESC");
                                if ($ann_list->num_rows > 0):
                                    while($ann = $ann_list->fetch_assoc()):
                                        $ann_id = $ann['announcement_id'];
                                        $ann_visible = $ann['is_visible'];
                                        $ann_sort = $ann['sort_order'];
                                ?>
                                    <div class="p-2 mt-1 rounded border bg-light d-flex justify-content-between align-items-center small <?= ($ann_visible == 0) ? 'opacity-50' : ''; ?>">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="btn-group btn-group-xs">
                                                <a href="dashboard.php?move_up_ann=<?= $ann_id; ?>&order=<?= $ann_sort; ?>&lesson_id=<?= $lid; ?>" class="btn btn-sm btn-outline-secondary py-0 px-1"><i class="fa-solid fa-arrow-up"></i></a>
                                                <a href="dashboard.php?move_down_ann=<?= $ann_id; ?>&order=<?= $ann_sort; ?>&lesson_id=<?= $lid; ?>" class="btn btn-sm btn-outline-secondary py-0 px-1"><i class="fa-solid fa-arrow-down"></i></a>
                                            </div>
                                            <div>
                                                <?php if (!empty($ann['title'])): ?><strong><?= $ann['title']; ?>:</strong><?php endif; ?>
                                                <span><?= $ann['content']; ?></span>
                                                <?php if ($ann_visible == 0): ?><span class="badge bg-dark ms-1">ซ่อน</span><?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit.php?type=announcement&id=<?= $ann_id; ?>" class="btn btn-link text-primary p-0 me-2 text-decoration-none">แก้ไข</a>
                                            <a href="dashboard.php?toggle_announcement=<?= $ann_id; ?>&status=<?= $ann_visible; ?>" class="btn btn-link text-secondary p-0 me-2 text-decoration-none"><?= ($ann_visible == 1) ? 'ซ่อน' : 'แสดง'; ?></a>
                                            <a href="dashboard.php?del_announcement=<?= $ann_id; ?>" class="btn btn-link text-danger p-0 text-decoration-none" onclick="return confirm('ลบประกาศนี้?')">ลบ</a>
                                        </div>
                                    </div>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <span class="text-muted small ms-2">ไม่มีประกาศ</span>
                                <?php endif; ?>
                            </div>

                            <!-- ส่วนลิงก์สื่อการสอนย่อย -->
                            <div class="ps-2 mt-2">
                                <span class="text-muted small fw-bold"><i class="fa-solid fa-link me-1"></i> ลิงก์สื่อ:</span>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    <?php 
                                    $materials = $conn->query("SELECT * FROM lesson_materials WHERE lesson_id = $lid");
                                    if ($materials->num_rows > 0):
                                        while($m = $materials->fetch_assoc()):
                                            $mat_id = $m['material_id'];
                                            $mat_visible = $m['is_visible'];
                                    ?>
                                        <div class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2 <?= ($mat_visible == 0) ? 'opacity-50' : ''; ?>">
                                            <span class="badge bg-secondary"><?= $m['category']; ?></span>
                                            <a href="<?= $m['url']; ?>" target="_blank" class="text-decoration-none text-dark"><?= $m['title']; ?></a>
                                            <?php if ($mat_visible == 0): ?><span class="badge bg-dark">ซ่อน</span><?php endif; ?>
                                            <div class="ms-1 border-start ps-2">
                                                <a href="edit.php?type=material&id=<?= $mat_id; ?>" class="text-primary text-decoration-none me-1" title="แก้ไข"><i class="fa-solid fa-pen" style="font-size: 10px;"></i></a>
                                                <a href="dashboard.php?toggle_material=<?= $mat_id; ?>&status=<?= $mat_visible; ?>" class="text-secondary text-decoration-none me-1" title="ซ่อน/แสดง"><i class="fa-solid fa-eye-slash" style="font-size: 10px;"></i></a>
                                                <a href="dashboard.php?del_material=<?= $mat_id; ?>" class="text-danger text-decoration-none" title="ลบ" onclick="return confirm('ลบลิงก์นี้?')"><i class="fa-solid fa-xmark"></i></a>
                                            </div>
                                        </div>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <span class="text-muted small ms-2">ยังไม่มีลิงก์สื่อการสอน</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div class="text-center text-muted py-4">ยังไม่มีข้อมูลบทเรียนในระบบ</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $('#summernote').summernote({
            height: 150,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph', 'height']],
                ['insert', ['link', 'picture']]
            ]
        });
    </script>
</body>
</html>