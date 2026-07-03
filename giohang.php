<?php
include 'includes/header.php';
require_once 'includes/connect.php';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['giohang'])) {
    $_SESSION['giohang'] = [];
}

// === XỬ LÝ CÁC HÀNH ĐỘNG ===

// THÊM sản phẩm vào giỏ
if (isset($_GET['action']) && $_GET['action'] == 'them' && isset($_GET['id'])) {
    $sp_id = intval($_GET['id']);
    $soluong_them = isset($_GET['soluong']) ? max(1, intval($_GET['soluong'])) : 1;

    // Lấy thông tin sản phẩm từ DB
    $stmt = $conn->prepare("SELECT * FROM sanpham WHERE id = ?");
    $stmt->bind_param("i", $sp_id);
    $stmt->execute();
    $sp = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($sp) {
        // Nếu sản phẩm đã có trong giỏ → tăng số lượng
        if (isset($_SESSION['giohang'][$sp_id])) {
            $_SESSION['giohang'][$sp_id]['soluong'] += $soluong_them;
        } else {
            // Thêm mới vào giỏ
            $_SESSION['giohang'][$sp_id] = [
                'id' => $sp['id'],
                'tensanpham' => $sp['tensanpham'],
                'giaban' => $sp['giaban'],
                'hinhanh' => $sp['hinhanh'],
                'soluong' => $soluong_them,
                'soluong_kho' => $sp['soluong']
            ];
        }
    }
    header("Location: giohang.php");
    exit();
}

// XÓA sản phẩm khỏi giỏ
if (isset($_GET['action']) && $_GET['action'] == 'xoa' && isset($_GET['id'])) {
    $sp_id = intval($_GET['id']);
    unset($_SESSION['giohang'][$sp_id]);
    header("Location: giohang.php");
    exit();
}

// CẬP NHẬT số lượng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['capnhat'])) {
    if (isset($_POST['soluong']) && is_array($_POST['soluong'])) {
        foreach ($_POST['soluong'] as $sp_id => $sl) {
            $sp_id = intval($sp_id);
            $sl = max(1, intval($sl));
            if (isset($_SESSION['giohang'][$sp_id])) {
                $_SESSION['giohang'][$sp_id]['soluong'] = $sl;
            }
        }
    }
    header("Location: giohang.php");
    exit();
}

// XÓA TOÀN BỘ giỏ hàng
if (isset($_GET['action']) && $_GET['action'] == 'xoahet') {
    $_SESSION['giohang'] = [];
    header("Location: giohang.php");
    exit();
}

// Tính tổng tiền
$tong_tien = 0;
$tong_sp = 0;
foreach ($_SESSION['giohang'] as $item) {
    $tong_tien += $item['giaban'] * $item['soluong'];
    $tong_sp += $item['soluong'];
}
?>

<div class="container" style="padding: 40px 0;">
    <h2 style="font-weight: 800; color: var(--navy); margin-bottom: 25px;">
        <i class="bi bi-cart3" style="color: var(--orange);"></i> Giỏ hàng của bạn
    </h2>

    <?php if (empty($_SESSION['giohang'])): ?>
        <!-- Giỏ hàng trống -->
        <div class="empty-state" style="background: white; border-radius: var(--radius); padding: 80px 20px; box-shadow: var(--shadow);">
            <i class="bi bi-cart-x" style="font-size: 5rem; color: #e0e0e0;"></i>
            <h4 style="margin-top: 15px;">Giỏ hàng đang trống</h4>
            <p>Hãy thêm sản phẩm yêu thích vào giỏ hàng nhé!</p>
            <a href="sanpham.php" class="btn-hero" style="margin-top: 15px; display: inline-flex;">
                 Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Bảng giỏ hàng -->
            <div class="col-lg-8">
                <form action="giohang.php" method="POST">
                    <div class="cart-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Ảnh</th>
                                    <th>Sản phẩm</th>
                                    <th style="width: 110px;">Đơn giá</th>
                                    <th style="width: 120px;">Số lượng</th>
                                    <th style="width: 120px;">Thành tiền</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['giohang'] as $sp_id => $item): ?>
                                    <tr>
                                        <td>
                                            <img src="Images/HinhAnh/<?php echo htmlspecialchars($item['hinhanh']); ?>" class="cart-img" alt="">
                                        </td>
                                        <td>
                                            <a href="chitiet_sanpham.php?id=<?php echo $item['id']; ?>" style="color: var(--navy); font-weight: 600;">
                                                <?php echo htmlspecialchars($item['tensanpham']); ?>
                                            </a>
                                        </td>
                                        <td style="color: #e53935; font-weight: 600;">
                                            <?php echo number_format($item['giaban']); ?>đ
                                        </td>
                                        <td>
                                            <input type="number" name="soluong[<?php echo $sp_id; ?>]" 
                                                   value="<?php echo $item['soluong']; ?>" 
                                                   min="1" max="99"
                                                   class="form-control form-control-sm" 
                                                   style="width: 70px; text-align: center; border-radius: 6px;">
                                        </td>
                                        <td style="font-weight: 700; color: var(--navy);">
                                            <?php echo number_format($item['giaban'] * $item['soluong']); ?>đ
                                        </td>
                                        <td>
                                            <a href="giohang.php?action=xoa&id=<?php echo $sp_id; ?>" 
                                               class="text-danger" title="Xóa"
                                               onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')">
                                                <i class="bi bi-trash" style="font-size: 1.1rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="sanpham.php" style="color: var(--orange); font-weight: 600;">
                            <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                        <div class="d-flex gap-2">
                            <a href="giohang.php?action=xoahet" class="btn btn-outline-danger btn-sm" onclick="return confirm('Xóa toàn bộ giỏ hàng?')">
                                <i class="bi bi-trash"></i> Xóa hết
                            </a>
                            <button type="submit" name="capnhat" value="1" class="btn btn-sm" style="background: var(--navy); color: white;">
                                <i class="bi bi-arrow-clockwise"></i> Cập nhật
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tổng kết đơn hàng -->
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4><i class="bi bi-receipt"></i> Tổng đơn hàng</h4>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Số sản phẩm:</span>
                        <span class="fw-bold"><?php echo $tong_sp; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-bold"><?php echo number_format($tong_tien); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="fw-bold text-success">Miễn phí</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold" style="font-size: 1.1rem;">Tổng cộng:</span>
                        <span class="total-price"><?php echo number_format($tong_tien); ?>đ</span>
                    </div>

                    <?php if (isset($_SESSION['id'])): ?>
                        <a href="thanhtoan.php" class="btn-checkout d-block text-center">
                            <i class="bi bi-credit-card"></i> Tiến hành thanh toán
                        </a>
                    <?php else: ?>
                        <a href="dangnhap.php" class="btn-checkout d-block text-center" style="background: var(--navy);">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập để thanh toán
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
