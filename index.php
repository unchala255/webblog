<?php
require_once 'config.php';

$page = $_GET['page'] ?? 1;
$category_id = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$limit = 9;
$offset = ($page - 1) * $limit;

$where = "WHERE p.status = 'published'";
$params = [];
$types = "";

if ($category_id) {
    $where .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

if ($search) {
    $where .= " AND (p.title LIKE ? OR p.excerpt LIKE ?)";
    $search_term = "%{$search}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

$count_sql = "SELECT COUNT(*) as total FROM posts p $where";
$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_posts = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $limit);

$sql = "SELECT p.*, u.display_name, c.name as category_name, c.slug as category_slug 
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where 
        ORDER BY p.published_at DESC 
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$posts = $stmt->get_result();

$cat_sql = "SELECT * FROM categories ORDER BY name";
$categories = $conn->query($cat_sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข่าวสารสำนักทะเบียน</title>
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
            <section class="hero">
                <h1><i class="fas fa-newspaper"></i> ข่าวสารสำนักทะเบียน</h1>
                <p>ติดตามข่าวสารและกิจกรรมล่าสุดของสำนักทะเบียน</p>
            </section>

            <section class="categories-section">
                <h2 class="section-title"><i class="fas fa-tags"></i> หมวดหมู่</h2>
                <div class="categories-grid">
                    <a href="index.php" class="category-tag <?php echo !$category_id ? 'active' : ''; ?>">
                        <i class="fas fa-globe"></i> ทั้งหมด
                    </a>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <a href="index.php?category=<?php echo $cat['id']; ?>" 
                           class="category-tag <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                            <?php echo $cat['name']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </section>

            <form method="GET" action="" class="search-box">
                <?php if ($category_id): ?>
                    <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                <?php endif; ?>
                <input type="text" name="search" placeholder="ค้นหาบทความ..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> ค้นหา</button>
            </form>

            <?php if ($search): ?>
                <div class="alert alert-info">
                    <i class="fas fa-search"></i> ผลการค้นหา: "<?php echo htmlspecialchars($search); ?>" (<?php echo $total_posts; ?> รายการ)
                </div>
            <?php endif; ?>

            <section class="posts-grid">
                <?php if ($posts->num_rows > 0): ?>
                    <?php while ($post = $posts->fetch_assoc()): ?>
                        <article class="post-card">
                            <div class="post-image">
                                <?php if ($post['featured_image']): ?>
                                    <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo $post['title']; ?>">
                                <?php else: ?>
                                    <i class="fas fa-newspaper"></i>
                                <?php endif; ?>
                                <span class="category-badge"><?php echo $post['category_name'] ?? 'ไม่มีหมวดหมู่'; ?></span>
                            </div>
                            <div class="post-content">
                                <h3 class="post-title">
                                    <a href="post.php?id=<?php echo $post['id']; ?>">
                                        <?php echo $post['title']; ?>
                                    </a>
                                </h3>
                                <p class="post-excerpt"><?php echo $post['excerpt']; ?></p>
                                <div class="post-meta">
                                    <div class="author">
                                        <i class="fas fa-user"></i> <?php echo $post['display_name'] ?? 'ไม่ระบุ'; ?>
                                    </div>
                                    <div class="date">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo date('d/m/Y', strtotime($post['published_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-folder-open"></i>
                        <h3>ไม่พบบทความ</h3>
                        <p>ลองค้นหาด้วยคำอื่นหรือเลือกหมวดหมู่อื่น</p>
                    </div>
                <?php endif; ?>
            </section>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="<?php echo $page == $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ข่าวสารสำนักทะเบียน | พัฒนาโดย สำนักทะเบียน</p>
        </div>
    </footer>
</body>
</html>