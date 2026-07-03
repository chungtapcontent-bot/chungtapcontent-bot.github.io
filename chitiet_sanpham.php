<?php
include 'includes/header.php';
require_once 'includes/connect.php';

if (!isset($_GET['id'])) {
    header("Location: sanpham.php");
    exit();
}

$id = intval($_GET['id']);

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT sp.*, lt.ten_loai, dm.ten_danhmuc 
                         FROM sanpham sp
                         JOIN loai_thucung lt ON sp.loai_thucung_id = lt.id
                         JOIN danhmuc dm ON sp.danhmuc_id = dm.id
                         WHERE sp.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$sp = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sp) {
    echo '<div class="container" style="padding: 80px 0;"><div class="empty-state"><i class="bi bi-exclamation-circle"></i><h4>Sản phẩm không tồn tại</h4><p><a href="sanpham.php">Quay lại danh sách sản phẩm</a></p></div></div>';
    include 'includes/footer.php';
    exit();
}

// Lấy sản phẩm liên quan (cùng danh mục, cùng loài, trừ sản phẩm hiện tại)
$sql_related = "SELECT sp.*, lt.ten_loai, dm.ten_danhmuc 
                FROM sanpham sp
                JOIN loai_thucung lt ON sp.loai_thucung_id = lt.id
                JOIN danhmuc dm ON sp.danhmuc_id = dm.id
                WHERE sp.danhmuc_id = ? AND sp.loai_thucung_id = ? AND sp.id != ?
                ORDER BY RAND() LIMIT 4";
$stmt_rel = $conn->prepare($sql_related);
$stmt_rel->bind_param("iii", $sp['danhmuc_id'], $sp['loai_thucung_id'], $id);
$stmt_rel->execute();
$result_related = $stmt_rel->get_result();
$stmt_rel->close();
?>

<div class="container" style="padding: 40px 0;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" style="color: var(--orange);">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="sanpham.php" style="color: var(--orange);">Sản phẩm</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($sp['tensanpham']); ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Ảnh sản phẩm -->
        <div class="col-lg-5">
            <div class="detail-img-wrapper">
                <img src="Images/HinhAnh/<?php echo htmlspecialchars($sp['hinhanh']); ?>" alt="<?php echo htmlspecialchars($sp['tensanpham']); ?>">
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-lg-7">
            <div class="detail-info">
                <h1><?php echo htmlspecialchars($sp['tensanpham']); ?></h1>
                
                <div class="detail-price"><?php echo number_format($sp['giaban']); ?>đ</div>
                
                <div class="detail-meta">
                    <span class="<?php echo $sp['loai_thucung_id'] == 1 ? 'badge-cho' : 'badge-meo'; ?>" style="color: white;">
                        <?php echo $sp['loai_thucung_id'] == 1 ? '🐕 Chó' : '🐈 Mèo'; ?>
                    </span>
                    <span><i class="bi bi-tag"></i> <?php echo htmlspecialchars($sp['ten_danhmuc']); ?></span>
                    <span>
                        <i class="bi bi-box-seam"></i> 
                        <?php echo $sp['soluong'] > 0 ? 'Còn ' . $sp['soluong'] . ' sản phẩm' : '<span style="color:red;">Hết hàng</span>'; ?>
                    </span>
                </div>

                <div class="detail-desc">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle"></i> Mô tả sản phẩm:</h6>
                    <?php echo nl2br(htmlspecialchars($sp['mota'])); ?>
                </div>

                <!-- Form thêm giỏ hàng -->
                <form action="giohang.php" method="GET">
                    <input type="hidden" name="action" value="them">
                    <input type="hidden" name="id" value="<?php echo $sp['id']; ?>">
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <label class="fw-bold">Số lượng:</label>
                        <div class="quantity-input">
                            <button type="button" onclick="changeQty(-1)">−</button>
                            <input type="number" name="soluong" id="qty" value="1" min="1" max="<?php echo $sp['soluong']; ?>" readonly>
                            <button type="button" onclick="changeQty(1)">+</button>
                        </div>
                    </div>

                    <?php if ($sp['soluong'] > 0): ?>
                        <button type="submit" class="btn-buy-now">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn-buy-now" style="background: #ccc; cursor: not-allowed;" disabled>
                            <i class="bi bi-x-circle"></i> Tạm hết hàng
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
    <?php if ($result_related->num_rows > 0): ?>
        <div style="margin-top: 60px;">
            <div class="section-title">
                <h2>Sản phẩm liên quan</h2>
            </div>
            <div class="row g-4">
                <?php while($rel = $result_related->fetch_assoc()): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="product-card">
                            <div class="card-img-wrapper">
                                <span class="badge-loai <?php echo $rel['loai_thucung_id'] == 1 ? 'badge-cho' : 'badge-meo'; ?>">
                                    <?php echo $rel['loai_thucung_id'] == 1 ? '🐕 Chó' : '🐈 Mèo'; ?>
                                </span>
                                <a href="chitiet_sanpham.php?id=<?php echo $rel['id']; ?>">
                                    <img src="Images/HinhAnh/<?php echo htmlspecialchars($rel['hinhanh']); ?>" alt="<?php echo htmlspecialchars($rel['tensanpham']); ?>">
                                </a>
                            </div>
                            <div class="card-body">
                                <h5><a href="chitiet_sanpham.php?id=<?php echo $rel['id']; ?>" style="color: inherit;"><?php echo htmlspecialchars($rel['tensanpham']); ?></a></h5>
                                <div class="category"><i class="bi bi-tag"></i> <?php echo htmlspecialchars($rel['ten_danhmuc']); ?></div>
                                <div class="price"><?php echo number_format($rel['giaban']); ?>đ</div>
                            </div>
                            <div class="card-footer">
                                <a href="giohang.php?action=them&id=<?php echo $rel['id']; ?>" class="btn-add-cart">
                                    <i class="bi bi-cart-plus"></i> Thêm giỏ
                                </a>
                                <a href="chitiet_sanpham.php?id=<?php echo $rel['id']; ?>" class="btn-detail">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > parseInt(input.max)) val = parseInt(input.max);
    input.value = val;
}
</script>

<?php include 'includes/footer.php'; ?>
