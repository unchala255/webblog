<?php
require_once '../config.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$message = '';
$message_type = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        if ($id != $_SESSION['user_id']) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'ลบผู้ใช้สำเร็จ';
                $message_type = 'success';
            }
        } else {
            $message = 'ไม่สามารถลบตัวเองได้';
            $message_type = 'danger';
        }
    }
    
    if ($action == 'toggle' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->query("UPDATE users SET is_active = NOT is_active WHERE id = $id");
        $message = 'อัปเดตสถานะสำเร็จ';
        $message_type = 'success';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $display_name = $_POST['display_name'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $user_id = $_POST['user_id'] ?? 0;
    
    if ($user_id > 0) {
        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=?, display_name=?, role=? WHERE id=?");
            $stmt->bind_param("sssssi", $username, $email, $password, $display_name, $role, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, display_name=?, role=? WHERE id=?");
            $stmt->bind_param("ssssi", $username, $email, $display_name, $role, $user_id);
        }
        $message = 'อัปเดตผู้ใช้สำเร็จ';
    } else {
        if (empty($password)) {
            $message = 'กรุณากรอกรหัสผ่าน';
            $message_type = 'danger';
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, display_name, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $password, $display_name, $role);
            $message = 'เพิ่มผู้ใช้สำเร็จ';
        }
    }
    
    if (!isset($message_type) || $message_type != 'danger') {
        if ($stmt->execute()) {
            $message_type = 'success';
        } else {
            $message = 'เกิดข้อผิดพลาด: ' . $conn->error;
            $message_type = 'danger';
        }
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM users WHERE id = $edit_id");
    $edit_user = $edit_result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้ - ข่าวสารสำนักทะเบียน</title>
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
                <a href="categories.php"><i class="fas fa-tags"></i> หมวดหมู่</a>
                <a href="users.php" class="active"><i class="fas fa-users"></i> ผู้ใช้งาน</a>
                <a href="../index.php" target="_blank"><i class="fas fa-eye"></i> ดูเว็บไซต์</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
            </nav>
        </aside>
        
        <main class="main-admin">
            <header class="admin-header">
                <h1><i class="fas fa-users"></i> จัดการผู้ใช้งาน</h1>
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
                
                <?php if ($edit_user): ?>
                <div class="admin-card">
                    <h3><i class="fas fa-edit"></i> แก้ไขผู้ใช้</h3>
                    <form method="POST" action="users.php">
                        <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label>ชื่อผู้ใช้</label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($edit_user['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>อีเมล</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($edit_user['email']); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>ชื่อที่แสดง</label>
                                <input type="text" name="display_name" value="<?php echo htmlspecialchars($edit_user['display_name']); ?>">
                            </div>
                            <div class="form-group">
                                <label>สิทธิ์</label>
                                <select name="role">
                                    <option value="admin" <?php echo $edit_user['role'] == 'admin' ? 'selected' : ''; ?>>ผู้ดูแลระบบ</option>
                                    <option value="author" <?php echo $edit_user['role'] == 'author' ? 'selected' : ''; ?>>นักเขียน</option>
                                    <option value="user" <?php echo $edit_user['role'] == 'user' ? 'selected' : ''; ?>>ผู้ใช้ทั่วไป</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>รหัสผ่าน (ว่างไว้ถ้าไม่ต้องการเปลี่ยน)</label>
                            <input type="password" name="password" placeholder="กรอกรหัสผ่านใหม่">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> บันทึก</button>
                            <a href="users.php" class="btn btn-secondary"><i class="fas fa-times"></i> ยกเลิก</a>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <div class="admin-card">
                    <h3><i class="fas fa-plus-circle"></i> เพิ่มผู้ใช้ใหม่</h3>
                    <form method="POST" action="users.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label>ชื่อผู้ใช้</label>
                                <input type="text" name="username" required placeholder="username">
                            </div>
                            <div class="form-group">
                                <label>อีเมล</label>
                                <input type="email" name="email" required placeholder="email@example.com">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>ชื่อที่แสดง</label>
                                <input type="text" name="display_name" placeholder="ชื่อ-นามสกุล">
                            </div>
                            <div class="form-group">
                                <label>สิทธิ์</label>
                                <select name="role">
                                    <option value="user">ผู้ใช้ทั่วไป</option>
                                    <option value="author">นักเขียน</option>
                                    <option value="admin">ผู้ดูแลระบบ</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>รหัสผ่าน</label>
                            <input type="password" name="password" required placeholder="รหัสผ่าน">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> เพิ่มผู้ใช้</button>
                    </form>
                </div>
                <?php endif; ?>
                
                <div class="admin-card">
                    <h3><i class="fas fa-list"></i> รายการผู้ใช้</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ชื่อผู้ใช้</th>
                                    <th>ชื่อที่แสดง</th>
                                    <th>อีเมล</th>
                                    <th>สิทธิ์</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['display_name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td>
                                        <?php 
                                        $role_badges = [
                                            'admin' => 'danger',
                                            'author' => 'warning',
                                            'user' => 'info'
                                        ];
                                        $badge = $role_badges[$user['role']] ?? 'info';
                                        ?>
                                        <span class="status-badge <?php echo $badge; ?>"><?php echo $user['role']; ?></span>
                                    </td>
                                    <td>
                                        <a href="?action=toggle&id=<?php echo $user['id']; ?>" 
                                           style="color: <?php echo $user['is_active'] ? 'green' : 'red'; ?>;">
                                            <i class="fas fa-<?php echo $user['is_active'] ? 'check-circle' : '-times-circle'; ?>"></i>
                                            <?php echo $user['is_active'] ? 'ใช้งาน' : 'ปิด'; ?>
                                        </a>
                                    </td>
                                    <td class="table-actions">
                                        <a href="?edit=<?php echo $user['id']; ?>" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="?action=delete&id=<?php echo $user['id']; ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('ต้องการลบผู้ใช้นี้หรือไม่?');">
                                            <i class="fas fa-trash"></i> ลบ
                                        </a>
                                        <?php endif; ?>
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
