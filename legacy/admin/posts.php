<?php
require_once '../config.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$message = '';
$message_type = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = 'ลบบทความสำเร็จ';
            $message_type = 'success';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $content = $_POST['content'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $status = $_POST['status'] ?? 'draft';
    $featured_image = $_POST['featured_image'] ?? '';
    $post_id = $_POST['post_id'] ?? 0;
    
    if (empty($slug)) {
        $slug = preg_replace('/[^a-zA-Z0-9ก-ฮ]/u', '-', $title);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
    }
    
    if ($post_id > 0) {
        $stmt = $conn->prepare("UPDATE posts SET title=?, slug=?, excerpt=?, content=?, category_id=?, status=?, featured_image=? WHERE id=?");
        $stmt->bind_param("ssssissi", $title, $slug, $excerpt, $content, $category_id, $status, $featured_image, $post_id);
        $message = 'อัปเดตบทความสำเร็จ';
    } else {
        $author_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO posts (title, slug, excerpt, content, author_id, category_id, status, featured_image, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssiiss", $title, $slug, $excerpt, $content, $author_id, $category_id, $status, $featured_image);
        $message = 'เพิ่มบทความสำเร็จ';
    }
    
    if ($stmt->execute()) {
        $message_type = 'success';
    } else {
        $message = 'เกิดข้อผิดพลาด: ' . $conn->error;
        $message_type = 'danger';
    }
}

$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$total = $conn->query("SELECT COUNT(*) as total FROM posts")->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

$posts = $conn->query("SELECT p.*, u.display_name, c.name as category_name 
                       FROM posts p 
                       LEFT JOIN users u ON p.author_id = u.id 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       ORDER BY p.created_at DESC 
                       LIMIT $limit OFFSET $offset");

$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$edit_post = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM posts WHERE id = $edit_id");
    $edit_post = $edit_result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการบทความ - ข่าวสารสำนักทะเบียน</title>
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
                <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="posts.php" class="active"><i class="fas fa-newspaper"></i> บทความ</a>
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
                <h1><i class="fas fa-newspaper"></i> จัดการบทความ</h1>
                <div>
                    <span style="color: var(--primary);">
                        <i class="fas fa-user-circle"></i> <?php echo $_SESSION['display_name']; ?>
                    </span>
                </div>
            </header>
            
            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : '-exclamation-circle'; ?>"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($edit_post): ?>
                <div class="admin-card">
                    <h3><i class="fas fa-edit"></i> แก้ไขบทความ</h3>
                    <form method="POST" action="posts.php">
                        <input type="hidden" name="post_id" value="<?php echo $edit_post['id']; ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label>หัวข้อ</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_post['title']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Slug</label>
                                <input type="text" name="slug" value="<?php echo htmlspecialchars($edit_post['slug']); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>ย่อเนื้อหา</label>
                            <textarea name="excerpt" rows="2"><?php echo htmlspecialchars($edit_post['excerpt']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>เนื้อหา</label>
                            <textarea name="content" rows="10" class="editor"><?php echo htmlspecialchars($edit_post['content']); ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>หมวดหมู่</label>
                                <select name="category_id">
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    <?php 
                                    $categories->data_seek(0);
                                    while ($cat = $categories->fetch_assoc()): ?>
                                        <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo $edit_post['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo $cat['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>สถานะ</label>
                                <select name="status">
                                    <option value="draft" <?php echo $edit_post['status'] == 'draft' ? 'selected' : ''; ?>>ฉบับร่าง</option>
                                    <option value="published" <?php echo $edit_post['status'] == 'published' ? 'selected' : ''; ?>>เผยแพร่</option>
                                    <option value="archived" <?php echo $edit_post['status'] == 'archived' ? 'selected' : ''; ?>>เก็บถาวร</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>รูปภาพ (URL)</label>
                            <input type="text" name="featured_image" value="<?php echo htmlspecialchars($edit_post['featured_image']); ?>" placeholder="https://...">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> บันทึก</button>
                            <a href="posts.php" class="btn btn-secondary"><i class="fas fa-times"></i> ยกเลิก</a>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <div class="admin-card">
                    <h3><i class="fas fa-plus-circle"></i> เพิ่มบทความใหม่</h3>
                    <form method="POST" action="posts.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label>หัวข้อ</label>
                                <input type="text" name="title" required placeholder="กรอกหัวข้อบทความ">
                            </div>
                            <div class="form-group">
                                <label>Slug</label>
                                <input type="text" name="slug" placeholder="url-friendly-slug">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>ย่อเนื้อหา</label>
                            <textarea name="excerpt" rows="2" placeholder="สรุปเนื้อหาสั้นๆ"></textarea>
                        </div>
                        <div class="form-group">
                            <label>เนื้อหา</label>
                            <textarea name="content" rows="10" class="editor" placeholder="เนื้อหาบทความ"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>หมวดหมู่</label>
                                <select name="category_id">
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    <?php 
                                    $categories->data_seek(0);
                                    while ($cat = $categories->fetch_assoc()): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>สถานะ</label>
                                <select name="status">
                                    <option value="draft">ฉบับร่าง</option>
                                    <option value="published">เผยแพร่</option>
                                    <option value="archived">เก็บถาวร</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>รูปภาพ (URL)</label>
                            <input type="text" name="featured_image" placeholder="https://example.com/image.jpg">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> เพิ่มบทความ</button>
                    </form>
                </div>
                <?php endif; ?>
                
                <div class="admin-card">
                    <h3><i class="fas fa-list"></i> รายการบทความ (<?php echo $total; ?>)</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>หัวข้อ</th>
                                    <th>หมวดหมู่</th>
                                    <th>ผู้เขียน</th>
                                    <th>สถานะ</th>
                                    <th>เข้าชม</th>
                                    <th>วันที่</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($post = $posts->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $post['id']; ?></td>
                                    <td><?php echo $post['title']; ?></td>
                                    <td><?php echo $post['category_name'] ?? '-'; ?></td>
                                    <td><?php echo $post['display_name'] ?? '-'; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $post['status']; ?>">
                                            <?php echo $post['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $post['view_count']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                                    <td class="table-actions">
                                        <a href="?edit=<?php echo $post['id']; ?>" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </a>
                                        <a href="?action=delete&id=<?php echo $post['id']; ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('ต้องการลบบทความนี้หรือไม่?');">
                                            <i class="fas fa-trash"></i> ลบ
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
