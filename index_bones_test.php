<!DOCTYPE HTML>
<html>
<head>
<style>


.debug {
position:absolute;
display:none;
left:300px;
top:0px;
font-family:tahoma;
font-size:14px;
color:#ffffff;
}

.debug * {
height:18px;
line-height:18px;
}

.debug font {
display:inline-block;
width:50px;
text-align:right;
vertical-align:top;
}

.debug span {
display:inline-block;
width:50px;
text-align:right;
vertical-align:top;
}

</style>
</head>
<body style="margin:0px;overflow:hidden;background:#8A7E9B;">


<div id="project">


<table id="debug" class="debug" cellpadding="0px" cellspacing="0px">
<tr><td>????? ????????:</td><td id="tris"></td></tr>
<tr><td>??????? ??????:</td><td id="town"></td></tr>
<tr><td>?????????? ?????:</td><td id="snow_add"></td></tr>
<tr><td>??????? ?????:</td><td id="snow_fall"></td></tr>
<tr><td>???????? ?????:</td><td id="snow_fade"></td></tr>
<tr><td>????????:</td><td id="animations"></td></tr>
<tr><td>Javascript:</td><td id="javascript"></td></tr>
<tr><td>????????:</td><td id="renderer"></td></tr>
<tr><td>????????? ?????:</td><td id="frame"></td></tr>
</table>


<div id="hud" style="display:none;">
<div style="position:absolute;left:20px;bottom:20px;font-family:arial;font-size:30px;color:#ffff00;">+<span id="health">100</span></div>
</div>
<div id="loading" style="position:absolute;display:block;top:50%;width:100%;text-align:center;font-family:arial;font-size:40px;color:#ffffff;text-shadow:1px 1px 4px #393342;">????????? <span id="loading_amount"></span></div>
<div id="begin" onclick="this.style.display='none';last();" style="cursor:pointer;position:absolute;display:none;top:50%;width:100%;text-align:center;font-family:arial;font-size:40px;color:#ffffff;text-shadow:1px 1px 4px #393342;">?????</div>
<canvas id="canvas" width="800" height="600" style="background:#8A7E9B;vertical-align:top;"></canvas>
</div>


<audio id="music" preload>
<source src="sounds/silent_hill.mp3" type="audio/mpeg">
</audio>


<script type="text/javascript">
var vs=[]; // ?????????? ??????
var fs=[]; // ??????????? ??????
</script>


<script type="text/javascript" src="js_ru/libs/three_132.js"></script>
<script type="text/javascript" src="js_ru/libs/SkeletonUtils.js"></script>
<script type="text/javascript" src="js_ru/libs/FirstPersonControls.js"></script>
<script type="text/javascript" src="js_ru/libs/fflate.min.js"></script>
<script type="text/javascript" src="js_ru/intersection_ray_triangle.js"></script>
<script type="text/javascript" src="js_ru/cell.js"></script>
<script type="text/javascript" src="js_ru/stats.js"></script>
<script type="text/javascript" src="js_ru/OBJLoader.js"></script>
<script type="text/javascript" src="js_ru/init.js"></script>
<script type="text/javascript" src="js_ru/lights.js"></script>
<script type="text/javascript" src="js_ru/sounds.js"></script>
<script type="text/javascript" src="js_ru/loader.js"></script>
<script type="text/javascript" src="js_ru/renderer_stats.js"></script>
<script type="text/javascript" src="js_ru/FBXLoader.js"></script>


<script type="text/javascript">


"use strict";


//____________________ ?????????? ____________________


var debug_mode=true; // false - ?? ?????????? ??????????, true - ??????????
var debug_frame=true; // false - ?? ?????????? ?????????? JAVASCRIPT, ????????? ? ????????? ?????, true - ??????????
var debug_max_min=5; // ? ????? ?????????? ? ????????, ????????? ???????????? ? ??????????? ??????????? ?????
var debug_now=0.2; // ? ????? ?????????? ? ????????, ????????? ??????? ??????????? ?????
var debug_text=[];
var debug_fps_elapsed=0;
var debug_fps_now=0;
var debug_fps_last=0;


var debug=[]; // ?????? ??? ?????????? ???????????? ???????


if(!debug_mode){
var item=document.getElementById("debug").getElementsByTagName("tr");
var max=item.length;
for(var n=0;n<item.length;n++){
var html=item[n].innerHTML;
if(!html.match(/javascript/g) && !html.match(/renderer/g) && !html.match(/frame/g)){
item[n].remove();
n--;
}
}
}


if(!debug_frame){
var item=document.getElementById("debug").getElementsByTagName("tr");
var max=item.length;
for(var n=0;n<item.length;n++){
var html=item[n].innerHTML;
if(html.match(/javascript/g) || html.match(/renderer/g) || html.match(/frame/g)){
item[n].remove();
n--;
}
}
}


if(debug_mode || debug_frame){ document.getElementById("debug").style.display="table"; }


function debug_set(name){
debug[name]={};
debug[name].element=document.getElementById(name);
debug[name].end;
debug[name].elapsed_now=0;
debug[name].elapsed_max_min=0;
debug[name].max=0;
debug[name].min=100;
debug[name].now=0;
}


function debug_calc(name,arg){
var item=debug[name];
var end=Number((performance.now()-item.start).toFixed(3));
var frame=0;
if(name=="javascript" || name=="renderer" || name=="frame"){ frame=1; }
if(!debug_frame && frame==1){ return; }
if(!debug_mode && frame==0){ return; }
if(end>item.max){ item.max=end; }
if(end<item.min){ item.min=end; }
item.elapsed_max_min+=delta;
item.elapsed_now+=delta;
if(item.elapsed_max_min>debug_max_min){ item.elapsed_max_min=0; item.max=0.001; item.min=100; }
if(item.elapsed_now>debug_now){ item.elapsed_now=0; item.now=end; }
if(arg!=null){ var text="<font>["+arg+"]</font> "; }
else{ var text="<font></font> "; }
text+="max: <span>"+item.max.toFixed(3)+"</span> min: <span>"+item.min.toFixed(3)+"</span> now: <span>"+item.now.toFixed(3)+"</span>";
debug_text.push([name,text]);
}


debug_set("snow_add");
debug_set("snow_fall");
debug_set("snow_fade");
debug_set("animations");
debug_set("javascript");
debug_set("renderer");
debug_set("frame");


var canvas=document.getElementById("canvas");
var sens=1.5; // ???????????????? ???????? ? ????????????? ??????


var ways_9=[[-1,-1],[0,-1],[1,-1],[-1,0],[0,0],[1,0],[-1,1],[0,1],[1,1]];


var objects_list=[];
var town_cell_size=20;
var town_tris_size=1;


var camera_bottom=-0.5;
var camera_min_height=1;
var camera_max_height=2;
var camera_top=3;


var camera_town_x=-500;
var camera_town_z=-500;
var camera_town_pre_x=0;
var camera_town_pre_z=0;


