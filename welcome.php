<?php
session_start();
include("db.php");

$error = "";
$success = "";
$username = "";
$ref_code = "";

/* -------------------- LOGIN -------------------- */
if (isset($_POST["login"])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            header("Location: index.php");
            exit();
        }
    }
    $error = "Incorrect username or password!";
}

/* -------------------- REGISTER -------------------- */
if (isset($_POST["register"])) {
    $show_register = false; // varsayılan login
    $username = trim($_POST['new_username']);
    $password = trim($_POST['new_password']);
    $ref_code = trim($_POST['ref_code']);

    if ($ref_code !== "İmodXxSikerAtar123") {
        $error = "Invalid referral code!";
    } else {
        $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "This username is already taken!";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, ref_code) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $hashed_pass, $ref_code);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover" />
<title>Syline</title>
<link rel="icon" type="image/x-icon" href="fotolar/syline.ico">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

:root{
  --bg:#000;
  --glass: rgba(255,255,255,0.06);
  --glass-border: rgba(255,30,30,0.35);

  --accent1: #ff1e1e;   /* ana kırmızı */
  --accent2: #b30000;   /* koyu kırmızı */
  --accent-glow: rgba(255,30,30,0.9);

  --muted: #aaa;
}

*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;
  font-family:"Inter",system-ui,Segoe UI,Roboto,"Helvetica Neue",Arial;
  background:var(--bg);
  color:#fff;
  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:10px;
  overflow-x:hidden;
}

/* canvas background */
canvas{position:fixed;inset:0;width:100%;height:100%;z-index:-1}

/* flip layout */
.flip-wrapper{
  width:100%;
  max-width:500px; /* arttırdım */
  padding:20px;
  display:flex;
  align-items:center;
  justify-content:center;
  min-height:100vh;
}


/* flip card */
.flip-card {
  width:100%;
  max-width:450px;
  min-height:480px;
  position:relative;
  transform-style:preserve-3d;
  transition: transform 0.75s cubic-bezier(.2,.9,.2,1);
  perspective: 1000px;
}

.flip-card.flip {
  transform: rotateY(180deg);
}

.box {
  position:absolute;
  inset:0;
    box-shadow:
    0 0 35px rgba(255,30,30,0.25),
    inset 0 0 25px rgba(255,30,30,0.12);
  padding:24px 20px;
  border-radius:22px;
  background: var(--glass);
  border:1px solid var(--glass-border);
  backdrop-filter: blur(25px) saturate(200%);
  -webkit-backface-visibility:hidden;
  backface-visibility:hidden;
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  text-align:center;
}

.front {
  transform: rotateY(0deg);
}

.back {
  transform: rotateY(180deg);
}


.logo{
  width:62px; height:62px; border-radius:50%; margin-bottom:12px;
  box-shadow:0 0 20px rgba(0,255,255,0.15);
  animation: float 3s ease-in-out infinite;
}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}

.box h2{color:var(--accent1);
  text-shadow:
    0 0 10px var(--accent-glow),
    0 0 25px rgba(255,30,30,0.6);}

