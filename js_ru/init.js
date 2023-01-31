// ____________________ œŒÀÕ€… › –¿Õ____________________


function fullscreen() {
var element=document.getElementById("project");
if(element.requestFullScreen){ element.requestFullScreen(); }
else if(element.webkitRequestFullScreen){ element.webkitRequestFullScreen(); }
else if(element.mozRequestFullScreen){ element.mozRequestFullScreen(); }
}


// ____________________ ”œ–¿¬À≈Õ»≈ Ã€ÿ Œ… ____________________


function lockChangeAlert(){
if(document.pointerLockElement===canvas || document.mozPointerLockElement===canvas){ document.addEventListener("mousemove",updatePosition,false); }
else{ document.removeEventListener("mousemove",updatePosition,false); }
}


// ____________________ œŒ¬Œ–Œ“  ____________________


var fps_cam_x=0;
var fps_cam_y=0;


function updatePosition(e,move_x,move_y){
if(stop==1){ return; }
if(mouse==0){ return; }
fps_cam_x+=e.movementX*sens;
fps_cam_y+=e.movementY*sens;
controls.mouseX=fps_cam_x;
controls.mouseY=fps_cam_y;
}
