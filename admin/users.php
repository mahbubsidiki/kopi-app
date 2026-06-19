<?php
require_once 'includes/header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id === $_SESSION['user_id']) {
        $error = "Anda tidak bisa menghapus akun Anda sendiri saat sedang login.";
    } else {
        $conn->query("DELETE FROM users WHERE id = $id");
        header("Location: users.php?msg=deleted");
        exit;
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $success = "User berhasil dihapus.";
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '';
    
    if ($action === 'add') {
        if (empty($password)) {
            $error = "Password wajib diisi.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nama, $email, $hashed, $role);
            if ($stmt->execute()) {
                header("Location: users.php?msg=added");
                exit;
            } else {
                $error = "Gagal menambah user. Email mungkin sudah terdaftar.";
            }
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, role = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nama, $email, $role, $hashed, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nama, $email, $role, $id);
        }
        
        if ($stmt->execute()) {
            header("Location: users.php?msg=updated");
            exit;
        } else {
            $error = "Gagal mengupdate user. Email mungkin sudah dipakai.";
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $success = "User berhasil ditambahkan.";
    if ($_GET['msg'] === 'updated') $success = "User berhasil diupdate.";
}

?>

<h1 class="page-title">Manajemen User</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <?php
    $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    ?>
    <div class="card">
        <div style="margin-bottom: 20px;">
            <a href="users.php?action=add" class="btn btn-primary">+ Tambah User</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->num_rows > 0): ?>
                        <?php while ($row = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td>
                                    <?php if ($row['role'] === 'admin'): ?>
                                        <span class="badge badge-proses">Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">User</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="users.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                        <a href="users.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">Belum ada user.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <?php
    $user = ['id' => '', 'nama' => '', 'email' => '', 'role' => 'user'];
    if ($action === 'edit' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $res = $conn->query("SELECT * FROM users WHERE id = $id");
        if ($res->num_rows > 0) {
            $user = $res->fetch_assoc();
        } else {
            die("User tidak ditemukan.");
        }
    }
    ?>
    <div class="card" style="max-width: 600px;">
        <h2 style="margin-bottom:20px; font-family:'Playfair Display',serif; color:var(--secondary);">
            <?= $action === 'add' ? 'Tambah User' : 'Edit User' ?>
        </h2>
        <form method="POST" action="users.php?action=<?= $action ?>">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($user['nama']) ?>">
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            
            <div class="form-group" style="position: relative;">
                <label>Password <?= $action === 'edit' ? '(Biarkan kosong jika tidak diubah)' : '' ?></label>
                <input type="password" id="userPassword" name="password" class="form-control" <?= $action === 'add' ? 'required' : '' ?> style="padding-right: 40px;">
                <span onclick="togglePassword('userPassword')" style="position: absolute; right: 15px; top: 38px; cursor: pointer; color: var(--text-muted, #6C757D);">
                    <svg id="eyeIcon_userPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </span>
            </div>
            
            <div style="margin-top:30px;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="users.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
    <script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById('eyeIcon_' + fieldId);
        if (field.type === 'password') {
            field.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            field.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }
    </script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
