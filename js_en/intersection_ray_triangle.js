"use strict"


// ____________________ INTERSECTION OF A BEAM AND A TRIANGLE WITH THE FRONT SIDE ____________________


function intersection_ray_triangle(){


var result=0;
var distance=Infinity;


var max=intersection_cell.length;


for(var n=0;n<max;n++){


var p=intersection_cell[n];


// REMOVE REPEATED TRIANGLES
if(intersection_tris[p+"n"]!=undefined){ continue; }
intersection_tris[p+"n"]=1;


var ax=town_tris_a[p],ay=town_tris_a[p+1],az=town_tris_a[p+2];
var bx=town_tris_a[p+3],by=town_tris_a[p+4],bz=town_tris_a[p+5];
var cx=town_tris_a[p+6],cy=town_tris_a[p+7],cz=town_tris_a[p+8];


var bax=bx-ax,bay=by-ay,baz=bz-az;
var cax=cx-ax,cay=cy-ay,caz=cz-az;


var pvecx=direction_y*caz-direction_z*cay;
var pvecy=direction_z*cax-direction_x*caz;
var pvecz=direction_x*cay-direction_y*cax;


var det=bax*pvecx+bay*pvecy+baz*pvecz;


if(det<0.000001)continue; // BEAM IS PARALLEL TO A TRIANGLE OR IN THE BACK SIDE


var tvecX=origin_x-ax,tvecY=origin_y-ay,tvecZ=origin_z-az;


var u=tvecX*pvecx+tvecY*pvecy+tvecZ*pvecz;


if(u<0 || u>det)continue;


var qvecx=tvecY*baz-tvecZ*bay;
var qvecy=tvecZ*bax-tvecX*baz;
var qvecz=tvecX*bay-tvecY*bax;


var v=direction_x*qvecx+direction_y*qvecy+direction_z*qvecz;


if(v<0 || u+v>det)continue;


var d=(cax*qvecx+cay*qvecy+caz*qvecz)/det;


// BEHIND THE BEAM OR GREATER DISTANCE
if(d<0 || d>distance)continue;


distance=d;
result=d;


}


return result;


}
