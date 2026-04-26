<?php
require_once 'config.php';

$post_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT p.*, u.display_name, c.name as category_name 
                        FROM posts p 
                        LEFT JOIN users u ON p.author_id = u.id 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE p.id = ? AND p.status = 'published'");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    header('Location: index.php');
    exit;
}

$update = $conn->prepare("UPDATE posts SET view_count = view_count + 1 WHERE id = ?");
$update->bind_param("i", $post_id);
$update->execute();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post['title']; ?> - ข่าวสารสำนักทะเบียน</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <img src="images/logo.png" alt="Logo" style="height: 40px; border-radius: 5px;">
                    <span>ข่าวสารสำนักทะเบียน</span>
                </a>
                <ul class="nav-links">
                    <li><a href="index.php"><i class="fas fa-home"></i> หน้าหลัก</a></li>
                    <li><a href="login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> กลับไปหน้าหลัก
            </a>
            
            <div class="post-detail">
                <div class="post-detail-header">
                    <span class="status-badge published"><?php echo $post['category_name'] ?? 'ไม่มีหมวดหมู่'; ?></span>
                    <h1><?php echo $post['title']; ?></h1>
                    <p>
                        <i class="fas fa-user"></i> <?php echo $post['display_name'] ?? 'ไม่ระบุ'; ?> | 
                        <i class="fas fa-calendar"></i> <?php echo date('d M Y', strtotime($post['published_at'])); ?> | 
                        <i class="fas fa-eye"></i> <?php echo $post['view_count']; ?> ครั้ง
                    </p>
                </div>
                <div class="post-detail-body">
                    <?php if ($post['featured_image']): ?>
                        <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo $post['title']; ?>">
                    <?php endif; ?>
                    
                    <?php echo $post['content']; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ข่าวสารสำนักทะเบียน | พัฒนาโดย สำนักทะเบียน</p>
        </div>
    </footer>
</body>
</html>