var canvas_width=screen.width;
var canvas_height=screen.height;
canvas.width=canvas_width;
canvas.height=canvas_height;
var canvas_half_width=canvas_width/2;
var canvas_half_height=canvas_height/2;


var renderer_PixelRatio=window.devicePixelRatio;
var stop=1; // ???? ? ?????? ??????? loop();
var mouse=0;


var OBJLoader=new THREE.OBJLoader();
var FBXLoader=new THREE.FBXLoader();


if(debug_mode){
var stats=new Stats();
document.getElementById("project").appendChild(stats.dom);


document.getElementById("project").appendChild(renderer_stats_canvas);
}


var mat=[];
var mesh=[];
var mixers=[];
var mixer=[];
var action=[];
var clock=new THREE.Clock();
clock.autoStart=true;
var delta=0;
var trigger=[];


var origin_x=0;
var origin_y=0;
var origin_z=0;
var direction_x=0;
var direction_y=0;
var direction_z=0;
var distance=0;


var direction_2d_x=0;
var direction_2d_z=0;


var intersection_cell="";
var intersection_tris=[];


//var camera=new THREE.PerspectiveCamera(60,canvas_width/canvas_height,0.1,20);
var camera=new THREE.PerspectiveCamera(60,canvas_width/canvas_height,0.1,20000);
camera.position.set(185,camera_min_height,-235);


var camera_position=camera.position;


var scene_hud=new THREE.Scene();
var camera_hud=new THREE.OrthographicCamera(canvas_width/-2,canvas_width/2,canvas_height/2,canvas_height/-2,-1,1000000);
// ?????????? ?? Z, ????? ????? ??????? ??????? ?????? ? ???? ??????
camera_hud.position.z=100000;


var renderer=new THREE.WebGLRenderer({canvas:canvas,antialias:true,alpha:true,transparent:true,premultipliedAlpha:true,physicallyCorrectLights:false,logarithmicDepthBuffer:false});
renderer.setSize(canvas_width,canvas_height);
renderer.setPixelRatio(renderer_PixelRatio);
renderer.setClearColor(0xffffff);
renderer.autoClear=false;
renderer.shadowMap.enabled=false;
renderer.shadowMap.type=0;


var controls=new THREE.FirstPersonControls(camera,renderer.domElement);
controls.movementSpeed=4;
controls.lookSpeed=0.1;
controls.lookVertical=true;
controls.lon=-1.5*180/Math.PI;


var scene=new THREE.Scene();
var scene_children=scene.children;


if(debug_mode){
mesh["grid_20"]=new THREE.GridHelper(800,40,0x0000ff,0x00ff00);
mesh["grid_20"].position.set(0,-1,0);
scene.add(mesh["grid_20"]);
}


//____________________ ???????? ____________________


var maxanisotropy=renderer.capabilities.getMaxAnisotropy(); // ???????? ???????????


var tex=[];
var texture_loader=new THREE.TextureLoader(loadingManager);


tex["logo_text"]=texture_loader.load("images/logo_text.png");
tex["logo_end"]=texture_loader.load("images/logo_end.png");
tex["logo_noise"]=texture_loader.load("images/logo_noise.jpg");
tex["snow"]=texture_loader.load("images/snow.png");


tex["Harry_Mason"]=texture_loader.load("models/Harry_Mason/Harry_Mason.png");
tex["Cheryl_Mason"]=texture_loader.load("models/Cheryl_Mason/Cheryl_Mason.png");
tex["air_screamer"]=texture_loader.load("models/air_screamer/air_screamer.png");
tex["groaner"]=texture_loader.load("models/groaner/groaner.png");
tex["mumbler"]=texture_loader.load("models/mumbler/mumbler.png");


var images=["AMBCARH","SPR01F","SPR02F","SPR03F","SPR04F","SPR04H","SPR05F","SPR06F","SPR07F","SPR07H","SPR08F","SPR08H","SPR09F","SPR10F","SPR11F",
"SPR12F","SPR13F","SPR14F","SPR15F","SPR15H","SPR16F","SPR17F","SPR18F","SPR19F","SPR21F","SPR24F","SPR25F","SPR25H","SPR26F","SPR27F","SPRG1F",
"SPRG2F","SPRG3F","SPRG4F","THR0001F","THR0002F","THR0003F","THR0004F","THR0201F","THR0301F","THR0401F","THR0501F","THR0601F","THR0701F","THR0702H",
"THR0801F","THR0901F","THR1001F","THR1101F","THR1102H","THR1201F","THR1202H","THR1301F","THR1401F","THR1501F","THR1601F","THR1701F","THR1801F",
"THR1901F","THR2001F","THR2101F","THR2102H","THR2201F","THR2301F","THR2301H","THR2401F","THR2501F","THR2601F","THR2701F","THR2801F","THR2901F",
"THR3001F","THR3101F","THR3102H","THR3201F","THR3301F","THR3401F","THR3401H","THR3501F","THR3501H","THR3601F","THR3701F","THR3801F","THR3801H",
"THR3901F","THR4001F","THR4101F","THR4301F","THR4401F","THR4401H","THR4501F","THR4601F","THR4701F","THR4801F","THR4802H","THR5001F","THR5001H",
"THR5101F","THR5201F","THR5301F","THR5401F","THR5402H","THR5701F","THR5702H","THR5801F","THR5802H","THR5901F","THR6001F","THR6002H","THR6101F",
"THR6201F","THR6401F","THR6402H","THR6501F","THR6601F","THR6602H","THR6701F","THR6801F","THR6801H","THR6901F","THR6901H","THR7001F","THR7001H",
"THR7101F","THR7101H","THR7201F","THR7201H","THR7301F","THR7401F","THR7401H","THR7501F","THR7601F","THR7602H","THR7701F","THR7801F","THR7901F",
"THR7902H","THR8001F","THR8101F","THR8201F","THR8301F","THR8401F","THR8501F","THR8601F","THR9001F","THR9002H","THR9101F","THR9401F","THR9401H",
"THR9501F","THR9601F","THR9701F","THR9801F","THR9901F","THRA001F","THRA101F","THRA102H","THRA201F","THRA301F","THRA301H","THRA401F","THRA401H",
"THRA501F","THRA601F","THRA601H","THRA701F","THRA801F","THRA801H","THRA901F","THRB101F","THRB102H","THRB401F","THRB501F","THRB502H","THRB601F","THRB701F","THRC101F"];


for(var n=0;n<images.length;n++){
tex[images[n]]=texture_loader.load("models/town/images/"+images[n]+".png");
// ??? ???????????? ??????? ??? ? PLAYSTATION
//tex[images[n]].magFilter=THREE.NearestFilter;
//tex[images[n]].minFilter=THREE.LinearMipMapLinearFilter;
}


for(var n in tex){
manager_to_load++; // ???????????? ?????????? ??????? ??? ????????
}


//____________________ ???? ____________________


scene.background=new THREE.Color(0x8A7E9B);


for(var n=0;n<images.length;n++){
mat[images[n]]=new THREE.MeshStandardMaterial({
map:tex[images[n]],
alphaTest:0.98,
});
}


