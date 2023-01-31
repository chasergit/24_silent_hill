//____________________ LOADER ____________________


var manager_to_load=0; // HOW MUCH TO UPLOAD THROUGH THE DOWNLOAD MANAGER. CALCULATED BELOW
var manager_loaded=0; // LOADED IN THE MANAGER
var other_to_load=0; // HOW MANY OTHER FILES SHOULD BE DIRECTLY DOWNLOADED. CALCULATED BELOW
var other_loaded=0; // LOADED DIRECTLY


var loadingManager=new THREE.LoadingManager();
loadingManager.onProgress=function(item,loaded,total){
console.log(item,loaded,total);
manager_loaded=loaded;
if(loaded==total){ console.log("FILES IN MANAGER LOADED"); }
};


//____________________ RUN A FILE UPLOAD CHECK WHEN THE PAGE ITSELF IS LOADED ____________________


window.onload=function(){
audios=document.getElementsByTagName("audio");
check_loaded=setTimeout("is_loaded();",100);
}


//____________________ CHECK UPLOAD FILE ____________________


var audios=[];
var check_loaded;


function is_loaded(){
document.getElementById("loading_amount").innerHTML=(manager_loaded+other_loaded)+"/"+(manager_to_load+other_to_load);
for(var aui=0;aui<audios.length;aui++){
if(audios[aui].readyState!=4){ check_loaded=setTimeout("is_loaded();",100); return; }
}


if(manager_to_load+other_to_load==manager_loaded+other_loaded){
clearTimeout(check_loaded);
init();
return;
}


check_loaded=setTimeout("is_loaded();",100);
}


//____________________ AFTER LOADING FIRST INITIALIZATION ____________________


function meshes_frustum_visible(item,mode){


if(mode==1){
item.traverse(function(child){
if(child.isMesh){
child.last_visible=child.visible;
child.visible=true;
child.last_frustumCulled=child.frustumCulled;
child.frustumCulled=false;
}
});
}
else{
item.traverse(function(child){
if(child.isMesh){
child.visible=child.last_visible;
child.frustumCulled=child.last_frustumCulled;
delete child.visible;
delete child.last_frustumCulled;
}
});
}


}


function init(){


canvas.requestPointerLock=canvas.requestPointerLock || canvas.mozRequestPointerLock;
document.exitPointerLock=document.exitPointerLock || document.mozExitPointerLock;
document.addEventListener("pointerlockchange",lockChangeAlert,false);
document.addEventListener("mozpointerlockchange",lockChangeAlert,false);

//scene.add(new THREE.AxesHelper(100));
//document.getElementById("begin").style.display="block";
init_lights();
//camera.add(listener);


// FIRST RENDERING TO GET EVERYTHING INTO MEMORY IMMEDIATELY AND NOT STOPPING
meshes_frustum_visible(scene,1);
meshes_frustum_visible(scene_hud,1);
renderer.render(scene,camera);
renderer.render(scene_hud,camera_hud);
meshes_frustum_visible(scene,2);
meshes_frustum_visible(scene_hud,2);


document.getElementById("loading").style.display="none";
document.getElementById("begin").style.display="block";


stop=0;
loop();


}
