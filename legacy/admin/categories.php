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
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = 'ลบหมวดหมู่สำเร็จ';
            $message_type = 'success';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $description = $_POST['description'] ?? '';
    $category_id = $_POST['category_id'] ?? 0;
    
    if (empty($slug)) {
        $slug = preg_replace('/[^a-zA-Z0-9ก-ฮ]/u', '-', $name);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
    }
    
    if ($category_id > 0) {
        $stmt = $conn->prepare("UPDATE categories SET name=?, slug=?, description=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $slug, $description, $category_id);
        $message = 'อัปเดตหมวดหมู่สำเร็จ';
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $slug, $description);
        $message = 'เพิ่มหมวดหมู่สำเร็จ';
    }
    
    if ($stmt->execute()) {
        $message_type = 'success';
    } else {
        $message = 'เกิดข้อผิดพลาด: ' . $conn->error;
        $message_type = 'danger';
    }
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM categories WHERE id = $edit_id");
    $edit_category = $edit_result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมวดหมู่ - ข่าวสารสำนักทะเบียน</title>
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
                <a href="posts.php"><i class="fas fa-newspaper"></i> บทความ</a>
                <a href="categories.php" class="active"><i class="fas fa-tags"></i> หมวดหมู่</a>
                <?php if (isAdmin()): ?>
                <a href="users.php"><i class="fas fa-users"></i> ผู้ใช้งาน</a>
                <?php endif; ?>
                <a href="../index.php" target="_blank"><i class="fas fa-eye"></i> ดูเว็บไซต์</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
            </nav>
        </aside>
        
        <main class="main-admin">
            <header class="admin-header">
                <h1><i class="fas fa-tags"></i> จัดการหมวดหมู่</h1>
                <div>
                    <span style="color: var(--primary);">
                        <i class="fas fa-user-circle"></i> <?php echo $_SESSION['display_name']; ?>
                    </span>
                </div>
            </header>
            
            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($edit_category): ?>
                <div class="admin-card">
                    <h3><i class="fas fa-edit"></i> แก้ไขหมวดหมู่</h3>
                    <form method="POST" action="categories.php">
                        <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label>ชื่อหมวดหมู่</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($edit_category['name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Slug</label>
                                <input type="text" name="slug" value="<?php echo htmlspecialchars($edit_category['slug']); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>รายละเอียด</label>
                            <textarea name="description" rows="3"><?php echo htmlspecialchars($edit_category['description']); ?></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> บันทึก</button>
                            <a href="categories.php" class="btn btn-secondary"><i class="fas fa-times"></i> ยกเลิก</a>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <div class="admin-card">
                    <h3><i class="fas fa-plus-circle"></i> เพิ่มหมวดหมู่ใหม่</h3>
                    <form method="POST" action="categories.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label>ชื่อหมวดหมู่</label>
                                <input type="text" name="name" required placeholder="กรอกชื่อหมวดหมู่">
                            </div>
                            <div class="form-group">
                                <label>Slug</label>
                                <input type="text" name="slug" placeholder="url-friendly-slug">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>รายละเอียด</label>
                            <textarea name="description" rows="3" placeholder="รายละเอียดหมวดหมู่"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> เพิ่มหมวดหมู่</button>
                    </form>
                </div>
                <?php endif; ?>
                
                <div class="admin-card">
                    <h3><i class="fas fa-list"></i> รายการหมวดหมู่</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ชื่อหมวดหมู่</th>
                                    <th>Slug</th>
                                    <th>รายละเอียด</th>
                                    <th>วันที่สร้าง</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $cat['id']; ?></td>
                                    <td><?php echo $cat['name']; ?></td>
                                    <td><code><?php echo $cat['slug']; ?></code></td>
                                    <td><?php echo $cat['description'] ? substr($cat['description'], 0, 50) . '...' : '-'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($cat['created_at'])); ?></td>
                                    <td class="table-actions">
                                        <a href="?edit=<?php echo $cat['id']; ?>" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </a>
                                        <a href="?action=delete&id=<?php echo $cat['id']; ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('ต้องการลบหมวดหมู่นี้หรือไม่?');">
                                            <i class="fas fa-trash"></i> ลบ
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