//____________________ ??????? ____________________


vs["logo"]=`


varying vec2 vUv;


void main(){
vUv=uv;
gl_Position=projectionMatrix*modelViewMatrix*vec4(position,1.0);
}`;


fs["logo"]=`


varying vec2 vUv;
uniform sampler2D map;
uniform sampler2D noise;
uniform float alpha;
uniform float size;
uniform float dissolve;
uniform vec3 color;


void main(){


vec4 diffuse=texture2D(map,vUv);
float n=texture2D(noise,vUv).x-dissolve;
if(n<0.0){ discard; }
if(n<size){ diffuse.rgb=color; }
gl_FragColor=diffuse;
gl_FragColor.a*=alpha;


}`;


mat["logo"]=new THREE.ShaderMaterial({
uniforms:{
map:{value:tex["logo_text"]},
noise:{value:tex["logo_noise"]},
alpha:{value:0},
size:{value:0.02},
dissolve:{value:0},
color:{value:new THREE.Color(0xffffff)}
},
vertexShader:vs["logo"],
fragmentShader:fs["logo"],
transparent:true,
depthTest:false,
depthWrite:false,
});


mesh["logo"]=new THREE.Mesh(new THREE.PlaneBufferGeometry(1.024,0.256,1),mat["logo"]);


mesh["logo"].frustumCulled=false;
mesh["logo"].position.z=-0.7;
mesh["logo"].updateMatrix();
mesh["logo"].matrixAutoUpdate=false;
mesh["logo"].updateMatrixWorld=function(){};
mesh["logo"].renderOrder=-1;
scene.add(mesh["logo"]);


// ____________________ HARRY MASON ____________________


other_to_load++;
FBXLoader.load('models/Harry_Mason/Harry_Mason.fbx',function(object){
mixer["Harry_Mason"]=new THREE.AnimationMixer(object);
action["Harry_Mason"]=mixer["Harry_Mason"].clipAction(object.animations[0]);
action["Harry_Mason"].play();
mixers.push(mixer["Harry_Mason"]);
mesh["Harry_Mason"]=object;
mesh["Harry_Mason"].children[1].material=new THREE.MeshBasicMaterial({map:tex["Harry_Mason"]});
mesh["Harry_Mason"].position.set(188.5,-0.58,-240);
mesh["Harry_Mason"].scale.set(0.05,0.05,0.05);
mesh["Harry_Mason"].children[1].frustumCulled=false;
mesh["Harry_Mason"].frustumCulled=false;
// ???????? ?????, ?????? ???????? ?? 10%. ??? ???? ?????????? ?????? ?????? ????????? ?????, ????????? ?????????????.
mesh["Harry_Mason"].traverse(function(child){
if(child.isBone){ child.visible=false; }
});
scene.add(mesh["Harry_Mason"]);
other_loaded++;
});


// ____________________ CHERYL MASON ____________________


other_to_load++;
FBXLoader.load('models/Cheryl_Mason/Cheryl_Mason.fbx',function(object){
mixer["Cheryl_Mason"]=new THREE.AnimationMixer(object);
action["Cheryl_Mason"]=mixer["Cheryl_Mason"].clipAction(object.animations[0]);
action["Cheryl_Mason"].timeScale=0.5;
action["Cheryl_Mason"].play();
mixers.push(mixer["Cheryl_Mason"]);
mesh["Cheryl_Mason"]=object;
mesh["Cheryl_Mason"].children[0].material=new THREE.MeshBasicMaterial({map:tex["Cheryl_Mason"]});
mesh["Cheryl_Mason"].position.set(165.5,-0.65,-167.7);
mesh["Cheryl_Mason"].rotation.y=-0.88
mesh["Cheryl_Mason"].scale.set(0.04,0.04,0.04);
// ???????? ?????, ?????? ???????? ?? 10%. ??? ???? ?????????? ?????? ?????? ????????? ?????, ????????? ?????????????.
mesh["Cheryl_Mason"].traverse(function(child){
if(child.isBone){ child.visible=false; }
});
scene.add(mesh["Cheryl_Mason"]);
other_loaded++;
});


// ____________________ AIR SCREAMER ____________________


other_to_load++;
FBXLoader.load('models/air_screamer/air_screamer.fbx',function(object){
mixer["air_screamer"]=new THREE.AnimationMixer(object);
action["air_screamer"]=mixer["air_screamer"].clipAction(object.animations[0]);
action["air_screamer"].timeScale=0.5;
action["air_screamer"].play();
mixers.push(mixer["air_screamer"]);
mesh["air_screamer"]=object;
mesh["air_screamer"].children[3].material=new THREE.MeshBasicMaterial({map:tex["air_screamer"]});
mesh["air_screamer"].position.set(47,-2,-165);
mesh["air_screamer"].rotation.y=1.57;
mesh["air_screamer"].scale.set(0.012,0.012,0.012);
// ???????? ?????, ?????? ???????? ?? 10%. ??? ???? ?????????? ?????? ?????? ????????? ?????, ????????? ?????????????.
mesh["air_screamer"].traverse(function(child){
if(child.isBone){ child.visible=false; }
});
scene.add(mesh["air_screamer"]);
other_loaded++;
});


trigger["air_screamer"]=new THREE.Mesh(new THREE.BoxBufferGeometry(10,5,26),new THREE.MeshBasicMaterial({color:0xffff00}));
trigger["air_screamer"].geometry.computeBoundingBox();
trigger["air_screamer"].material.wireframe=true;
trigger["air_screamer"].position.set(58,1.5,-160);
if(debug_mode){
scene.add(trigger["air_screamer"]);
}
var min=trigger["air_screamer"].geometry.boundingBox.min;
var max=trigger["air_screamer"].geometry.boundingBox.max;
var position=trigger["air_screamer"].position;
min.x+=position.x;
min.y+=position.y;
min.z+=position.z;
max.x+=position.x;
max.y+=position.y;
max.z+=position.z;
trigger["air_screamer"].min_x=min.x;
trigger["air_screamer"].min_y=min.y;
trigger["air_screamer"].min_z=min.z;
trigger["air_screamer"].max_x=max.x;
trigger["air_screamer"].max_y=max.y;
trigger["air_screamer"].max_z=max.z;
trigger["air_screamer"].activated=0;


// ____________________ GROANER JUMP AND RUN ____________________


