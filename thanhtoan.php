<?php
include 'includes/header.php';
require_once 'includes/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id'])) {
    header("Location: dangnhap.php");
    exit();
}

// Kiểm tra giỏ hàng
if (!isset($_SESSION['giohang']) || empty($_SESSION['giohang'])) {
    header("Location: giohang.php");
    exit();
}

$error = '';
$success = false;
$ma_donhang = '';

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dathangg'])) {
    $taikhoan_id = $_SESSION['id'];
    $has_error = false;

    // Lưu từng sản phẩm vào bảng donhang
    foreach ($_SESSION['giohang'] as $item) {
        $stmt = $conn->prepare("INSERT INTO donhang (taikhoan_id, sanpham_id, soluongmua, trangthai) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("iii", $taikhoan_id, $item['id'], $item['soluong']);

        if (!$stmt->execute()) {
            $has_error = true;
            $error = "Lỗi khi lưu đơn hàng, vui lòng thử lại!";
            break;
        }

        // Trừ số lượng kho
        $stmt_update = $conn->prepare("UPDATE sanpham SET soluong = soluong - ? WHERE id = ? AND soluong >= ?");
        $stmt_update->bind_param("iii", $item['soluong'], $item['id'], $item['soluong']);
        $stmt_update->execute();
        $stmt_update->close();

        $stmt->close();
    }

    if (!$has_error) {
        $success = true;
        // Lưu thông tin hoá đơn trước khi xóa giỏ hàng
        $_SESSION['hoadon_temp'] = $_SESSION['giohang'];
        $_SESSION['hoadon_ngay'] = date('d/m/Y H:i:s');
        // Xóa giỏ hàng
        $_SESSION['giohang'] = [];
    }
}

// Nếu đặt hàng thành công → hiển thị hoá đơn
if ($success && isset($_SESSION['hoadon_temp'])):
    $hoadon = $_SESSION['hoadon_temp'];
    $ngay = $_SESSION['hoadon_ngay'];
    $tong = 0;
    foreach ($hoadon as $item) {
        $tong += $item['giaban'] * $item['soluong'];
    }
    // Xóa dữ liệu tạm
    unset($_SESSION['hoadon_temp']);
    unset($_SESSION['hoadon_ngay']);
?>

<div class="container" style="padding: 40px 0;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Hoá đơn -->
            <div style="background: white; border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden;">
                
                <!-- Header hoá đơn -->
                <div style="background: linear-gradient(135deg, var(--navy), var(--navy-dark)); color: white; padding: 30px; text-align: center;">
                    <i class="bi bi-check-circle" style="font-size: 3rem; color: #4caf50;"></i>
                    <h2 style="font-weight: 800; margin-top: 10px;">ĐẶT HÀNG THÀNH CÔNG!</h2>
                    <p style="opacity: 0.8; margin: 0;">Cảm ơn bạn đã tin tưởng Pets Care</p>
                </div>

                <!-- Thông tin hoá đơn -->
                <div style="padding: 30px;">
                    <div class="d-flex justify-content-between align-items-start mb-4" style="border-bottom: 2px dashed #e0e0e0; padding-bottom: 20px;">
                        <div>
                            <h4 style="font-weight: 800; color: var(--navy); margin-bottom: 5px;">
                                <img src="logo.jpg" style="height: 40px; border-radius: 50%; margin-right: 8px;">
                                PETS CARE
                            </h4>
                            <small class="text-muted">Your Trusted Pet Partner</small>
                        </div>
                        <div class="text-end">
                            <h5 style="color: var(--orange); font-weight: 700;">HÓA ĐƠN</h5>
                            <small class="text-muted"><i class="bi bi-calendar3"></i> <?php echo $ngay; ?></small><br>
                            <small class="text-muted"><i class="bi bi-person"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></small>
                        </div>
                    </div>

                    <!-- Bảng sản phẩm -->
                    <table class="table" style="margin-bottom: 0;">
                        <thead>
                            <tr style="background: var(--bg-light);">
                                <th style="font-weight: 700; color: var(--navy); padding: 12px;">STT</th>
                                <th style="font-weight: 700; color: var(--navy); padding: 12px;">Sản phẩm</th>
                                <th style="font-weight: 700; color: var(--navy); padding: 12px; text-align: center;">SL</th>
                                <th style="font-weight: 700; color: var(--navy); padding: 12px; text-align: right;">Đơn giá</th>
                                <th style="font-weight: 700; color: var(--navy); padding: 12px; text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $stt = 1; foreach ($hoadon as $item): ?>
                                <tr>
                                    <td style="padding: 12px;"><?php echo $stt++; ?></td>
                                    <td style="padding: 12px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="Images/HinhAnh/<?php echo htmlspecialchars($item['hinhanh']); ?>" 
                                                 style="width: 40px; height: 40px; object-fit: contain; border-radius: 6px; background: var(--cream); padding: 3px;">
                                            <span style="font-weight: 600;"><?php echo htmlspecialchars($item['tensanpham']); ?></span>
                                        </div>
                                    </td>
                                    <td style="padding: 12px; text-align: center;"><?php echo $item['soluong']; ?></td>
                                    <td style="padding: 12px; text-align: right;"><?php echo number_format($item['giaban']); ?>đ</td>
                                    <td style="padding: 12px; text-align: right; font-weight: 600;"><?php echo number_format($item['giaban'] * $item['soluong']); ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Tổng cộng -->
                    <div style="background: var(--cream); padding: 20px; border-radius: 8px; margin-top: 20px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-bold"><?php echo number_format($tong); ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="fw-bold text-success">Miễn phí</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span style="font-size: 1.2rem; font-weight: 800; color: var(--navy);">TỔNG THANH TOÁN:</span>
                            <span style="font-size: 1.4rem; font-weight: 800; color: #e53935;"><?php echo number_format($tong); ?>đ</span>
                        </div>
                    </div>

                    <!-- Thanh toán khi nhận hàng -->
                    <div class="text-center mt-3" style="padding: 15px; background: #e8f5e9; border-radius: 8px;">
                        <i class="bi bi-cash-coin" style="color: #2e7d32; font-size: 1.3rem;"></i>
                        <span style="color: #2e7d32; font-weight: 600;"> Thanh toán khi nhận hàng (COD)</span>
                    </div>

                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="donhang.php" class="btn-hero" style="background: var(--navy); padding: 12px 25px; font-size: 0.9rem;">
                            <i class="bi bi-receipt"></i> Xem đơn hàng
                        </a>
                        <a href="sanpham.php" class="btn-hero" style="padding: 12px 25px; font-size: 0.9rem;">
                            <i class="bi bi-bag-heart"></i> Tiếp tục mua sắm
                        </a>
                    </div>

                    <!-- Footer hoá đơn -->
                    <div class="text-center mt-4" style="padding-top: 20px; border-top: 2px dashed #e0e0e0;">
                        <small class="text-muted">
                            <i class="bi bi-telephone"></i> Hotline: 0909 123 456 | 
                            <i class="bi bi-envelope"></i> info@petscare.vn<br>
                            <i class="bi bi-geo-alt"></i> 123 Đường ABC, Quận 1, TP.HCM<br>
                            <strong>Cảm ơn quý khách đã mua hàng tại PETS CARE! 🐾</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Form xác nhận thanh toán -->
