<?php
http_response_code(500);
$logo = "fotolar/logo.png";
$request_uri = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hata 500 | Syline</title>
<link rel="icon" type="image/x-icon" href="fotolar/syline.ico">

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

:root{
  --red:#ff1e1e;
  --dark-red:#8b0000;
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
  filter:drop-shadow(0 0 22px rgba(255,0,0,.85));
}

/* CENTER */
.center-box{
  position:absolute;
  top:50%;
  left:50%;
  transform:translate(-50%,-50%);
  text-align:center;
  background:var(--glass);
  padding:42px 58px;
  border-radius:30px;
  backdrop-filter:blur(24px);
  border:1px solid rgba(255,50,50,0.35);
  box-shadow:0 0 55px rgba(255,0,0,.55);
  animation:fadeIn .8s ease;
}

.center-box h1{
  font-size:56px;
  margin:8px 0;
  color:var(--red);
  text-shadow:0 0 30px rgba(255,0,0,1);
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
  padding:13px 32px;
  border-radius:22px;
  cursor:pointer;
  color:#fff;
  font-size:15px;
  font-weight:600;
  box-shadow:0 0 22px rgba(255,0,0,.85);
  transition:.25s;
}
.btn:hover{
  transform:scale(1.08);
  box-shadow:0 0 40px rgba(255,0,0,1);
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

  <h1>500</h1>
  <p><b>Internal Server Error</b></p>
  <p>Sunucu tarafında bir hata oluştu. Lütfen daha sonra tekrar dene.</p>

  <button class="btn" onclick="location.reload()">Tekrar Dene</button>
</div>

<script>
// 🔴 KIRMIZI NEON PARTICLES
const canvas=document.getElementById('bg');
const ctx=canvas.getContext('2d');
let w,h,particles=[];

function resize(){
  w=canvas.width=window.innerWidth;
  h=canvas.height=window.innerHeight;
}
window.addEventListener('resize',resize);
resize();

for(let i=0;i<30;i++){
  particles.push({
    x:Math.random()*w,
    y:Math.random()*h,
    r:Math.random()*120+40,
    dx:(Math.random()-.5)*0.45,
    dy:(Math.random()-.5)*0.45,
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
    if(p.x<-120||p.x>w+120)p.dx*=-1;
    if(p.y<-120||p.y>h+120)p.dy*=-1;
  }
  requestAnimationFrame(draw);
}
draw();
</script>

</body>
</html>