other_to_load++;
FBXLoader.load('models/groaner/groaner.fbx',function(object){
mixer["groaner"]=new THREE.AnimationMixer(object);
action["groaner_jump_and_run_1"]=THREE.AnimationUtils.subclip(object.animations[0],'jump',0,28);
action["groaner_jump_and_run_1"]=mixer["groaner"].clipAction(action["groaner_jump_and_run_1"]);
action["groaner_jump_and_run_2"]=THREE.AnimationUtils.subclip(object.animations[0],'run',29,49);
action["groaner_jump_and_run_2"]=mixer["groaner"].clipAction(action["groaner_jump_and_run_2"]);
action["groaner_jump_and_run_2"].play();
mixers.push(mixer["groaner"]);
mesh["groaner_jump_and_run"]=object;
mesh["groaner_jump_and_run"].children[1].material=new THREE.MeshLambertMaterial({map:tex["groaner"]});
mesh["groaner_jump_and_run"].position.set(56,-1.2,-80);
mesh["groaner_jump_and_run"].rotation.y=3.14;
mesh["groaner_jump_and_run"].scale.set(0.02,0.02,0.02);
mesh["groaner_jump_and_run"].frustumCulled=false;
// ???????? ?????, ?????? ???????? ?? 10%. ??? ???? ?????????? ?????? ?????? ????????? ?????, ????????? ?????????????.
mesh["groaner_jump_and_run"].traverse(function(child){
if(child.isBone){ child.visible=false; }
});
scene.add(mesh["groaner_jump_and_run"]);
other_loaded++;
});


var groaner_jar_status=[];
var groaner_jar_status_n=0;
var groaner_jar_timer=0;


groaner_jar_status[0]=function(){
}


groaner_jar_status[1]=function(){
groaner_jar_timer+=delta;
if(groaner_jar_timer>2){
action["groaner_jump_and_run_2"].stop();
action["groaner_jump_and_run_1"].play();
groaner_jar_timer=0;
groaner_jar_status_n=2;
}
}


groaner_jar_status[2]=function(){
groaner_jar_timer+=delta;
if(groaner_jar_timer>0.88){
action["groaner_jump_and_run_1"].stop();
action["groaner_jump_and_run_2"].play();
groaner_jar_timer=0;
groaner_jar_status_n=3;
}
}


groaner_jar_status[3]=function(){
mesh["groaner_jump_and_run"].position.x+=delta*1;
}


trigger["groaner_jump_and_run"]=new THREE.Mesh(new THREE.BoxBufferGeometry(10,5,5),new THREE.MeshBasicMaterial({color:0xffff00}));
trigger["groaner_jump_and_run"].geometry.computeBoundingBox();
trigger["groaner_jump_and_run"].material.wireframe=true;
trigger["groaner_jump_and_run"].position.set(58,1.5,-100);
if(debug_mode){
scene.add(trigger["groaner_jump_and_run"]);
}
var min=trigger["groaner_jump_and_run"].geometry.boundingBox.min;
var max=trigger["groaner_jump_and_run"].geometry.boundingBox.max;
var position=trigger["groaner_jump_and_run"].position;
min.x+=position.x;
min.y+=position.y;
min.z+=position.z;
max.x+=position.x;
max.y+=position.y;
max.z+=position.z;
trigger["groaner_jump_and_run"].min_x=min.x;
trigger["groaner_jump_and_run"].min_y=min.y;
trigger["groaner_jump_and_run"].min_z=min.z;
trigger["groaner_jump_and_run"].max_x=max.x;
trigger["groaner_jump_and_run"].max_y=max.y;
trigger["groaner_jump_and_run"].max_z=max.z;
trigger["groaner_jump_and_run"].activated=0;


// ____________________ MUMBLER ____________________


other_to_load++;
FBXLoader.load('models/mumbler/mumbler.fbx',function(object){
mixer["mumbler"]=new THREE.AnimationMixer(object);
action["mumbler"]=mixer["mumbler"].clipAction(object.animations[0]);
action["mumbler"].timeScale=0.5;
action["mumbler"].play();
mixers.push(mixer["mumbler"]);
mesh["mumbler"]=object;
mesh["mumbler"].children[1].material=new THREE.MeshBasicMaterial({map:tex["mumbler"]});
mesh["mumbler"].position.set(133,-1.3,-7);
mesh["mumbler"].rotation.y=2.6;
mesh["mumbler"].scale.set(0.06,0.06,0.06);
// ???????? ?????, ?????? ???????? ?? 10%. ??? ???? ?????????? ?????? ?????? ????????? ?????, ????????? ?????????????.
mesh["mumbler"].traverse(function(child){
if(child.isBone){ child.visible=false; }
});
scene.add(mesh["mumbler"]);
other_loaded++;
});


// ____________________ GROANER ATTACK ____________________


mat["groaner_attack"]=new THREE.MeshLambertMaterial({map:tex["groaner"]});


other_to_load++;


FBXLoader.load('models/groaner/groaner.fbx',function(object){


mesh["groaner_attack"]=object;
// ????????? ???????? ????????, ????? ????? ????? ???? ??? ?????? ????? ?? ???? ????????
// ?????????? ??????? ????????????? ? ?????????? ??????????, ?? ?? ?????? ??? ????? ?? ??????????,
// ?.?. ? ?????? ???????? ????? ???? ????????, ? ????????? ??? ?????? ?? ?Ũ
// ???? ????? ?????? ????????, ?? ??? ??????? ???? ????????? ????? ???????? ? ??????? ???????????
// ???? ? ????? ????????? ????? ???? ????????, ?? ???? ?? ???̨? ??????, ? ?????? ?????????? ?????????? ??????? ? ?????????? ??????????
mesh["groaner_attack"].children[1].material=mat["groaner_attack"];


// ???????? ?????, ?????? ???????? ?? 10%. ??? ???? ?????????? ?????? ?????? ????????? ?????, ????????? ?????????????.
mesh["groaner_attack"].traverse(function(child){
if(child.isBone){ child.visible=false; }
});



for(var n=0;n<200;n++){
mesh["groaner_attack_"+n]=THREE.SkeletonUtils.clone(mesh["groaner_attack"]);
mesh["groaner_attack_"+n].scale.set(0.02,0.02,0.02);
mesh["groaner_attack_"+n].position.set(190+n*0.3,-0.58,-240);
mesh["groaner_attack_"+n].animations=mesh["groaner_attack"].animations;
mixer["groaner_attack_"+n]=new THREE.AnimationMixer(mesh["groaner_attack_"+n]);
action["groaner_attack_"+n]=THREE.AnimationUtils.subclip(mesh["groaner_attack_"+n].animations[0],'run',153,173);
action["groaner_attack_"+n]=mixer["groaner_attack_"+n].clipAction(action["groaner_attack_"+n]);
mesh["groaner_attack_"+n].animations=[];
action["groaner_attack_"+n].time=Math.random()*action["groaner_attack_"+n]._clip.duration;
action["groaner_attack_"+n].play();
//mesh["groaner_attack_"+n].updateMatrixWorld=function(){};

/*
delete mesh["groaner_attack_"+n].children[1].position;
mesh["groaner_attack_"+n].children[1].position=mesh["groaner_attack_"+n].position;


delete mesh["groaner_attack_"+n].children[1].matrix;
mesh["groaner_attack_"+n].children[1].matrix=mesh["groaner_attack_"+n].matrix;


delete mesh["groaner_attack_"+n].children[1].matrixWorld;
mesh["groaner_attack_"+n].children[1].matrixWorld=mesh["groaner_attack_"+n].matrixWorld;
mesh["groaner_attack_"+n].children[1].parent=null;
*/

//mesh["groaner_attack_"+n].children[1].updateMatrixWorld=function(){};


mixers.push(mixer["groaner_attack_"+n]);
scene.add(mesh["groaner_attack_"+n]);
}


other_loaded++;
});


