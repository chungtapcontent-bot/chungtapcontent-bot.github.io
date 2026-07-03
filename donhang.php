<?php
include 'includes/header.php';
require_once 'includes/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id'])) {
    header("Location: dangnhap.php");
    exit();
}

$taikhoan_id = $_SESSION['id'];

// Lấy danh sách đơn hàng của người dùng
$sql = "SELECT dh.*, sp.tensanpham, sp.hinhanh, sp.giaban 
        FROM donhang dh
        JOIN sanpham sp ON dh.sanpham_id = sp.id
        WHERE dh.taikhoan_id = ?
        ORDER BY dh.ngaydat DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $taikhoan_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Hàm hiển thị trạng thái
function getTrangThai($tt) {
    switch ($tt) {
        case 0: return ['text' => 'Chờ xử lý', 'class' => 'status-pending', 'icon' => 'bi-clock'];
        case 1: return ['text' => 'Đang giao', 'class' => 'status-processing', 'icon' => 'bi-truck'];
        case 2: return ['text' => 'Hoàn thành', 'class' => 'status-done', 'icon' => 'bi-check-circle'];
        default: return ['text' => 'Không rõ', 'class' => 'status-pending', 'icon' => 'bi-question-circle'];
    }
}
?>

<div class="container" style="padding: 40px 0;">
    <h2 style="font-weight: 800; color: var(--navy); margin-bottom: 25px;">
        <i class="bi bi-receipt" style="color: var(--orange);"></i> Đơn hàng của tôi
    </h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="cart-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th style="text-align: center;">SL</th>
                        <th style="text-align: right;">Thành tiền</th>
                        <th>Ngày đặt</th>
                        <th style="text-align: center;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($dh = $result->fetch_assoc()):
                        $tt = getTrangThai($dh['trangthai']);
                    ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--navy);">#<?php echo $dh['id']; ?></td>
                            <td>
                                <img src="Images/HinhAnh/<?php echo htmlspecialchars($dh['hinhanh']); ?>" class="cart-img" alt="">
                            </td>
                            <td>
                                <a href="chitiet_sanpham.php?id=<?php echo $dh['sanpham_id']; ?>" style="color: var(--navy); font-weight: 600;">
                                    <?php echo htmlspecialchars($dh['tensanpham']); ?>
                                </a>
                            </td>
                            <td style="text-align: center;"><?php echo $dh['soluongmua']; ?></td>
                            <td style="text-align: right; font-weight: 700; color: #e53935;">
                                <?php echo number_format($dh['giaban'] * $dh['soluongmua']); ?>đ
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i> <?php echo date('d/m/Y', strtotime($dh['ngaydat'])); ?><br>
                                    <i class="bi bi-clock"></i> <?php echo date('H:i', strtotime($dh['ngaydat'])); ?>
                                </small>
                            </td>
                            <td style="text-align: center;">
                                <span class="status-badge <?php echo $tt['class']; ?>">
                                    <i class="bi <?php echo $tt['icon']; ?>"></i> <?php echo $tt['text']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state" style="background: white; border-radius: var(--radius); padding: 80px 20px; box-shadow: var(--shadow);">
            <i class="bi bi-receipt-cutoff" style="font-size: 5rem; color: #e0e0e0;"></i>
            <h4 style="margin-top: 15px;">Bạn chưa có đơn hàng nào</h4>
            <p>Hãy bắt đầu mua sắm để thấy đơn hàng tại đây nhé!</p>
            <a href="sanpham.php" class="btn-hero" style="margin-top: 15px; display: inline-flex;">
                <i class="bi bi-bag-heart"></i> Mua sắm ngay
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
