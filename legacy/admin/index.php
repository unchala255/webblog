<?php
require_once '../config.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$stats = [
    'posts' => $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'],
    'published' => $conn->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published'")->fetch_assoc()['count'],
    'draft' => $conn->query("SELECT COUNT(*) as count FROM posts WHERE status = 'draft'")->fetch_assoc()['count'],
    'categories' => $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'],
    'users' => $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'],
    'views' => $conn->query("SELECT SUM(view_count) as total FROM posts")->fetch_assoc()['total'] ?? 0,
];

$recent_posts = $conn->query("SELECT p.*, u.display_name FROM posts p LEFT JOIN users u ON p.author_id = u.id ORDER BY p.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ข่าวสารสำนักทะเบียน</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../images/logo.png" alt="Logo" style="height: 50px; margin-bottom: 10px; border-radius: 8px;">
                <h2>ข่าวสารสำนักทะเบียน</h2>
                <p>ระบบจัดการหลังบ้าน</p>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="posts.php"><i class="fas fa-newspaper"></i> บทความ</a>
                <a href="categories.php"><i class="fas fa-tags"></i> หมวดหมู่</a>
                <?php if (isAdmin()): ?>
                <a href="users.php"><i class="fas fa-users"></i> ผู้ใช้งาน</a>
                <?php endif; ?>
                <a href="../index.php" target="_blank"><i class="fas fa-eye"></i> ดูเว็บไซต์</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
            </nav>
        </aside>
        
        <main class="main-admin">
            <header class="admin-header">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <div>
                    <span style="color: var(--primary);">
                        <i class="fas fa-user-circle"></i> <?php echo $_SESSION['display_name']; ?>
                    </span>
                </div>
            </header>
            
            <div class="admin-content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fas fa-newspaper"></i></div>
                        <div class="stat-info">
                            <h4>บทความทั้งหมด</h4>
                            <div class="number"><?php echo $stats['posts']; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-info">
                            <h4>เผยแพร่แล้ว</h4>
                            <div class="number"><?php echo $stats['published']; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="fas fa-edit"></i></div>
                        <div class="stat-info">
                            <h4>ฉบับร่าง</h4>
                            <div class="number"><?php echo $stats['draft']; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red"><i class="fas fa-eye"></i></div>
                        <div class="stat-info">
                            <h4>ยอดเข้าชม</h4>
                            <div class="number"><?php echo number_format($stats['views']); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="admin-card">
                    <h3><i class="fas fa-clock"></i> บทความล่าสุด</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>หัวข้อ</th>
                                    <th>ผู้เขียน</th>
                                    <th>สถานะ</th>
                                    <th>วันที่</th>
                                    <th>จำนวนเข้าชม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($post = $recent_posts->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $post['title']; ?></td>
                                    <td><?php echo $post['display_name'] ?? 'ไม่ระบุ'; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $post['status']; ?>">
                                            <?php echo $post['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                                    <td><?php echo $post['view_count']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="admin-card">
                        <h3><i class="fas fa-tags"></i> หมวดหมู่</h3>
                        <p style="font-size: 2rem; color: var(--primary);"><?php echo $stats['categories']; ?></p>
                        <a href="categories.php" class="btn btn-sm btn-primary" style="margin-top: 10px;">จัดการหมวดหมู่</a>
                    </div>
                    <div class="admin-card">
                        <h3><i class="fas fa-users"></i> ผู้ใช้งาน</h3>
                        <p style="font-size: 2rem; color: var(--primary);"><?php echo $stats['users']; ?></p>
                        <?php if (isAdmin()): ?>
                        <a href="users.php" class="btn btn-sm btn-primary" style="margin-top: 10px;">จัดการผู้ใช้</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
