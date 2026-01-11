<?php
http_response_code(403);
$logo = "fotolar/logo.png";
$request_uri = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hata 403 | Syline</title>
<link rel="icon" type="image/x-icon" href="fotolar/syline.ico">

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

:root{
  --red:#ff2d2d;
  --dark-red:#b30000;
  --glass:rgba(255,255,255,0.08);
}

body{
  margin:0;
  font-family:'Inter',sans-serif;
  color:#fff;
  background:#000;
  overflow:hidden;
}

canvas{
  position:fixed;
  inset:0;
  z-index:-1;
}

/* LOGO */
.top-logo{
  text-align:center;
  margin-bottom:25px;
}
.top-logo img{
  width:120px;
  filter:drop-shadow(0 0 18px rgba(255,0,0,.7));
}

/* CENTER BOX */
.center-box{
  position:absolute;
  top:50%;
  left:50%;
  transform:translate(-50%,-50%);
  text-align:center;
  background:var(--glass);
  padding:40px 55px;
  border-radius:30px;
  backdrop-filter:blur(22px);
  border:1px solid rgba(255,80,80,0.35);
  box-shadow:0 0 45px rgba(255,0,0,0.45);
  animation:fadeIn .8s ease;
}

.center-box h1{
  font-size:54px;
  margin:10px 0;
  color:var(--red);
  text-shadow:0 0 25px rgba(255,0,0,.9);
}

.center-box p{
  font-size:16px;
  color:#ccc;
  margin-top:10px;
}

/* BUTTON */
.btn{
  margin-top:28px;
  background:linear-gradient(135deg,var(--red),var(--dark-red));
  border:none;
  padding:12px 30px;
  border-radius:22px;
  cursor:pointer;
  color:#fff;
  font-size:15px;
  font-weight:600;
  box-shadow:0 0 20px rgba(255,0,0,.7);
  transition:.25s;
}
.btn:hover{
  transform:scale(1.08);
  box-shadow:0 0 35px rgba(255,0,0,1);
}

@keyframes fadeIn{
  from{opacity:0;transform:translate(-50%,-45%)}
  to{opacity:1;transform:translate(-50%,-50%)}
}
</style>
</head>

<body>

<canvas id="bg"></canvas>

<div class="center-box">

  <div class="top-logo">
    <img src="<?php echo $logo; ?>" alt="Logo">
  </div>

  <h1>403</h1>
  <p><b>Erişim Engellendi</b></p>
  <p>Bu sayfaya erişim iznin yok.</p>

  <button class="btn" onclick="history.back()">Geri Dön</button>
</div>

<script>
// 🔴 KIRMIZI NEON PARTICLE BACKGROUND
const canvas=document.getElementById('bg');
const ctx=canvas.getContext('2d');
let w,h,particles=[];

function resize(){
  w=canvas.width=window.innerWidth;
  h=canvas.height=window.innerHeight;
}
window.addEventListener('resize',resize);
resize();

for(let i=0;i<25;i++){
  particles.push({
    x:Math.random()*w,
    y:Math.random()*h,
    r:Math.random()*100+40,
    dx:(Math.random()-.5)*0.4,
    dy:(Math.random()-.5)*0.4,
    color:'rgba(255,0,0,0.35)'
  });
}

function draw(){
  ctx.clearRect(0,0,w,h);
  for(const p of particles){
    const g=ctx.createRadialGradient(p.x,p.y,0,p.x,p.y,p.r);
    g.addColorStop(0,p.color);
    g.addColorStop(1,'transparent');
    ctx.fillStyle=g;
    ctx.beginPath();
    ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
    ctx.fill();
    p.x+=p.dx; p.y+=p.dy;
    if(p.x<-100||p.x>w+100)p.dx*=-1;
    if(p.y<-100||p.y>h+100)p.dy*=-1;
  }
  requestAnimationFrame(draw);
}
draw();
</script>

</body>
</html>