// ____________________ TOWN ____________________


var town_text="";


var town_obj_total=0;
var town_tris_total=0;
var town_tris_cell_c=0;
var town_tris_cell_n=0;
var town_tris_cell=[];
var town_tris_a=new Float32Array(10000000);
var town_tris_n=0;
var town_left=Infinity;
var town_right=-Infinity;
var town_top=Infinity;
var town_bottom=-Infinity;
var town_cell=[];
var town_children=[];
var town=new THREE.Group;
scene.add(town);


//other_to_load++;


OBJLoader.load("models/air_screamer/air_screamer.obj",function(object){

other_loaded++; return;

var town_pre_cell=[];


while(object.children.length){


var name=object.children[0].name;
mesh[name]=object.children[0];
mesh[name].geometry.computeBoundingBox();
mesh[name].matrixAutoUpdate=false;
mesh[name].updateMatrixWorld=function(){};
mesh[name].frustumCulled=false;


mesh[name].onAfterRender=function(){
this.frustumCulled=true;
scene.remove(this);
this.onAfterRender=function(){};
}


// ____________________ ????????? ????? ?? ??????? ? ????????? ____________________


var item=mesh[name].geometry.boundingBox;
var bmin=item.min;
var bmax=item.max;
// ????????? ????? floor ?????, ? ?? ceil ??? ?????? ??????? ? ceil ??? ????.
// ??????: ?????? ??????? 50. ????? ????? 0, ?????? ????? 51.2. ??? floor ???̨? ??????? 0 ? 1
// ???? ??? ?????? ????? ???????????? ceil, ?? ???̨? 0, 1 ? 2, ? ??? ??? ??????. ???? ?????? ????? 51.2, ? ??? ????? ?? 3 ???????.
var left=Math.floor(bmin.x/town_cell_size);
var right=Math.floor(bmax.x/town_cell_size);
var top=Math.floor(bmin.z/town_cell_size);
var bottom=Math.floor(bmax.z/town_cell_size);


if(left<town_left){ town_left=left; }
if(right>town_right){ town_right=right; }
if(top<town_top){ town_top=top; }
if(bottom>town_bottom){ town_bottom=bottom; }


for(var x=left;x<=right;x++){
for(var z=top;z<=bottom;z++){
var cell_name=x+"_"+z;
if(town_pre_cell[cell_name]==undefined){ town_pre_cell[cell_name]=[]; }
town_pre_cell[cell_name].push(name);
}
}


// ____________________ ????????? ????? ?? ??????? ? ?????????????? ____________________


var item=mesh[name].geometry.attributes.position.array;
var max=mesh[name].geometry.attributes.position.count*3;


town_obj_total++;
town_tris_total+=max/9;


for(var n=0;n<max;n+=9){


var left=Infinity;
var right=-Infinity;
var top=Infinity;
var bottom=-Infinity;


town_tris_a[town_tris_n]=item[n];
town_tris_a[town_tris_n+1]=item[n+1];
town_tris_a[town_tris_n+2]=item[n+2];
town_tris_a[town_tris_n+3]=item[n+3];
town_tris_a[town_tris_n+4]=item[n+4];
town_tris_a[town_tris_n+5]=item[n+5];
town_tris_a[town_tris_n+6]=item[n+6];
town_tris_a[town_tris_n+7]=item[n+7];
town_tris_a[town_tris_n+8]=item[n+8];


// ?????? ?????
if(item[n]<left){ left=item[n]; }
if(item[n]>right){ right=item[n]; }
if(item[n+2]<top){ top=item[n+2]; }
if(item[n+2]>bottom){ bottom=item[n+2]; }
// ?????? ?????
if(item[n+3]<left){ left=item[n+3]; }
if(item[n+3]>right){ right=item[n+3]; }
if(item[n+5]<top){ top=item[n+5]; }
if(item[n+5]>bottom){ bottom=item[n+5]; }
// ?????? ?????
if(item[n+6]<left){ left=item[n+6]; }
if(item[n+6]>right){ right=item[n+6]; }
if(item[n+8]<top){ top=item[n+8]; }
if(item[n+8]>bottom){ bottom=item[n+8]; }


// ????????? ????? floor ?????, ? ?? ceil ??? ?????? ??????? ? ceil ??? ????.
// ??????: ?????? ??????? 50. ????? ????? 0, ?????? ????? 51.2. ??? floor ???̨? ??????? 0 ? 1
// ???? ??? ?????? ????? ???????????? ceil, ?? ???̨? 0, 1 ? 2, ? ??? ??? ??????. ???? ?????? ????? 51.2, ? ??? ????? ?? 3 ???????.
left=Math.floor(left/town_tris_size);
right=Math.floor(right/town_tris_size);
top=Math.floor(top/town_tris_size);
bottom=Math.floor(bottom/town_tris_size);


for(var x=left;x<=right;x++){
for(var z=top;z<=bottom;z++){
var cell_name=x+"_"+z;
if(town_tris_cell[cell_name]==undefined){ town_tris_cell[cell_name]=[]; town_tris_cell_c++; }
town_tris_cell[cell_name].push(town_tris_n);
town_tris_cell_n++;
}
}


town_tris_n+=9;


}


scene.add(mesh[name]);


}


if(debug_mode){
document.getElementById("tris").innerHTML=town_obj_total+" ????? ?????????????: "+town_tris_total+" ????? ????? "+town_tris_cell_c+" ??????? ????? "+town_tris_cell_n;
}


// ____________________ ?????????? ??????? ?? ??????? ? ??????? 9 ???????? ____________________


for(var x=town_left-1;x<=town_right+1;x++){
for(var z=town_top-1;z<=town_bottom+1;z++){


var merge_name=x+"_"+z;
var found=0;


town_cell[merge_name]=[];
town_children[merge_name]=[];
var town_items_added_list=[];


for(var i=0;i<9;i++){


var cell_name=(x+ways_9[i][0])+"_"+(z+ways_9[i][1]);


if(town_pre_cell[cell_name]==undefined){ continue; }


found=1;
var max=town_pre_cell[cell_name].length;


for(var n=0;n<max;n++){


var item_name=town_pre_cell[cell_name][n];
if(town_items_added_list[item_name]==undefined){
town_cell[merge_name].push(town_pre_cell[cell_name][n]);
town_children[merge_name].push(mesh[town_pre_cell[cell_name][n]]);
town_items_added_list[item_name]=1;
}


}
}


// ???? ?????? ?????? ???, ?? ???????, ????? ?? ???????? ??????
if(found==0){
delete town_cell[merge_name];
delete town_children[merge_name]
}


}
}


other_loaded++;


});


// ____________________ ?????????? ???? ?????? ??????? ?? 9 ???????? ____________________


