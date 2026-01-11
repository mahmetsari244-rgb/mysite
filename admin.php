<?php
ob_start();
session_start();
include("db.php");

// ------------------ ADMIN KONTROL ------------------
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: welcome.php");
    exit;
}

// ------------------ ERROR ------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ------------------ MAINTENANCE ------------------
$maintFile = "maintenance.json";
if (!file_exists($maintFile)) {
    file_put_contents($maintFile, json_encode(["maintenance"=>false], JSON_PRETTY_PRINT));
}
$maint = json_decode(file_get_contents($maintFile), true);

if (isset($_GET['toggle_maint'])) {
    $maint['maintenance'] = !$maint['maintenance'];
    file_put_contents($maintFile, json_encode($maint, JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

// ------------------ APPS ------------------
$dataFile = "apps.json";
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([], JSON_PRETTY_PRINT));
}
$apps = json_decode(file_get_contents($dataFile), true);
if (!is_array($apps)) $apps = [];

// APP EKLE
if (isset($_POST['add_app'])) {
    $icon = $_POST['icon'] ? "fotolar/".basename($_POST['icon']) : "fotolar/default.png";
    $apps[] = [
        "icon"=>$icon,
        "name"=>$_POST['name'],
        "version"=>$_POST['version'],
        "link"=>$_POST['link'],
        "category"=>$_POST['category']
    ];
    file_put_contents($dataFile, json_encode($apps, JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

// APP SİL
if (isset($_GET['delete_app'])) {
    unset($apps[(int)$_GET['delete_app']]);
    file_put_contents($dataFile, json_encode(array_values($apps), JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

// ------------------ USERS ------------------
if (isset($_POST['add_user'])) {
    $stmt = $conn->prepare("INSERT INTO users (username,password,role) VALUES (?,?,?)");
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $_POST['username'], $hash, $_POST['role']);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}

if (isset($_GET['delete_user'])) {
    $conn->query("DELETE FROM users WHERE id=".(int)$_GET['delete_user']);
    header("Location: admin.php");
    exit;
}

$users=[];
$res=$conn->query("SELECT id,username,role FROM users ORDER BY id DESC");
while($u=$res->fetch_assoc()) $users[]=$u;

// ------------------ LOGOUT ------------------
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: welcome.php");
    exit;
}

// ------------------ TAB ------------------
$tab = $_GET['tab'] ?? 'apps';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<style>
body{background:#0a0a0a;color:#fff;font-family:Arial;margin:0;padding:0;}
.menu{display:flex;gap:15px;padding:15px;background:#111;justify-content:center;}
.menu a{color:#ff1e1e;text-decoration:none;font-weight:bold;padding:8px 16px;border-radius:8px;}
.menu a.active{background:#ff1e1e;color:#000;}
.box{background:#111;padding:20px;border-radius:15px;margin:20px auto;max-width:800px;}
input,select,button{width:100%;padding:10px;margin:5px 0;border-radius:8px;border:none}
button{background:#ff1e1e;color:#fff;font-weight:bold;cursor:pointer;}
table{width:100%;border-collapse:collapse;margin-top:15px;}
td,th{padding:10px;border-bottom:1px solid #333;text-align:center;}
a.del{color:#ff4d4d;text-decoration:none;font-weight:bold;cursor:pointer;}
img{width:40px;border-radius:10px;}
</style>
</head>
<body>

<div class="menu">
<a href="?tab=apps" class="<?= $tab=='apps'?'active':'' ?>">📦 Apps</a>
<a href="?tab=users" class="<?= $tab=='users'?'active':'' ?>">👤 Users</a>
<a href="?tab=maint" class="<?= $tab=='maint'?'active':'' ?>">🛠 Bakım: <?= $maint['maintenance']?'AÇIK 🟢':'KAPALI 🔴' ?></a>
<a href="?logout=1">🚪 Logout</a>
</div>

<?php if($tab=='apps'): ?>
<div class="box">
<h3>➕ App Ekle</h3>
<form method="post">
<input name="icon" placeholder="icon.png (boş = default)">
<input name="name" required placeholder="App adı">
<input name="version" required placeholder="Version">
<input name="link" required placeholder="Link">
<select name="category">
<option>king</option><option>zoon</option><option>vn</option><option>oasis</option><option>star</option><option>more</option>
</select>
<button name="add_app">EKLE</button>
</form>

<table>
<tr><th>#</th><th>Icon</th><th>Ad</th><th>Ver</th><th>Sil</th></tr>
<?php foreach($apps as $i=>$a): ?>
<tr>
<td><?=$i?></td>
<td><img src="<?=$a['icon']?>"></td>
<td><?=$a['name']?></td>
<td><?=$a['version']?></td>
<td><a class="del" href="?delete_app=<?=$i?>" onclick="return confirm('Silinsin mi?')">❌</a></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php endif; ?>

<?php if($tab=='users'): ?>
<div class="box">
<h3>➕ User Ekle</h3>
<form method="post">
<input name="username" required placeholder="Username">
<input name="password" type="password" required placeholder="Password">
<select name="role"><option>user</option><option>admin</option></select>
<button name="add_user">EKLE</button>
</form>

<table>
<tr><th>ID</th><th>User</th><th>Role</th><th>Sil</th></tr>
<?php foreach($users as $u): ?>
<tr>
<td><?=$u['id']?></td>
<td><?=$u['username']?></td>
<td><?=$u['role']?></td>
<td><a class="del" href="?delete_user=<?=$u['id']?>" onclick="return confirm('Silinsin mi?')">❌</a></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php endif; ?>

<?php if($tab=='maint'): ?>
<div class="box">
<h3>⚙️ Bakım Modu</h3>
<p>Şu an bakım modu: <strong><?= $maint['maintenance']?'AÇIK 🟢':'KAPALI 🔴' ?></strong></p>
<a href="?toggle_maint=1"><button>Durumu Değiştir</button></a>
</div>
<?php endif; ?>

</body>
</html>
