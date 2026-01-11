<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$apps = json_decode(
    file_get_contents($_SERVER['DOCUMENT_ROOT']."/apps.json"),
    true
);

if (!is_array($apps)) {
    $apps = [];
}
?>

<?php
$maint = json_decode(@file_get_contents("maintenance.json"), true);

if (isset($maint['maintenance']) && $maint['maintenance'] === true) {
    include("bakim.php");
    exit;
}
?>

<?php
if (!isset($_SESSION['username'])) {
    header("Location: welcome.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$logo = "fotolar/logo.png";
$apps = json_decode(file_get_contents("apps.json"), true);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Syline</title>
<link rel="icon" type="image/x-icon" href="fotolar/logo.ico">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

    :root{
    --bg-black:#000;
    --glass:rgba(255,255,255,0.08);
    --red-main:#ff1e1e;
    --red-dark:#b30000;
    --red-glow:rgba(255,30,30,0.8);
    --text-soft:#aaa;
}
    
body{
    background:radial-gradient(circle at top, #0a0a0a, #000);
    color:#fff;
}

::-webkit-scrollbar-thumb{
    background:linear-gradient(180deg,var(--red-main),var(--red-dark));
    box-shadow:0 0 10px var(--red-glow);
}
::-webkit-scrollbar-thumb:hover{
    background:linear-gradient(180deg,#ff4d4d,#cc0000);
}
    
canvas {
    position:fixed;
    top:0;left:0;
    width:100%;height:100%;
    z-index:-1;
}

/* Header */
.header {
    position:relative;
    padding:25px 20px;
    font-size:24px;
    font-weight:700;
    text-shadow:0 0 15px var(--red-glow);
    display:flex;
    align-items:center;
    justify-content:space-between;
}
.logo {
    width:48px;
    height:48px;
    border-radius:50%;
    box-shadow:0 0 15px rgba(255,255,255,0.3);
    filter: drop-shadow(0 0 12px var(--red-glow));
    animation:float 3s ease-in-out infinite;
    cursor:pointer;
    transition:transform 0.3s ease;
}
.logo:hover {
    transform:scale(1.1) rotate(8deg);
}
@keyframes float {
    0%,100% {transform:translateY(0);}
    50% {transform:translateY(-6px);}
}

/* Search */
.search-btn {
    background:rgba(255,255,255,0.1);
    border:none;
    font-size:22px;
    color:#fff;
    cursor:pointer;
    border-radius:50%;
    width:40px;
    height:40px;
    backdrop-filter:blur(10px);
    transition:all 0.3s ease;
}
.search-btn:hover {transform:scale(1.1) rotate(12deg);background:rgba(255,255,255,0.2);}
.search-box {
    position:absolute;
    top:85px;
    right:70px;
    width:0;
    padding:8px 14px;
    border-radius:20px;
    border:none;
    outline:none;
    font-size:14px;
    opacity:0;
    transition:all 0.4s ease;
    background:rgba(255,255,255,0.1);
    color:#fff;
    backdrop-filter:blur(10px);
}
.search-box.active {
    width:200px;
    opacity:1;
}

/* Section */
.section {
    padding:15px 25px;
    font-size:17px;
    color:#ccc;
    text-shadow:0 0 10px rgba(0,255,255,0.3);
}

/* App Card */
.app {
    display:flex;
    align-items:center;
    justify-content:space-between;
    background:rgba(255,255,255,0.08);
    border:1px solid rgba(255,255,255,0.15);
    border-radius:24px;
    padding:16px 20px;
    margin:16px 20px;
    box-shadow:0 8px 25px rgba(0,0,0,0.6);
    backdrop-filter:blur(25px) saturate(200%);
    transition:all 0.35s ease;
}
.app:hover {
    transform:translateY(-6px) scale(1.03);
    box-shadow:0 12px 35px rgba(0,255,255,0.3);
}
.app img.icon {
    width:70px;
    height:70px;
    border-radius:18px;
    box-shadow:0 0 20px rgba(255,255,255,0.2);
    margin-right:16px;
}
.info {flex:1;display:flex;flex-direction:column;}
.info .name {font-size:18px;font-weight:600;color:#fff;}
.info .version {font-size:14px;color:#aaa;}

.install-btn {
    background:linear-gradient(135deg,#00e1ff,#0072ff);
    border:none;
    padding:10px 20px;
    border-radius:20px;
    font-size:14px;
    cursor:pointer;
    text-decoration:none;
    color:#fff;
    font-weight:600;
    letter-spacing:0.5px;
    box-shadow:0 0 10px rgba(0,114,255,0.4);
    transition:all 0.25s ease;
    overflow:hidden;
    position:relative;
}
    .install-btn:disabled{
    background:linear-gradient(135deg,#ff1e1e,#b30000) !important;
    box-shadow:0 0 16px rgba(255,30,30,0.9) !important;
    color:#fff !important;
    opacity:0.85;
    cursor:default;
}
.install-btn::after {
    content:"";
    position:absolute;
    left:-75%;
    top:0;width:50%;height:100%;
    background:rgba(255,255,255,0.2);
    transform:skewX(-20deg);
    transition:left .5s ease;
}
    .install-btn:disabled{
    background:linear-gradient(135deg,#ff1e1e,#b30000) !important;
    box-shadow:0 0 16px rgba(255,30,30,0.9) !important;
    color:#fff !important;
    opacity:0.85;
    cursor:default;
}
.install-btn{
    background:linear-gradient(135deg,var(--red-main),var(--red-dark));
    box-shadow:0 0 12px var(--red-glow);
}
.install-btn:hover{
    box-shadow:0 0 30px var(--red-glow);
}

/* Overlay Menu */
.overlay {
    display:none;
    position:fixed;
    top:0;left:0;
    width:100%;height:100%;
    background:rgba(0,0,0,0.7);
    backdrop-filter:blur(15px);
    z-index:9999;
    align-items:center;
    justify-content:center;
    animation:fadeIn 0.4s ease;
}
    .cat-btn {
    background:rgba(255,255,255,0.1);
    border:1px solid rgba(255,255,255,0.2);
    padding:8px 16px;
    border-radius:14px;
    color:#fff;
    cursor:pointer;
    font-size:14px;
    transition:0.3s;
}
.cat-btn{
    background:rgba(255,0,0,0.1);
    border:1px solid rgba(255,0,0,0.3);
}
    .cat-btn:hover{
    background:rgba(255,0,0,0.4);
}

@keyframes fadeIn {
    from {opacity:0;} to {opacity:1;}
}
    .category-bar {
    display: flex;
    gap: 10px;
    padding: 10px 20px;
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
    scrollbar-width: none; /* Firefox */
}
.category-bar::-webkit-scrollbar {
    display: none; /* Chrome */
}
.overlay-content{
    position:relative;
    background:rgba(0,0,0,0.85);
    border-radius:20px;
    padding:30px 40px;
    min-width:300px;
    text-align:center;
    box-shadow:0 0 35px var(--red-glow);
}
.close-btn {
    position:absolute;
    top:10px;right:15px;
    font-size:22px;
    color:#fff;
    cursor:pointer;
    transition:transform 0.2s;
}
.close-btn:hover {transform:scale(1.2);color:#00ffff;}
.powered {
    font-size:20px;
    font-weight:700;
color:var(--red-main);
    text-shadow:0 0 20px var(--red-glow);
    animation:glow 2s ease-in-out infinite;
}
@keyframes glow {
    0%,100% {text-shadow:0 0 10px #00e1ff, 0 0 20px #0072ff;}
    50% {text-shadow:0 0 25px #00ffff, 0 0 40px #00aaff;}
}

@media(max-width:480px){
    .header {font-size:20px;padding:18px}
    .app {flex-direction:column;align-items:flex-start;text-align:left;}
    .app img.icon{width:60px;height:60px;margin-bottom:10px;}
    .install-btn{align-self:flex-end;margin-top:10px;}
}
</style>
</head>
<body>

<canvas id="bg"></canvas>

<div class="header">
  <span class="title">Syline</span>
  <div style="display:flex;align-items:center;gap:10px;">
    <button type="button" class="search-btn" id="searchToggle">🔍</button>
    <img src="<?php echo $logo; ?>" class="logo" id="logoBtn" alt="logo">
  </div>
  <input type="text" id="searchBox" class="search-box" placeholder="Ara...">
</div>
<div class="category-bar">
    <button class="cat-btn" onclick="filterCategory('all')">All</button>
    <button class="cat-btn" onclick="filterCategory('king')">King Ios</button>
    <button class="cat-btn" onclick="filterCategory('oasis')">Oasis Ios</button>
    <button class="cat-btn" onclick="filterCategory('vn')">Vn Hax Ios</button>
    <button class="cat-btn" onclick="filterCategory('zoon')">Zoon Ios</button>
</div>
</div>

<div id="appList">
<?php foreach($apps as $index => $app): ?>
<div class="app" data-category="<?php echo $app['category']; ?>">
    <img src="<?php echo $app['icon']; ?>" class="icon">
    <div class="info">
        <div class="name"><?php echo $app['name']; ?></div>
        <div class="version"><?php echo $app['version']; ?></div>
    </div>
    <button class="install-btn" onclick="installApp('<?php echo $app['link']; ?>', this)">İnstall</button>
</div>
<?php endforeach; ?>
</div>

<!-- Overlay -->
<div class="overlay" id="menuOverlay">
  <div class="overlay-content">
    <div class="close-btn" id="closeOverlay">✖</div>
    <div class="powered">Powered By @mamisko / @sylinepubg </div>
  </div>
</div>

<script>
// neon background
const canvas=document.getElementById('bg');
const ctx=canvas.getContext('2d');
let w,h,particles=[];
function resize(){w=canvas.width=window.innerWidth;h=canvas.height=window.innerHeight;}
window.addEventListener('resize',resize);resize();
for(let i=0;i<30;i++){
 particles.push({x:Math.random()*w,y:Math.random()*h,r:Math.random()*100+50,dx:(Math.random()-.5)*0.5,dy:(Math.random()-.5)*0.5,color:`rgba(255,30,30,0.25)`});
}
function draw(){
 ctx.clearRect(0,0,w,h);
 for(let p of particles){
  const grad=ctx.createRadialGradient(p.x,p.y,0,p.x,p.y,p.r);
  grad.addColorStop(0,p.color);
  grad.addColorStop(1,'transparent');
  ctx.fillStyle=grad;
  ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);ctx.fill();
  p.x+=p.dx;p.y+=p.dy;
  if(p.x<0||p.x>w)p.dx*=-1;if(p.y<0||p.y>h)p.dy*=-1;
 }
 requestAnimationFrame(draw);
}
draw();

// search
const toggleBtn=document.getElementById("searchToggle");
const searchBox=document.getElementById("searchBox");
const apps=document.querySelectorAll(".app");
toggleBtn.addEventListener("click",()=>{searchBox.classList.toggle("active");if(searchBox.classList.contains("active"))searchBox.focus();});
searchBox.addEventListener("input",()=>{let f=searchBox.value.toLowerCase();apps.forEach(a=>{let n=a.querySelector(".name").textContent.toLowerCase();a.style.display=n.includes(f)?"flex":"none";});});

// install
function installApp(link,btn){
 btn.disabled=true;
 const originalText=btn.textContent;
 btn.textContent="Yükleniyor...";
 btn.style.background="linear-gradient(135deg,#00ff99,#007aff)";
 btn.style.boxShadow="0 0 12px rgba(0,255,153,0.7)";
 setTimeout(()=>{window.location.href=link;},1000);
 window.addEventListener("beforeunload",()=>{btn.textContent=originalText;btn.disabled=false;});
}
    

// overlay aç kapa
const logoBtn=document.getElementById("logoBtn");
const menuOverlay=document.getElementById("menuOverlay");
const closeOverlay=document.getElementById("closeOverlay");

logoBtn.addEventListener("click",()=>{menuOverlay.style.display="flex";});
closeOverlay.addEventListener("click",()=>{menuOverlay.style.display="none";});
menuOverlay.addEventListener("click",e=>{
  if(e.target===menuOverlay) menuOverlay.style.display="none";
});
    function filterCategory(cat) {
    const apps = document.querySelectorAll(".app");

    apps.forEach(app => {
        let c = app.getAttribute("data-category");

        if (cat === "all" || cat === c) {
            app.style.display = "flex";
        } else {
            app.style.display = "none";
        }
    });
}
</script>
</body>
</html>