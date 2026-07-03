<?php
// Bắt đầu session ở đầu file header để trang nào nhúng vào cũng dùng được session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Tính số sản phẩm trong giỏ hàng
$cart_count = 0;
if (isset($_SESSION['giohang'])) {
    foreach ($_SESSION['giohang'] as $item) {
        $cart_count += $item['soluong'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PETS CARE - Cửa hàng đồ thú cưng chó mèo</title>
    <meta name="description" content="Pets Care - Cửa hàng đồ cho thú cưng chó mèo. Đồ ăn, đồ chơi, phụ kiện và dịch vụ spa grooming chuyên nghiệp.">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-petshop">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="logo.jpg" alt="Pets Care Logo">
            <div class="brand-text">PETS <span>CARE</span></div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" style="border-color: rgba(255,255,255,0.3);">
            <i class="bi bi-list" style="color: white; font-size: 1.5rem;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sanpham.php"><i class="bi bi-grid"></i> Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dichvu.php"><i class="bi bi-scissors"></i> Dịch vụ</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item cart-badge">
                    <a class="nav-link" href="giohang.php">
                        <i class="bi bi-cart3" style="font-size: 1.2rem;"></i> Giỏ hàng
                        <?php if ($cart_count > 0): ?>
                            <span class="badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <?php if (isset($_SESSION['id'])): ?>
                    <!-- Nếu đã đăng nhập -->
                    <li class="nav-item">
                        <span class="nav-link user-greeting"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                    <?php if (isset($_SESSION['vaitro']) && $_SESSION['vaitro'] == 1): ?>
                        <li class="nav-item">
                            <a class="nav-link btn-admin-link" href="admin/index.php"><i class="bi bi-gear"></i> Admin</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="donhang.php"><i class="bi bi-receipt"></i> Đơn hàng</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link btn-login" href="dangxuat.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
                    </li>
                <?php else: ?>
                    <!-- Nếu chưa đăng nhập -->
                    <li class="nav-item">
                        <a class="nav-link btn-login" href="dangnhap.php"><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-register" href="dangki.php"><i class="bi bi-person-plus"></i> Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>