<?php
$adminIps = [
    "127.0.0.1",
    "BURAYA_KENDI_IP"
];

$userIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if (in_array($userIp, $adminIps)) {
    header("Location: index.php");
    exit;
}

http_response_code(503);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Syline | Bakımda</title>
<link rel="icon" href="fotolar/logo.ico">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

:root{
    --red-main:#ff1e1e;
    --red-dark:#b30000;
    --red-glow:rgba(255,30,30,0.9);
}

*{margin:0;padding:0;box-sizing:border-box;font-family:Inter,sans-serif;}

body{
    min-height:100vh;
    background:radial-gradient(circle at top,#0a0a0a,#000);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}

canvas{position:fixed;inset:0;z-index:-1;}

.card{
    background:rgba(255,255,255,0.06);
    border:1px solid rgba(255,255,255,0.15);
    border-radius:26px;
    padding:40px 45px;
    max-width:420px;
    width:90%;
    text-align:center;
    backdrop-filter:blur(25px);
    box-shadow:0 0 40px var(--red-glow);
}

.logo{
    width:90px;
    height:90px;
    border-radius:50%;
    box-shadow:0 0 30px var(--red-glow);
    animation:float 3s ease-in-out infinite;
}

@keyframes float{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-10px)}
}

h1{
    margin-top:20px;
    font-size:26px;
    color:var(--red-main);
    text-shadow:0 0 20px var(--red-glow);
}

p{
    margin-top:12px;
    font-size:15px;
    color:#ccc;
    line-height:1.6;
}

.status{
    margin-top:25px;
    display:inline-block;
    padding:8px 18px;
    border-radius:20px;
    background:linear-gradient(135deg,var(--red-main),var(--red-dark));
    box-shadow:0 0 20px var(--red-glow);
    font-weight:600;
    font-size:14px;
}

.telegram-btn{
    margin-top:20px;
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:12px 22px;
    border-radius:22px;
    background:linear-gradient(135deg,#ff1e1e,#b30000);
    color:#fff;
    font-weight:600;
    text-decoration:none;
    box-shadow:0 0 25px var(--red-glow);
    transition:.3s;
}

.telegram-btn:hover{
    transform:scale(1.07);
    box-shadow:0 0 40px var(--red-glow);
}

.footer{
    margin-top:28px;
    font-size:14px;
    color:#aaa;
}

.footer span{
    color:var(--red-main);
    text-shadow:0 0 12px var(--red-glow);
}
</style>
</head>
<body>

<canvas id="bg"></canvas>

<div class="card">
    <img src="fotolar/logo.png" class="logo">
    <h1>Bakımdayız</h1>
    <p>
        Sistem şu anda güncelleniyor.<br>
        Duyurular için Telegram kanalımıza katıl.
    </p>

    <div class="status">⛔ Geçici Olarak Kapalı</div>

    <a href="https://t.me/mamisko" target="_blank" class="telegram-btn">
        🚀 Telegram @mamisko
    </a>

    <div class="footer">
        Powered By <span>@mamisko / @sylinepubg</span>
    </div>
</div>

<script>
const c=document.getElementById("bg");
const x=c.getContext("2d");
let w,h,p=[];
function r(){w=c.width=innerWidth;h=c.height=innerHeight;}
addEventListener("resize",r);r();

for(let i=0;i<25;i++){
 p.push({x:Math.random()*w,y:Math.random()*h,r:Math.random()*120+60,dx:(Math.random()-.5)*0.4,dy:(Math.random()-.5)*0.4});
}

(function d(){
 x.clearRect(0,0,w,h);
 for(let a of p){
  const g=x.createRadialGradient(a.x,a.y,0,a.x,a.y,a.r);
  g.addColorStop(0,"rgba(255,30,30,.25)");
  g.addColorStop(1,"transparent");
  x.fillStyle=g;
  x.beginPath();x.arc(a.x,a.y,a.r,0,Math.PI*2);x.fill();
  a.x+=a.dx;a.y+=a.dy;
  if(a.x<0||a.x>w)a.dx*=-1;
  if(a.y<0||a.y>h)a.dy*=-1;
 }
 requestAnimationFrame(d);
})();
</script>

</body>
</html>