/*
function town_objects2(){


for(var i=0;i<9;i++){


var cell_name=(camera_town_pre_x+ways_9[i][0])+"_"+(camera_town_pre_z+ways_9[i][1]);


if(town_cell[cell_name]==undefined){ continue; }


var max_objects=town_cell[cell_name].length;


for(var n=0;n<max_objects;n++){


scene.remove(mesh[town_cell[cell_name][n]]);


}


}


}


function town_objects_old(){


var max=objects_list.length;
for(var n=0;n<max;n++){
scene.remove(objects_list[n]);
}


var name=camera_town_pre_x+"_"+camera_town_pre_z;


if(town_cell[name]!=undefined){
var max=town_cell[name].length;
var item=[];
for(var n=0;n<max;n++){
item.push(mesh[town_cell[name][n]]);
scene.add(mesh[town_cell[name][n]]);
}
//scene.add(item);
objects_list=item;
}
else{
objects_list=[];
}


camera_town_x=camera_town_pre_x;
camera_town_z=camera_town_pre_z;


}


function town_objects77(){


var objects_new=[];


var town_cell_obj=town_cell[camera_town_pre_x+"_"+camera_town_pre_z];


if(town_cell_obj!=undefined){


var max=town_cell_obj.length;


for(var n=0;n<max;n++){


var name=town_cell_obj[n];
objects_new[name]=1;


if(objects_list[name]==undefined){
// ????????? ????????
scene_children.push(mesh[name]);
}
else{
delete objects_list[name];
}


}


}


for(var i in objects_list){
// ??????? ????????
scene_children.splice(scene_children.indexOf(mesh[i]),1);
}


objects_list=objects_new;


camera_town_x=camera_town_pre_x;
camera_town_z=camera_town_pre_z;


}


*/


function town_objects(){


var start=performance.now();
 
 
camera_town_pre_x=Math.floor(camera_position.x/town_cell_size);
camera_town_pre_z=Math.floor(camera_position.z/town_cell_size);


if(camera_town_x==camera_town_pre_x && camera_town_z==camera_town_pre_z){ return; }



var town_children_obj=town_children[camera_town_pre_x+"_"+camera_town_pre_z];


if(town_children_obj!=undefined){ town.children=town_children_obj; }
else{ town.children=[]; }


camera_town_x=camera_town_pre_x;
camera_town_z=camera_town_pre_z;


var end=(performance.now()-start).toFixed(3);


if(debug_mode){
var count=0;
if(town_children_obj!=undefined){ count=town_children_obj.length; }
document.getElementById("town").innerHTML="<font>["+count+"]</font> "+end;
}


}


// ____________________ ???? ____________________


var snow_fall=[]; // ?????? ???????? ????????
var snow_fall_radius_1=16; // ? ????? ??????? ?? ?????? ?????? ????????? ????????
var snow_fall_radius_2=4; // ? ????? ??????? ???????? ??????????? ??????? ????????. ??? ??????, ??? ?????? ???????? ???? ???????
var snow_fall_top=3; // ? ????? ?????? ?????? ????????
var snow_fall_bottom=-5.0; // ?? ???? ???????? ?? ?????? ? ???????? ????? ?????? ????????
var snow_fall_speed=5; // ???????? ??????? ?????? ? ???????
var snow_fall_offset=0.02; // ?? ??????? ?????? ?????????? ???????? ?? ???????????
var snow_add_m=0; // 0 - ?? ????????? ????, 1 - ?????????
var snow_add_time=0.002; // ????? ??????? ?????????? ????????? ????? ????????
var snow_add_time_n=0; // ?? ??????
var snow_fade=[]; // ?????? ??????? ????????
var snow_fade_time=1.5; // ????? ??????? ?????? ??????? ??????? ????????


vs["snow_fall"]=`


varying float fogDepth;


void main(){


vec4 mvPosition=modelViewMatrix*vec4(position,1.0);
fogDepth=-mvPosition.z;
gl_PointSize=(100.0/fogDepth);
gl_Position=projectionMatrix*mvPosition;


}`;


fs["snow_fall"]=`


uniform sampler2D map;
varying float fogDepth;
float fogDensity=-0.14*0.14;
vec3 fogColor=vec3(0.5411,0.4941,0.6078);


void main(){


gl_FragColor=texture2D(map,gl_PointCoord);
if(gl_FragColor.a<0.02){ discard; }
float fogFactor=1.0-exp(fogDensity*fogDepth*fogDepth);
gl_FragColor.rgb=mix(gl_FragColor.rgb,fogColor,fogFactor);


}`;


mat["snow_fall"]=new THREE.ShaderMaterial({
uniforms:{
map:{value:tex["snow"]}
},
vertexShader:vs["snow_fall"],
fragmentShader:fs["snow_fall"],
transparent:true,
});


var geometry=new THREE.BufferGeometry();
geometry.setAttribute("position",new THREE.BufferAttribute(new Float32Array(),3));
mesh["snow_fall"]=new THREE.Points(geometry,mat["snow_fall"]);
mesh["snow_fall"].matrixAutoUpdate=false;
mesh["snow_fall"].updateMatrixWorld=function(){};
mesh["snow_fall"].frustumCulled=false;
scene.add(mesh["snow_fall"]);


vs["snow_fade"]=`


precision highp float;
attribute vec4 position;
varying float fogDepth;
varying float vFade;
uniform mat4 modelViewMatrix;
uniform mat4 projectionMatrix;


void main(){


vFade=position.w;
vec4 mvPosition=modelViewMatrix*vec4(position.xyz,1.0);
fogDepth=-mvPosition.z;
gl_PointSize=(100.0/fogDepth);
gl_Position=projectionMatrix*mvPosition;


}`;


fs["snow_fade"]=`


precision highp float;
uniform sampler2D map;
varying float fogDepth;
varying float vFade;
float fogDensity=-0.14*0.14;
vec3 fogColor=vec3(0.5411,0.4941,0.6078);


void main(){


gl_FragColor=texture2D(map,gl_PointCoord);
if(gl_FragColor.a<0.02){ discard; }
float fogFactor=1.0-exp(fogDensity*fogDepth*fogDepth);
gl_FragColor.rgb=mix(gl_FragColor.rgb,fogColor,fogFactor);
gl_FragColor.a*=vFade;


}`;


mat["snow_fade"]=new THREE.RawShaderMaterial({
uniforms:{
map:{value:tex["snow"]},
modelViewMatrix:{value:camera.projectionMatrix},
projectionMatrix:{value:camera.projectionMatrix},
},
vertexShader:vs["snow_fade"],
fragmentShader:fs["snow_fade"],
transparent:true,
});


var geometry=new THREE.BufferGeometry();
geometry.setAttribute("position",new THREE.BufferAttribute(new Float32Array(),4));
mesh["snow_fade"]=new THREE.Points(geometry,mat["snow_fade"]);
mesh["snow_fade"].matrixAutoUpdate=false;
mesh["snow_fade"].updateMatrixWorld=function(){};
mesh["snow_fade"].frustumCulled=false;
scene.add(mesh["snow_fade"]);


// ____________________ ?????????? ????? ____________________


