<?php
// 1. Kiểm tra quyền Admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] != 1) {
    header("Location: ../dangnhap.php");
    exit();
}

require_once '../includes/connect.php';

// 2. Xử lý chức năng XÓA SẢN PHẨM (Nếu có yêu cầu gửi tới)
if (isset($_GET['action']) && $_GET['action'] == 'xoa' && isset($_GET['id'])) {
    $id_xoa = intval($_GET['id']);
    
    // Xóa sản phẩm dựa trên ID
    $stmt_xoa = $conn->prepare("DELETE FROM sanpham WHERE id = ?");
    $stmt_xoa->bind_param("i", $id_xoa);
    $stmt_xoa->execute();
    $stmt_xoa->close();
    
    // Tải lại trang để cập nhật danh sách
    header("Location: index.php");
    exit();
}

// 3. Xử lý chức năng TÌM KIẾM
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// 4. Truy vấn danh sách sản phẩm kèm tên Loại và tên Danh mục
if (!empty($search)) {
    // Nếu có tìm kiếm: Sử dụng LIKE để tìm theo tên sản phẩm
    $sql = "SELECT sp.*, lt.ten_loai, dm.ten_danhmuc 
            FROM sanpham sp
            JOIN loai_thucung lt ON sp.loai_thucung_id = lt.id
            JOIN danhmuc dm ON sp.danhmuc_id = dm.id
            WHERE sp.tensanpham LIKE ? 
            ORDER BY sp.id DESC";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search . "%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Nếu không tìm kiếm: Lấy toàn bộ sản phẩm
    $sql = "SELECT sp.*, lt.ten_loai, dm.ten_danhmuc 
            FROM sanpham sp
            JOIN loai_thucung lt ON sp.loai_thucung_id = lt.id
            JOIN danhmuc dm ON sp.danhmuc_id = dm.id
            ORDER BY sp.id DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Quản trị - PetShop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; background: #333; color: white; padding: 15px; border-radius: 5px; }
        .admin-header a { color: #fff; text-decoration: none; font-weight: bold; margin-left: 15px; }
        .btn { padding: 8px 12px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block; cursor: pointer; border: none; }
        .btn-add { background: #4CAF50; color: white; margin: 20px 0; }
        .btn-edit { background: #2196F3; color: white; font-size: 12px; }
        .btn-delete { background: #f44336; color: white; font-size: 12px; }
        .search-box { margin: 20px 0; display: flex; gap: 10px; }
        .search-box input { padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #ff9800; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .product-img { width: 60px; hieght: auto; border-radius: 4px; }
    </style>
</head>
<body>

<div class="admin-header">
    <h2>Trang Quản Trị Admin 🐾</h2>
    <div>
        <span>Xin chào, Admin | </span>
        <a href="../index.php">Xem Trang Chủ</a>
        <a href="../dangxuat.php" style="color: #ff5722;">Đăng xuất</a>
    </div>
</div>

<a href="them.php" class="btn btn-add">+ Thêm Sản Phẩm Mới</a>

<!-- Form tìm kiếm sản phẩm -->
<form action="" method="GET" class="search-box">
    <input type="text" name="search" placeholder="Nhập tên sản phẩm cần tìm..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit" class="btn" style="background: #333; color: white;">Tìm kiếm</button>
    <?php if(!empty($search)): ?>
        <a href="index.php" class="btn" style="background: #9e9e9e; color: white;">Hủy lọc</a>
    <?php endif; ?>
</form>

<!-- Bảng hiển thị danh sách sản phẩm -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Hình ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Giá bán</th>
            <th>Số lượng</th>
            <th>Loài</th>
            <th>Danh mục</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><img src="../images/<?php echo htmlspecialchars($row['hinhanh']); ?>" class="product-img" alt="Product"></td>
                    <td><b><?php echo htmlspecialchars($row['tensanpham']); ?></b></td>
                    <td><?php echo number_format($row['giaban']); ?> đ</td>
                    <td><?php echo $row['soluong']; ?></td>
                    <td><?php echo htmlspecialchars($row['ten_loai']); ?></td>
                    <td><?php echo htmlspecialchars($row['ten_danhmuc']); ?></td>
                    <td>
                        <a href="sua.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Sửa</a>
                        <a href="index.php?action=xoa&id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align: center; color: #999;">Không tìm thấy sản phẩm nào!</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>