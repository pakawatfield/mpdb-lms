<?php
include 'db.php';

// ดึงข้อมูลหลักของรายวิชา
$course_result = $conn->query("SELECT * FROM courses LIMIT 1");
$course = $course_result->fetch_assoc();

if (!$course) {
    $course = [
        'course_code' => '01-206-051-203',
        'course_name' => 'Media Production for Digital Business',
        'prerequisite' => '-',
        'lecturer' => 'Mr. Pakawat Kormitin',
        'room' => 'Room 01-314 / 01-302',
        'course_description' => 'Multimedia Production Principles, Importance, Rules, and Techniques...',
        'grading_policy' => 'Midterm 45 Final 55'
    ];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $course['course_code']; ?> <?= $course['course_name']; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }
        h1, h4, .accordion-button {
            font-weight: 400 !important;
        }
        .header-banner { background-color: #b30000; color: white; padding: 20px 0; }
        .section-banner { background-color: #cc0000; color: white; padding: 10px 15px; font-weight: 400; margin-top: 30px; margin-bottom: 20px; border-radius: 5px; }
        .accordion-button:not(.collapsed) { background-color: #fdf2f2; color: #b30000; }
        a { color: #0d6efd; transition: 0.2s; word-break: break-word; text-decoration: none; }
        a:hover { color: #0a58ca; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="header-banner">
        <div class="container">
            <h1 class="fs-3 fs-md-2"><?= $course['course_code']; ?> <?= $course['course_name']; ?></h1>
        </div>
    </div>

    <div class="container my-4">
        <p><strong>Prerequisite :</strong> <?= $course['prerequisite']; ?></p>
        <p><strong>Lecturer:</strong> <?= $course['lecturer']; ?></p>
        <p><strong>Room:</strong> <?= $course['room']; ?></p>

        <h4 class="mt-4">Course Description</h4>
        <p class="text-secondary"><?= $course['course_description']; ?></p>

        <h4 class="mt-4">Grade</h4>
        <ul>
            <li><?= $course['grading_policy']; ?></li>
        </ul>

        <div class="section-banner">
            Lesson Materials, Case Studies & Announcements
        </div>

        <div class="accordion" id="lessonAccordion">
            <?php 
            $lessons = $conn->query("SELECT * FROM lessons WHERE is_visible = 1 ORDER BY sort_order ASC, lesson_id ASC");
            if ($lessons && $lessons->num_rows > 0):
                $index = 0;
                while($row = $lessons->fetch_assoc()):
                    $lesson_id = $row['lesson_id'];
                    $index++;
                    $is_expanded = ($index == 1) ? 'true' : 'false';
                    $is_show = ($index == 1) ? 'show' : '';
            ?>
            <div class="accordion-item mb-3 shadow-sm border rounded">
                <h2 class="accordion-header" id="heading<?= $lesson_id; ?>">
                    <button class="accordion-button <?= ($index != 1) ? 'collapsed' : ''; ?> fs-6 fs-md-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $lesson_id; ?>" aria-expanded="<?= $is_expanded; ?>" aria-controls="collapse<?= $lesson_id; ?>">
                        <?= $row['week_no']; ?> : <?= $row['subject_title']; ?>
                    </button>
                </h2>
                <div id="collapse<?= $lesson_id; ?>" class="accordion-collapse collapse <?= $is_show; ?>" aria-labelledby="heading<?= $lesson_id; ?>" data-bs-parent="#lessonAccordion">
                    <div class="accordion-body bg-white">
                        
                        <!-- ส่วนประกาศ (เรียงตาม sort_order และแสดงเฉพาะที่เปิดใช้งาน) -->
                        <div class="mb-3">
                            <?php 
                            $ann_query = $conn->query("SELECT * FROM announcements WHERE lesson_id = $lesson_id AND is_visible = 1 ORDER BY sort_order ASC, announcement_id DESC");
                            while($ann = $ann_query->fetch_assoc()):
                            ?>
                                <div class="p-3 mb-2 rounded border bg-light">
                                    <?php if (!empty($ann['title'])): ?>
                                        <h6 class="text-danger mb-2"><?= $ann['title']; ?></h6>
                                    <?php endif; ?>
                                    <div class="text-dark"><?= $ann['content']; ?></div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <!-- ส่วนแสดงลิงก์สื่อการสอน (แสดงเฉพาะที่เปิด is_visible = 1) -->
                        <div class="row g-3">
                            <!-- Slide & Video -->
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light h-100">
                                    <span class="d-block fw-bold text-secondary mb-2 small text-uppercase">Slide & Video</span>
                                    <?php 
                                    $m_slide = $conn->query("SELECT * FROM lesson_materials WHERE lesson_id = $lesson_id AND category = 'slide_video' AND is_visible = 1");
                                    if ($m_slide->num_rows > 0):
                                        while($s = $m_slide->fetch_assoc()):
                                    ?>
                                        <a href="<?= $s['url']; ?>" target="_blank" class="d-block mb-1"><?= $s['title']; ?></a>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <span class="text-muted small">ไม่มีข้อมูล</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Reading -->
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light h-100">
                                    <span class="d-block fw-bold text-secondary mb-2 small text-uppercase">Reading</span>
                                    <?php 
                                    $m_read = $conn->query("SELECT * FROM lesson_materials WHERE lesson_id = $lesson_id AND category = 'reading' AND is_visible = 1");
                                    if ($m_read->num_rows > 0):
                                        while($r = $m_read->fetch_assoc()):
                                    ?>
                                        <a href="<?= $r['url']; ?>" target="_blank" class="d-block mb-1"><?= $r['title']; ?></a>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <span class="text-muted small">ไม่มีข้อมูล</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Animation -->
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light h-100">
                                    <span class="d-block fw-bold text-secondary mb-2 small text-uppercase">Animation</span>
                                    <?php 
                                    $m_anim = $conn->query("SELECT * FROM lesson_materials WHERE lesson_id = $lesson_id AND category = 'animation' AND is_visible = 1");
                                    if ($m_anim->num_rows > 0):
                                        while($a = $m_anim->fetch_assoc()):
                                    ?>
                                        <a href="<?= $a['url']; ?>" target="_blank" class="d-block mb-1"><?= $a['title']; ?></a>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <span class="text-muted small">ไม่มีข้อมูล</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php 
                endwhile; 
            else:
            ?>
            <div class="text-center text-muted py-4 card p-4">ยังไม่มีข้อมูลบทเรียนในระบบ หรือบทเรียนทั้งหมดถูกซ่อนไว้</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>