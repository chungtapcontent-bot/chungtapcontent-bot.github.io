<?php
include 'includes/header.php';
require_once 'includes/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id'])) {
    header("Location: dangnhap.php");
    exit();
}

// Lấy danh sách dịch vụ
$result_dv = $conn->query("SELECT * FROM dichvu ORDER BY id ASC");

// Dịch vụ đã chọn sẵn (nếu có)
$selected_dv = isset($_GET['dichvu_id']) ? intval($_GET['dichvu_id']) : 0;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dichvu_id = intval($_POST['dichvu_id']);
    $ngayhen = trim($_POST['ngayhen']);
    $giohen = trim($_POST['giohen']);
    $taikhoan_id = $_SESSION['id'];

    if ($dichvu_id <= 0 || empty($ngayhen) || empty($giohen)) {
        $error = "Vui lòng chọn đầy đủ dịch vụ, ngày và giờ hẹn!";
    } elseif (strtotime($ngayhen) < strtotime(date('Y-m-d'))) {
        $error = "Ngày hẹn không được ở trong quá khứ!";
    } else {
        $stmt = $conn->prepare("INSERT INTO datlich (taikhoan_id, dichvu_id, ngayhen, giohen, trangthai) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("iiss", $taikhoan_id, $dichvu_id, $ngayhen, $giohen);

        if ($stmt->execute()) {
            $success = "Đặt lịch thành công! Chúng tôi sẽ liên hệ xác nhận sớm nhất.";
        } else {
            $error = "Lỗi hệ thống, vui lòng thử lại!";
        }
        $stmt->close();
    }
}
?>

<div class="container" style="padding: 40px 0;">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="form-wrapper" style="margin: 20px auto;">
                <h2><i class="bi bi-calendar-check" style="color: var(--orange);"></i> Đặt lịch dịch vụ</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                        <br><a href="dichvu.php" class="fw-bold">Quay lại trang dịch vụ</a>
                    </div>
                <?php else: ?>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Chọn dịch vụ:</label>
                            <select name="dichvu_id" class="form-control" required>
                                <option value="">-- Chọn dịch vụ --</option>
                                <?php while($dv = $result_dv->fetch_assoc()): ?>
                                    <option value="<?php echo $dv['id']; ?>" <?php if($selected_dv == $dv['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($dv['tendichvu']); ?> - <?php echo number_format($dv['gia']); ?>đ (<?php echo htmlspecialchars($dv['thoigian']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày hẹn:</label>
                            <input type="date" name="ngayhen" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giờ hẹn:</label>
                            <input type="time" name="giohen" class="form-control" min="08:00" max="20:00" required>
                            <small class="text-muted">Giờ hoạt động: 8:00 - 20:00</small>
                        </div>
                        <button type="submit" class="btn-submit"><i class="bi bi-calendar-plus"></i> Xác nhận đặt lịch</button>
                    </form>
                <?php endif; ?>

                <p class="text-center mt-3">
                    <a href="dichvu.php" style="color: var(--orange);"><i class="bi bi-arrow-left"></i> Quay lại danh sách dịch vụ</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
