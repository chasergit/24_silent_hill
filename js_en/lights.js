function init_lights(){


// ____________________ SURROUNDING LIGHT ____________________


var ambient=new THREE.AmbientLight(0xF5CF6B,0.2);
scene.add(ambient);


// ____________________ FOG ____________________


scene.fog=new THREE.FogExp2(0x8A7E9B,2);


// ____________________ SUNLIGHT ____________________


var sun=new THREE.DirectionalLight(0xffffff,1.0);
sun.position.set(400,500,400);
sun.castShadow=true;
sun.shadow.mapSize.width=4096;
sun.shadow.mapSize.height=4096;
sun.shadow.camera.near=10;
sun.shadow.camera.far=1700;
sun.shadow.camera.left=-2000;
sun.shadow.camera.right=2000;
sun.shadow.camera.top=1350;
sun.shadow.camera.bottom=-1350;
//sun.shadow.bias=-0.007;
sun.shadow.bias=0.001;
sun.shadow.radius=1;
scene.add(sun);


}