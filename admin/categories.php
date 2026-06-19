<?php
require_once 'includes/header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    header("Location: categories.php?msg=deleted");
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $success = "Kategori berhasil dihapus.";
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug'])));
    
    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO categories (nama, slug) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $slug);
        if ($stmt->execute()) {
            header("Location: categories.php?msg=added");
            exit;
        } else {
            $error = "Gagal menambah. Slug mungkin sudah ada.";
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE categories SET nama = ?, slug = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nama, $slug, $id);
        if ($stmt->execute()) {
            header("Location: categories.php?msg=updated");
            exit;
        } else {
            $error = "Gagal mengubah. Slug mungkin sudah ada.";
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $success = "Kategori berhasil ditambahkan.";
    if ($_GET['msg'] === 'updated') $success = "Kategori berhasil diupdate.";
}

?>

<h1 class="page-title">Manajemen Kategori</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <?php
    $categories = $conn->query("SELECT * FROM categories ORDER BY nama ASC");
    ?>
    <div class="card">
        <div style="margin-bottom: 20px;">
            <a href="categories.php?action=add" class="btn btn-primary">+ Tambah Kategori</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories->num_rows > 0): ?>
                        <?php while ($row = $categories->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><code><?= htmlspecialchars($row['slug']) ?></code></td>
                                <td>
                                    <a href="categories.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="categories.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus? Menu dengan kategori ini juga akan terhapus.')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center;">Belum ada kategori.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <?php
    $cat = ['id' => '', 'nama' => '', 'slug' => ''];
    if ($action === 'edit' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $res = $conn->query("SELECT * FROM categories WHERE id = $id");
        if ($res->num_rows > 0) {
            $cat = $res->fetch_assoc();
        } else {
            die("Kategori tidak ditemukan.");
        }
    }
    ?>
    <div class="card" style="max-width: 600px;">
        <h2 style="margin-bottom:20px; font-family:'Playfair Display',serif; color:var(--secondary);">
            <?= $action === 'add' ? 'Tambah Kategori' : 'Edit Kategori' ?>
        </h2>
        <form method="POST" action="categories.php?action=<?= $action ?>">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($cat['nama']) ?>">
            </div>
            <div class="form-group">
                <label>Slug (URL Friendly)</label>
                <input type="text" name="slug" class="form-control" required value="<?= htmlspecialchars($cat['slug']) ?>">
                <small style="color:var(--text-muted);">Contoh: kopi-susu</small>
            </div>
            
            <div style="margin-top:20px;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="categories.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
