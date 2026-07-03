<?php
include 'includes/header.php';
require_once 'includes/connect.php';

// Lấy tất cả dịch vụ
$result_dv = $conn->query("SELECT * FROM dichvu ORDER BY id ASC");
?>

<!-- Page Banner -->
<div class="page-banner">
    <img src="Images/banner_dichvu.png" alt="Dịch vụ" class="banner-bg">
    <div class="banner-content">
        <h1><i class="bi bi-scissors"></i> Dịch vụ chăm sóc thú cưng</h1>
        <p>Spa, grooming, cắt tỉa lông và khách sạn thú cưng chuyên nghiệp</p>
    </div>
</div>

<div class="container" style="padding: 50px 0;">
    <div class="section-title">
        <h2>🐾 Các dịch vụ của chúng tôi</h2>
        <p>Đội ngũ chuyên gia giàu kinh nghiệm, trang thiết bị hiện đại, mang đến trải nghiệm tốt nhất cho thú cưng của bạn</p>
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
                            <?php if (isset($_SESSION['id'])): ?>
                                <a href="datlich.php?dichvu_id=<?php echo $dv['id']; ?>" class="btn-book">
                                    <i class="bi bi-calendar-check"></i> Đặt lịch ngay
                                </a>
                            <?php else: ?>
                                <a href="dangnhap.php" class="btn-book" style="background: var(--orange);">
                                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập để đặt lịch
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-scissors"></i>
                    <h4>Chưa có dịch vụ nào</h4>
                    <p>Vui lòng quay lại sau</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tại sao chọn chúng tôi -->
<section style="padding: 60px 0; background: var(--cream);">
    <div class="container">
        <div class="section-title">
            <h2>Tại sao chọn Pets Care?</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon" style="background: #e8f5e9; color: #43a047;">
                        <i class="bi bi-award"></i>
                    </div>
                    <h5>Chuyên gia hàng đầu</h5>
                    <p>Đội ngũ groomer giàu kinh nghiệm, được đào tạo bài bản</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon" style="background: #e3f2fd; color: #1e88e5;">
                        <i class="bi bi-droplet-half"></i>
                    </div>
                    <h5>Sản phẩm an toàn</h5>
                    <p>Sử dụng dầu gội, dưỡng chất nhập khẩu, an toàn tuyệt đối</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon" style="background: #fff3e0; color: #ff9800;">
                        <i class="bi bi-camera-video"></i>
                    </div>
                    <h5>Camera giám sát</h5>
                    <p>Theo dõi thú cưng real-time qua camera khi lưu trú</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box">
                    <div class="feature-icon" style="background: #fce4ec; color: #e91e63;">
                        <i class="bi bi-emoji-smile"></i>
                    </div>
                    <h5>Hài lòng 100%</h5>
                    <p>Cam kết hoàn tiền nếu bạn không hài lòng với dịch vụ</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