function snow_add(){


// ????? ??????? ?????????? ?????? ????? ?????? ????,
// ?????? ?????? ??????? ?????????? ? ????? ???????? ? ???????? ?? ??? ????????? ?????????? X ? Z. ? ?????? ?????? ????
var radius_1=snow_fall_radius_1*Math.sqrt(Math.random());
var theta=2*Math.PI*Math.random();
var x_1=camera_position.x+radius_1*Math.cos(theta);
var y_1=snow_fall_top;
var z_1=camera_position.z+radius_1*Math.sin(theta);


// ?????? ????????? 2D ????????? ??????? ?٨ ???? ?????? ? ??????????? ? ????


var radius_2=snow_fall_radius_2*Math.sqrt(Math.random());
var theta=2*Math.PI*Math.random();
var x_2=x_1+radius_2*Math.cos(theta);
var y_2=snow_fall_bottom;
var z_2=z_1+radius_2*Math.sin(theta);


// ??????? 3D ?????? ? ????????? 2D ???????????, ??????? ?????????? ??????
direction_x=x_2-x_1;
direction_y=y_2-y_1;
direction_z=z_2-z_1;


// ??????????? ??????
var divide=1/Math.sqrt(direction_x*direction_x+direction_y*direction_y+direction_z*direction_z);
direction_x*=divide;
direction_y*=divide;
direction_z*=divide;


// ??????? ??????????? ?? ????????


direction_2d_x=x_2-x_1;
direction_2d_z=z_2-z_1;


var divide=1/Math.sqrt(direction_2d_x*direction_2d_x+direction_2d_z*direction_2d_z);
direction_2d_x*=divide;
direction_2d_z*=divide;


origin_x=x_1;
origin_y=snow_fall_top;
origin_z=z_1;
distance=snow_fall_radius_2;


var result=cells_find();
if(result!=0){ result-=snow_fall_offset; x_2=origin_x+direction_x*result; y_2=origin_y+direction_y*result; z_2=origin_z+direction_z*result; }


// ????????? ???????, ???????? ???????, ?????? ????????, ??????? ?????? ??????????? ? ??????? ????????
snow_fall.push([x_1,y_1,z_1,x_2,y_2,z_2,direction_x*snow_fall_speed,direction_y*snow_fall_speed,direction_z*snow_fall_speed,0]);


}


// ____________________ ??????? ????? ____________________


function snow_fall_update(){


var max=snow_fall.length;
var alive=new Array(max);
var pre=new Float32Array(max*3);
var i=0;
var j=0;


for(var n=0;n<max;n++){


var item=snow_fall[n];
item[9]+=delta;
var t=item[9];
var y=item[1]+item[7]*t;


// ???? ???? ???????? ???????????, ?? ??????? ??? ?? ??????? ????????? ????? ? ????????? ? ?????? ???????? ?????
// ????? ??? ??????? ?? ?????????? now.splice(n,1); n--; max--; ?????? ?????? ????????? ?????????????? ? ???????? ?????. ??????? ????? ?????? ??? ????????????? ??????
// ??? 5000 ????????? splice ????????? 5000 ??? ? ???̨? 42??, ? ???????????? ??????? alive.push(item); ????? 0.12??, ??? ? 350 ??? ???????
// ???? ? ??????? ????? ???? ??? ????????????? splice, ?? ?٨ ?????????, ?? ??? ??? ????? ???????? 3-5 ????????, ?? ??? ??? ????? ???????????? ???????


if(y>item[4]){
alive[i]=item;
i++;
pre[j]=item[0]+item[6]*t;
pre[j+1]=y;
pre[j+2]=item[2]+item[8]*t;
j+=3;
}
else{
snow_fade.push([item[3],item[4],item[5],snow_fade_time]);
}


}


alive.length=i;
snow_fall=alive;


mesh["snow_fall"].geometry.attributes.position=new THREE.BufferAttribute(pre,3);


}


// ____________________ ???????? ????? ____________________


function snow_fade_update(){


var max=snow_fade.length;
var alive=new Array(max);
var pre=new Float32Array(max*4);
var i=0;
var j=0;


for(var n=0;n<max;n++){


var item=snow_fade[n];
item[3]-=delta;
var t=item[3];


if(t>0){
alive[i]=item;
i++;
pre[j]=item[0];
pre[j+1]=item[1];
pre[j+2]=item[2];
pre[j+3]=t/snow_fade_time;
j+=4;
}


}


alive.length=i;
snow_fade=alive;


mesh["snow_fade"].geometry.attributes.position=new THREE.BufferAttribute(pre,4);
// ????????????? ????????????, ????? ?? ???? ??????? FPS ??? ???????? ?????? ? ???
mesh["snow_fade"].geometry.attributes.position.setUsage(THREE.DynamicDrawUsage);


}


// ____________________ ?????? ????? ____________________


if(debug_mode){


var circleRadius=snow_fall_radius_1;
var circleShape=new THREE.Shape().moveTo(0,circleRadius)
.quadraticCurveTo(circleRadius,circleRadius,circleRadius,0)
.quadraticCurveTo(circleRadius,-circleRadius,0,-circleRadius)
.quadraticCurveTo(-circleRadius,-circleRadius,-circleRadius,0)
.quadraticCurveTo(-circleRadius,circleRadius,0,circleRadius);
circleShape.autoClose=true;


var points=circleShape.getPoints();
var max=points.length;
// ??????????????
for(var n=0;n<max;n++){
points[n].z=points[n].y;
points[n].y=0;
}
var geometry=new THREE.BufferGeometry().setFromPoints(points);


mesh["snow_circle"]=new THREE.Line(geometry,new THREE.LineBasicMaterial({color:0xffffff}));
mesh["snow_circle"].frustumCulled=false;
mesh["snow_circle"].position.x=camera_position.x;
mesh["snow_circle"].position.y=-1;
mesh["snow_circle"].position.z=camera_position.z;
scene.add(mesh["snow_circle"]);


}


var logo_status=[];
var logo_status_n=0;
var logo_timer=0;


logo_status[0]=function(){}


logo_status[1]=function(){
logo_timer+=delta;
if(logo_timer>7){
snow_add_m=1;
logo_status_n=2;
logo_timer=0;
}
}


logo_status[2]=function(){
logo_timer+=delta;
if(logo_timer>2){
document.getElementById('music').play();
logo_timer=0;
logo_status_n=3;
}
}


logo_status[3]=function(){
logo_timer+=delta;
if(logo_timer>4){
logo_timer=0;
logo_status_n=4;
}
}


logo_status[4]=function(){
mesh["logo"].material.uniforms.alpha.value+=delta/40;
if(mesh["logo"].material.uniforms.alpha.value>=0.2){
mesh["logo"].material.uniforms.alpha.value=0.2;
logo_status_n=5;
}
}


logo_status[5]=function(){
logo_timer+=delta;
if(logo_timer>4){
logo_timer=0;
logo_status_n=6;
}
}


