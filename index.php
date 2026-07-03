<?php
include 'includes/header.php';
require_once 'includes/connect.php';

// Lấy 8 sản phẩm mới nhất
$sql_sp = "SELECT sp.*, lt.ten_loai, dm.ten_danhmuc 
           FROM sanpham sp
           JOIN loai_thucung lt ON sp.loai_thucung_id = lt.id
           JOIN danhmuc dm ON sp.danhmuc_id = dm.id
           ORDER BY sp.id DESC LIMIT 8";
$result_sp = $conn->query($sql_sp);

// Lấy 3 dịch vụ nổi bật
$sql_dv = "SELECT * FROM dichvu LIMIT 3";
$result_dv = $conn->query($sql_dv);
?>

<!-- Hero Section -->
<section class="hero-section">
    <img src="Images/banner_hero.png" alt="Pets Care Banner" class="hero-bg">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1>Chăm sóc <span>thú cưng</span> của bạn tốt nhất!</h1>
                    <p>Cung cấp đồ ăn, đồ chơi, phụ kiện cao cấp và dịch vụ spa grooming chuyên nghiệp cho chó & mèo.</p>
                    <a href="sanpham.php" class="btn-hero">
                        <i class="bi bi-bag-heart"></i> Mua sắm ngay
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-img">
                    <img src="logo.jpg" alt="Pets Care">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon" style="background: #fff3e0; color: #ff9800;">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h5>Giao hàng nhanh</h5>
                    <p>Giao hàng trong 2h nội thành TP.HCM</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon" style="background: #e3f2fd; color: #1e88e5;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5>Chính hãng 100%</h5>
                    <p>Cam kết sản phẩm chính hãng, nguồn gốc rõ ràng</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon" style="background: #fce4ec; color: #e91e63;">
                        <i class="bi bi-heart"></i>
                    </div>
                    <h5>Đa dạng sản phẩm</h5>
                    <p>Hơn 500+ sản phẩm cho chó và mèo</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon" style="background: #e8f5e9; color: #43a047;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h5>Tư vấn 24/7</h5>
                    <p>Đội ngũ chuyên gia sẵn sàng hỗ trợ bạn</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sản phẩm nổi bật -->
<section style="padding: 0 0 60px;">
    <div class="container">
        <div class="section-title">
            <h2>🐾 Sản phẩm nổi bật</h2>
            <p>Những sản phẩm mới nhất và được yêu thích nhất dành cho thú cưng của bạn</p>
        </div>
        <div class="row g-4">
            <?php if ($result_sp->num_rows > 0): ?>
                <?php while($sp = $result_sp->fetch_assoc()): ?>
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
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="sanpham.php" class="btn-hero" style="background: var(--pink);">
                <i class="bi bi-grid"></i> Xem tất cả sản phẩm
            </a>
        </div>
    </div>
</section>

<!-- Dịch vụ nổi bật -->
<section style="padding: 60px 0; background: var(--cream);">
    <div class="container">
        <div class="section-title">
            <h2>✂️ Dịch vụ chăm sóc</h2>
            <p>Dịch vụ spa, grooming, cắt tỉa lông và khách sạn thú cưng chuyên nghiệp</p>
        </div>
        <div class="row g-4">
            <?php if ($result_dv->num_rows > 0): ?>
                <?php while($dv = $result_dv->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="service-card">
                            <div class="service-img">
                                <img src="Images/DichVu/<?php echo htmlspecialchars($dv['hinhanh']); ?>" alt="<?php echo htmlspecialchars($dv['tendichvu']); ?>">
                            </div>
                            <div class="service-body">
                                <h5><?php echo htmlspecialchars($dv['tendichvu']); ?></h5>
                                <div class="service-price"><?php echo number_format($dv['gia']); ?>đ</div>
                                <div class="service-time"><i class="bi bi-clock"></i> <?php echo htmlspecialchars($dv['thoigian']); ?></div>
                                <p><?php echo htmlspecialchars($dv['mota']); ?></p>
                                <a href="datlich.php?dichvu_id=<?php echo $dv['id']; ?>" class="btn-book">
                                    <i class="bi bi-calendar-check"></i> Đặt lịch ngay
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="dichvu.php" class="btn-hero">
                <i class="bi bi-scissors"></i> Xem tất cả dịch vụ
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>