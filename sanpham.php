<?php
include 'includes/header.php';
require_once 'includes/connect.php';

// Lấy bộ lọc
$loai_filter = isset($_GET['loai']) ? intval($_GET['loai']) : 0;
$danhmuc_filter = isset($_GET['danhmuc']) ? intval($_GET['danhmuc']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Xây dựng câu query
$where = [];
$params = [];
$types = '';

if ($loai_filter > 0) {
    $where[] = "sp.loai_thucung_id = ?";
    $params[] = $loai_filter;
    $types .= 'i';
}
if ($danhmuc_filter > 0) {
    $where[] = "sp.danhmuc_id = ?";
    $params[] = $danhmuc_filter;
    $types .= 'i';
}
if (!empty($search)) {
    $where[] = "sp.tensanpham LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Đếm tổng sản phẩm
$count_sql = "SELECT COUNT(*) as total FROM sanpham sp $where_clause";
if (!empty($params)) {
    $stmt_count = $conn->prepare($count_sql);
    $stmt_count->bind_param($types, ...$params);
    $stmt_count->execute();
    $total = $stmt_count->get_result()->fetch_assoc()['total'];
    $stmt_count->close();
} else {
    $total = $conn->query($count_sql)->fetch_assoc()['total'];
}
$total_pages = ceil($total / $per_page);

// Lấy sản phẩm
$sql = "SELECT sp.*, lt.ten_loai, dm.ten_danhmuc 
        FROM sanpham sp
        JOIN loai_thucung lt ON sp.loai_thucung_id = lt.id
        JOIN danhmuc dm ON sp.danhmuc_id = dm.id
        $where_clause
        ORDER BY sp.id DESC
        LIMIT $per_page OFFSET $offset";

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

// Lấy danh mục và loài thú cưng cho bộ lọc
$loai_result = $conn->query("SELECT * FROM loai_thucung");
$danhmuc_result = $conn->query("SELECT * FROM danhmuc");
?>

<!-- Page Banner -->
<div class="page-banner">
    <img src="Images/banner_hero.png" alt="Sản phẩm" class="banner-bg">
    <div class="banner-content">
        <h1><i class="bi bi-grid"></i> Sản phẩm cho thú cưng</h1>
        <p>Đồ ăn, đồ chơi, phụ kiện chất lượng cao cho chó và mèo</p>
    </div>
</div>

<div class="container" style="padding: 40px 0;">
    <!-- Bộ lọc -->
    <div class="filter-bar">
        <form action="" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Loài thú cưng</label>
                <select name="loai" class="form-select">
                    <option value="0">-- Tất cả --</option>
                    <?php while($l = $loai_result->fetch_assoc()): ?>
                        <option value="<?php echo $l['id']; ?>" <?php if($loai_filter == $l['id']) echo 'selected'; ?>>
                            <?php echo $l['id'] == 1 ? '🐕' : '🐈'; ?> <?php echo htmlspecialchars($l['ten_loai']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Danh mục</label>
                <select name="danhmuc" class="form-select">
                    <option value="0">-- Tất cả --</option>
                    <?php while($d = $danhmuc_result->fetch_assoc()): ?>
                        <option value="<?php echo $d['id']; ?>" <?php if($danhmuc_filter == $d['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($d['ten_danhmuc']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Nhập tên sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-filter w-100"><i class="bi bi-funnel"></i> Lọc</button>
            </div>
        </form>
    </div>

    <!-- Kết quả -->
    <p class="text-muted mb-3">Tìm thấy <strong><?php echo $total; ?></strong> sản phẩm</p>

    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($sp = $result->fetch_assoc()): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="card-img-wrapper">
                            <span class="badge-loai <?php echo $sp['loai_thucung_id'] == 1 ? 'badge-cho' : 'badge-meo'; ?>">
                                <?php echo $sp['loai_thucung_id'] == 1 ? '🐕 Chó' : '🐈 Mèo'; ?>
                            </span>
                            <a href="chitiet_sanpham.php?id=<?php echo $sp['id']; ?>">
                                <img src="Images/HinhAnh/<?php echo htmlspecialchars($sp['hinhanh']); ?>" alt="<?php echo htmlspecialchars($sp['tensanpham']); ?>">
                            </a>
                        </div>
                        <div class="card-body">
                            <h5><a href="chitiet_sanpham.php?id=<?php echo $sp['id']; ?>" style="color: inherit;"><?php echo htmlspecialchars($sp['tensanpham']); ?></a></h5>
                            <div class="category"><i class="bi bi-tag"></i> <?php echo htmlspecialchars($sp['ten_danhmuc']); ?></div>
                            <div class="price"><?php echo number_format($sp['giaban']); ?>đ</div>
                        </div>
                        <div class="card-footer">
                            <a href="giohang.php?action=them&id=<?php echo $sp['id']; ?>" class="btn-add-cart">
                                <i class="bi bi-cart-plus"></i> Thêm giỏ
                            </a>
                            <a href="chitiet_sanpham.php?id=<?php echo $sp['id']; ?>" class="btn-detail">
                                <i class="bi bi-eye"></i> Xem
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-search"></i>
                    <h4>Không tìm thấy sản phẩm nào</h4>
                    <p>Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Phân trang -->
    <?php if ($total_pages > 1): ?>
        <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?loai=<?php echo $loai_filter; ?>&danhmuc=<?php echo $danhmuc_filter; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?loai=<?php echo $loai_filter; ?>&danhmuc=<?php echo $danhmuc_filter; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?loai=<?php echo $loai_filter; ?>&danhmuc=<?php echo $danhmuc_filter; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