logo_status[6]=function(){
mesh["logo"].material.uniforms.dissolve.value+=delta/3;
if(mesh["logo"].material.uniforms.dissolve.value>=1){
mesh["logo"].material.uniforms.dissolve.value=1;
mesh["logo"].material.uniforms.map.value=tex["logo_end"];
mesh["logo"].position.x=-100;
mesh["logo"].updateMatrix();
logo_status_n=7;
}
}


logo_status[7]=function(){
logo_timer+=delta;
if(logo_timer>0.5){
logo_timer=0;
logo_status_n=8;
}
}


logo_status[8]=function(){
scene.fog.density-=delta;
if(scene.fog.density<=0.14){
scene.fog.density=0.14;
logo_status_n=9;
mouse=1;
go=1;
clock.start();
}
}


logo_status[9]=function(){
logo_timer+=delta;
if(logo_timer>1000){
logo_timer=0;
mesh["logo"].position.x=0;
mesh["logo"].updateMatrix();
mesh["logo"].material.uniforms.alpha.value=0;
mesh["logo"].material.uniforms.dissolve.value=0;
logo_status_n=10;
}
}


logo_status[10]=function(){
scene.fog.density+=delta/4;
if(scene.fog.density>=2){
scene.fog.density=2;
}
mesh["logo"].material.uniforms.alpha.value+=delta/60;
if(mesh["logo"].material.uniforms.alpha.value>=0.2){
mesh["logo"].material.uniforms.alpha.value=0.2;
}
if(scene.fog.density>=2 && mesh["logo"].material.uniforms.alpha.value>=0.2){
snow_add_m=0;
logo_status_n=11;
}
}


logo_status[11]=function(){
mesh["logo"].material.uniforms.dissolve.value+=delta/3;
if(mesh["logo"].material.uniforms.dissolve.value>=1){
mesh["logo"].material.uniforms.dissolve.value=1;
mesh["logo"].material.uniforms.map.value=tex["logo_end"];
logo_status_n=0;
}
}


function last(){
fullscreen();
canvas.requestPointerLock();
logo_status_n=1;
}


// ____________________ ????????? ____________________


var at=0;
var go=0;


function loop(){


requestAnimationFrame(loop);


delta=clock.getDelta();


// ??????????? ????? ?????????, ????? ?? ??????????? ? ????? ??????? ??????????
if(debug_mode){ stats.update(); }


debug["javascript"].start=performance.now();
debug["frame"].start=performance.now();
debug_fps_elapsed+=delta;
if(debug_fps_elapsed>0.5){ debug_fps_elapsed=0; debug_fps_now=(1000/(performance.now()-debug_fps_last)).toFixed(2); }
debug_fps_last=performance.now();


if(stop==1){ return; }


logo_status[logo_status_n]();


if(debug_mode){
mesh["snow_circle"].position.x=camera_position.x;
mesh["snow_circle"].position.z=camera_position.z;
}


// ____________________ ?????????? ????? ____________________


// ?????? ?Ĩ? ?????????? ?????, ? ????? ???????, ????? ????????? ???????? ?????? ????????? ?????? ?????????? ?? ?????? ??????? FPS, ?????????


debug["snow_add"].start=performance.now();


var add=0;


if(snow_add_m==1){


snow_add_time_n+=delta;
add=Math.floor(snow_add_time_n/snow_add_time);
snow_add_time_n-=add*snow_add_time;
// ???? ??????? ?????????
if(add>0.016/snow_add_time*60*2){ snow_add_time_n=0; add=0; }


for(var n=0;n<add;n++){
snow_add();
}


}


debug_calc("snow_add",add);


// ____________________ ??????? ????? ____________________


debug["snow_fall"].start=performance.now();


snow_fall_update();


debug_calc("snow_fall",snow_fall.length);


// ____________________ ???????? ????? ____________________


debug["snow_fade"].start=performance.now();


snow_fade_update();


debug_calc("snow_fade",snow_fade.length);


// ____________________ ?????????? ???? ?????? ??????? ?? 9 ???????? ____________________


town_objects();


if(go==1){


controls.update(delta);


if(camera_position.y<camera_bottom){ camera_position.y=camera_bottom; }
if(camera_position.y<camera_min_height){ camera_position.y+=(camera_min_height-camera_position.y)*0.02; }
if(camera_position.y>camera_top){ camera_position.y=camera_top; }
if(camera_position.y>camera_max_height){ camera_position.y-=(camera_position.y-camera_max_height)*0.02; }


at+=delta;
if(at>1.){ at=at-1.; mesh["Harry_Mason"].position.z+=3.1; }


if(trigger["air_screamer"].activated==0 &&
camera_position.x>=trigger["air_screamer"].min_x && camera_position.x<=trigger["air_screamer"].max_x &&
camera_position.y>=trigger["air_screamer"].min_y && camera_position.y<=trigger["air_screamer"].max_y &&
camera_position.z>=trigger["air_screamer"].min_z && camera_position.z<=trigger["air_screamer"].max_z
){
trigger["air_screamer"].activated=1;
}


if(trigger["air_screamer"].activated==1){


mesh["air_screamer"].position.x+=0.015;


var height=Math.sin(clock.elapsedTime*5);
if(height<0){ height/=200; }
else{
height/=26;
}
mesh["air_screamer"].position.y+=height;


}


if(trigger["groaner_jump_and_run"].activated==0 &&
camera_position.x>=trigger["groaner_jump_and_run"].min_x && camera_position.x<=trigger["groaner_jump_and_run"].max_x &&
camera_position.y>=trigger["groaner_jump_and_run"].min_y && camera_position.y<=trigger["groaner_jump_and_run"].max_y &&
camera_position.z>=trigger["groaner_jump_and_run"].min_z && camera_position.z<=trigger["groaner_jump_and_run"].max_z
){
trigger["groaner_jump_and_run"].activated=1;
groaner_jar_status_n=1;
}


if(trigger["groaner_jump_and_run"].activated==1){


mesh["groaner_jump_and_run"].position.z-=delta*5;


groaner_jar_status[groaner_jar_status_n]();


}


debug["animations"].start=performance.now();


var max=mixers.length;


for(var n=0;n<max;n++){
mixers[n].update(delta);
}


debug_calc("animations",max);


}


// ?????? ??????? ???????? ??????
camera.updateMatrixWorld();
mesh["logo"].matrixWorld.multiplyMatrices(camera.matrixWorld,mesh["logo"].matrix);


debug_calc("javascript");
debug["renderer"].start=performance.now();


renderer.clear(); // ??????? ?????????
renderer.render(scene,camera); // ???????? ?????
if(debug_mode){ renderer_stats_update(0); }
renderer.clearDepth(); // ??????? ??????? ?? ??????? ?????, ?? ???? ??????
renderer.render(scene_hud,camera_hud); // HUD ?????
if(debug_mode){ renderer_stats_update(1); }


debug_calc("renderer");
debug_calc("frame",debug_fps_now);


var max=debug_text.length;
for(var n=0;n<max;n++){
var item=debug_text[n];
debug[item[0]].element.innerHTML=item[1];
}
debug_text=[];


}


</script>
</body>
</html>