input[type="text"], input[type="password"]{
  width:100%; padding:12px 14px; margin:8px 0; border-radius:12px;
    background:rgba(255,255,255,0.06);
    border:1px solid rgba(255,30,30,0.35);
    box-shadow: inset 0 0 12px rgba(255,30,30,0.15);
  border:none; background: rgba(255,255,255,0.08); color:#fff; font-size:15px; outline:none;
}
    input:focus{
  box-shadow:
    0 0 0 2px rgba(255,30,30,0.4),
    inset 0 0 15px rgba(255,30,30,0.2);
}
input::placeholder{color:#cbd5e0}

button{
  width:100%; padding:12px; margin-top:14px; border-radius:12px; border:none;
  cursor:pointer; font-weight:600;
 background:linear-gradient(135deg,var(--accent1),var(--accent2));
    box-shadow:
    0 0 12px var(--accent-glow),
    0 0 25px rgba(255,30,30,0.3);
  color:#fff; box-shadow:0 6px 18px rgba(0,114,255,0.12);
  transition: transform .18s ease, box-shadow .18s ease;
}
    
    button:hover{
  box-shadow:
    0 0 25px var(--accent-glow),
    0 0 45px rgba(255,30,30,0.4);
} 
button:active{transform:translateY(1px)}
.box p{margin-top:14px;color:var(--muted);font-size:14px}
.box p a{color:var(--accent1);text-decoration:none;cursor:pointer}
.box p a:hover{text-decoration:underline}

.error{color:#ff6b6b;margin-bottom:8px}
.success{color:#7ef1b7;margin-bottom:8px}

/* responsive */
@media (max-width:1024px){
  .flip-card{
    max-width:90vw;
    min-height:420px;
  }
  .box{
    padding:18px;
  }
  input, button{
    font-size:15px;
  }
}

@media (max-width:480px){
  .flip-card{
    max-width:95vw;
    min-height:380px;
  }
}


@media (max-height:500px){
  .flip-card{ height:420px; }
}
</style>
</head>
<body>

<canvas id="bg"></canvas>

<div class="flip-wrapper">
<div class="flip-card <?= (isset($_POST['register']) && $error) ? 'flip' : '' ?>" id="card">

    <!-- LOGIN -->
    <div class="box front">
      <img src="fotolar/logo.png" alt="logo" class="logo">
      <h2>Login to Syline</h2>

      <?php if ($error && isset($_POST["login"])) echo "<div class='error'>$error</div>"; ?>
      <?php if ($success) echo "<div class='success'>$success</div>"; ?>

      <form method="post" style="width:100%;max-width:320px;">
        <input type="text" name="username" placeholder="Username" required value="<?= isset($_POST['username'])?htmlspecialchars($_POST['username']):'' ?>">
        <input type="password" name="password" placeholder="Password" required>
        <button name="login" type="submit">Login</button>
      </form>

      <p>Don't have an account? <a onclick="flip()" role="button">Register</a></p>
    </div>

    <!-- REGISTER -->
    <div class="box back">
      <img src="fotolar/logo.png" alt="logo" class="logo">
      <h2>Register to Syline</h2>

      <?php if ($error && isset($_POST["register"])) echo "<div class='error'>$error</div>"; ?>

      <form method="post" style="width:100%;max-width:320px;">
        <input type="text" name="new_username" placeholder="Username" required value="<?= isset($_POST['new_username'])?htmlspecialchars($_POST['new_username']):'' ?>">
        <input type="password" name="new_password" placeholder="Password" required>
        <input type="text" name="ref_code" placeholder="Referral Code" required value="<?= isset($_POST['ref_code'])?htmlspecialchars($_POST['ref_code']):'' ?>">
        <button name="register" type="submit">Register</button>
      </form>

      <p>Already have an account? <a onclick="flip()" role="button">Login</a></p>
    </div>
  </div>
</div>

<script>
function flip(){
  document.getElementById('card').classList.toggle('flip');
  document.activeElement && document.activeElement.blur();
}

// neon background
const canvas=document.getElementById('bg');
const ctx=canvas.getContext('2d');
let w,h,particles=[];
function resize(){ w=canvas.width=window.innerWidth; h=canvas.height=window.innerHeight; }
window.addEventListener('resize', resize);
resize();
for(let i=0;i<30;i++){
  particles.push({
    x: Math.random()*w,
    y: Math.random()*h,
    r: Math.random()*100+50,
    dx: (Math.random()-0.5)*0.5,
    dy: (Math.random()-0.5)*0.5,
   color: 'rgba(255, 0, 0, 0.30)'
  });
}
    function flip(){
  document.getElementById('card').classList.toggle('flip');
}

function draw(){
  ctx.clearRect(0,0,w,h);
  for(let p of particles){
    const g = ctx.createRadialGradient(p.x,p.y,0,p.x,p.y,p.r);
    g.addColorStop(0,p.color); g.addColorStop(1,'transparent');
    ctx.fillStyle=g;
    ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2); ctx.fill();
    p.x += p.dx; p.y += p.dy;
    if(p.x < -100 || p.x > w+100) p.dx *= -1;
    if(p.y < -100 || p.y > h+100) p.dy *= -1;
  }
  requestAnimationFrame(draw);
}
draw();
</script>

</body>
</html>