<?php
// Tính tổng
$tong_tien = 0;
$tong_sp = 0;
foreach ($_SESSION['giohang'] as $item) {
    $tong_tien += $item['giaban'] * $item['soluong'];
    $tong_sp += $item['soluong'];
}
?>

<div class="container" style="padding: 40px 0;">
    <h2 style="font-weight: 800; color: var(--navy); margin-bottom: 25px;">
        <i class="bi bi-credit-card" style="color: var(--orange);"></i> Xác nhận thanh toán
    </h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Danh sách sản phẩm -->
        <div class="col-lg-7">
            <div class="cart-table">
                <div style="padding: 15px 20px; background: var(--cream); font-weight: 700; color: var(--navy);">
                    <i class="bi bi-bag-check"></i> Sản phẩm trong đơn hàng (<?php echo $tong_sp; ?> sản phẩm)
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">SL</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['giohang'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="Images/HinhAnh/<?php echo htmlspecialchars($item['hinhanh']); ?>" class="cart-img" alt="">
                                        <span style="font-weight: 600;"><?php echo htmlspecialchars($item['tensanpham']); ?></span>
                                    </div>
                                </td>
                                <td style="text-align: center;"><?php echo $item['soluong']; ?></td>
                                <td style="text-align: right; font-weight: 700; color: #e53935;">
                                    <?php echo number_format($item['giaban'] * $item['soluong']); ?>đ
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <a href="giohang.php" class="d-inline-block mt-2" style="color: var(--orange); font-weight: 600;">
                <i class="bi bi-arrow-left"></i> Quay lại giỏ hàng
            </a>
        </div>

        <!-- Tổng kết & nút đặt hàng -->
        <div class="col-lg-5">
            <div class="cart-summary">
                <h4><i class="bi bi-receipt"></i> Thông tin thanh toán</h4>

                <div class="mb-3" style="background: var(--bg-light); padding: 15px; border-radius: 8px;">
                    <small class="text-muted d-block mb-1">Khách hàng:</small>
                    <span class="fw-bold"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>

                <div class="mb-3" style="background: #e8f5e9; padding: 15px; border-radius: 8px;">
                    <small class="text-muted d-block mb-1">Phương thức thanh toán:</small>
                    <span class="fw-bold" style="color: #2e7d32;">
                        <i class="bi bi-cash-coin"></i> Thanh toán khi nhận hàng (COD)
                    </span>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tạm tính (<?php echo $tong_sp; ?> sp):</span>
                    <span class="fw-bold"><?php echo number_format($tong_tien); ?>đ</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phí vận chuyển:</span>
                    <span class="fw-bold text-success">Miễn phí</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span style="font-size: 1.1rem; font-weight: 800;">Tổng cộng:</span>
                    <span class="total-price"><?php echo number_format($tong_tien); ?>đ</span>
                </div>

                <form action="thanhtoan.php" method="POST">
                    <button type="submit" name="dathangg" value="1" class="btn-checkout d-block text-center" onclick="return confirm('Xác nhận đặt hàng?')">
                        <i class="bi bi-bag-check"></i> Xác nhận đặt hàng
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
