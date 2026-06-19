<?php
require_once 'includes/header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Buat direktori gambar jika belum ada
$img_dir = __DIR__ . '/../assets/img/';
if (!is_dir($img_dir)) {
    mkdir($img_dir, 0777, true);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM menu_items WHERE id = $id");
    header("Location: menu_items.php?msg=deleted");
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $success = "Menu item berhasil dihapus.";
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)$_POST['category_id'];
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = (float)$_POST['harga'];
    $tersedia = isset($_POST['tersedia']) ? 1 : 0;
    
    // Handle File Upload
    $gambar = $_POST['gambar_lama'] ?? 'default.jpg';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['gambar']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_name = uniqid() . '.' . $ext;
            if (move_uploaded_file($tmp, $img_dir . $new_name)) {
                $gambar = $new_name;
            }
        } else {
            $error = "Format gambar tidak didukung (hanya jpg, png, webp).";
        }
    }
    
    if (!$error) {
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO menu_items (category_id, nama, deskripsi, harga, gambar, tersedia) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdsi", $category_id, $nama, $deskripsi, $harga, $gambar, $tersedia);
            if ($stmt->execute()) {
                header("Location: menu_items.php?msg=added");
                exit;
            } else {
                $error = "Gagal menambah data.";
            }
        } elseif ($action === 'edit') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE menu_items SET category_id = ?, nama = ?, deskripsi = ?, harga = ?, gambar = ?, tersedia = ? WHERE id = ?");
            $stmt->bind_param("issdsii", $category_id, $nama, $deskripsi, $harga, $gambar, $tersedia, $id);
            if ($stmt->execute()) {
                header("Location: menu_items.php?msg=updated");
                exit;
            } else {
                $error = "Gagal mengubah data.";
            }
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $success = "Menu item berhasil ditambahkan.";
    if ($_GET['msg'] === 'updated') $success = "Menu item berhasil diupdate.";
}

?>

<h1 class="page-title">Manajemen Menu</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <?php
    $menus = $conn->query("
        SELECT m.*, c.nama as kategori 
        FROM menu_items m 
        JOIN categories c ON m.category_id = c.id 
        ORDER BY c.id, m.nama ASC
    ");
    ?>
    <div class="card">
        <div style="margin-bottom: 20px;">
            <a href="menu_items.php?action=add" class="btn btn-primary">+ Tambah Menu</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="80">Gambar</th>
                        <th>Kategori</th>
                        <th>Nama Menu</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($menus->num_rows > 0): ?>
                        <?php while ($row = $menus->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="/kopi-app/assets/img/<?= htmlspecialchars($row['gambar']) ?>" 
                                         alt="<?= htmlspecialchars($row['nama']) ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"
                                         onerror="this.src='/kopi-app/assets/img/default.jpg'">
                                </td>
                                <td><?= htmlspecialchars($row['kategori']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($row['tersedia']): ?>
                                        <span class="badge badge-selesai">Tersedia</span>
                                    <?php else: ?>
                                        <span class="badge badge-batal">Kosong</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="menu_items.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="menu_items.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus menu ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">Belum ada menu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <?php
    $menu = ['id' => '', 'category_id' => '', 'nama' => '', 'deskripsi' => '', 'harga' => '', 'gambar' => 'default.jpg', 'tersedia' => 1];
    if ($action === 'edit' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $res = $conn->query("SELECT * FROM menu_items WHERE id = $id");
        if ($res->num_rows > 0) {
            $menu = $res->fetch_assoc();
        } else {
            die("Menu tidak ditemukan.");
        }
    }
    $categories = $conn->query("SELECT * FROM categories ORDER BY nama ASC");
    ?>
    <div class="card" style="max-width: 800px;">
        <h2 style="margin-bottom:20px; font-family:'Playfair Display',serif; color:var(--secondary);">
            <?= $action === 'add' ? 'Tambah Menu' : 'Edit Menu' ?>
        </h2>
        <form method="POST" action="menu_items.php?action=<?= $action ?>" enctype="multipart/form-data">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?= $menu['id'] ?>">
                <input type="hidden" name="gambar_lama" value="<?= $menu['gambar'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Kategori</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>" <?= $menu['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($menu['nama']) ?>">
            </div>
            
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($menu['deskripsi']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" required value="<?= $menu['harga'] ?>">
            </div>
            
            <div class="form-group">
                <label>Gambar (Biarkan kosong jika tidak ingin mengubah)</label>
                <input type="file" name="gambar" class="form-control" accept="image/*">
                <?php if ($action === 'edit' && $menu['gambar']): ?>
                    <div style="margin-top: 10px;">
                        <img src="/kopi-app/assets/img/<?= htmlspecialchars($menu['gambar']) ?>" alt="" style="width: 100px; border-radius: 8px;">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="tersedia" value="1" <?= $menu['tersedia'] ? 'checked' : '' ?> style="width:18px; height:18px;">
                    Menu Tersedia
                </label>
            </div>
            
            <div style="margin-top:30px;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="menu_items.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
