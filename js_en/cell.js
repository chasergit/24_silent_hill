function cells_find(){


var current_x=Math.floor(origin_x/town_tris_size);
var current_z=Math.floor(origin_z/town_tris_size);


intersection_tris=[];


intersection_cell=town_tris_cell[current_x+"_"+current_z];
if(intersection_cell!=undefined){
var result=intersection_ray_triangle();
if(result!=0){ return result; }
}


var last_x=Math.floor((origin_x+direction_2d_x*distance)/town_tris_size);
var last_z=Math.floor((origin_z+direction_2d_z*distance)/town_tris_size);


// FIND THE DIRECTION
var stepX=direction_2d_x>=0?1:-1;
var stepZ=direction_2d_z>=0?1:-1;


// FIND THE CLOSEST BOUNDARY IN X
var item_1=origin_x;
var item_2=direction_2d_x;
// FIX THE CASE WHEN THE X COORDINATE IS EXACTLY ON THE BORDER
if(item_1%town_tris_size==0){
if(item_2<0){ var tMaxX=0; }
else{ var tMaxX=town_tris_size/item_2; }
}
else{
if(direction_2d_x<0){
item_1=-item_1;
item_2=-item_2;
}
item_1=(item_1%town_tris_size+town_tris_size)%town_tris_size;
var tMaxX=(town_tris_size-item_1)/item_2;
}


// FIND THE CLOSEST BORDER IN Z
var item_1=origin_z;
var item_2=direction_2d_z;
// FIX THE CASE WHEN THE Z COORDINATE IS EXACTLY ON THE BORDER
if(item_1%town_tris_size==0){
if(item_2<0){ var tMaxZ=0; }
else{ var tMaxZ=town_tris_size/item_2; }
}
else{
if(direction_2d_z<0){
item_1=-item_1;
item_2=-item_2;
}
item_1=(item_1%town_tris_size+town_tris_size)%town_tris_size;
var tMaxZ=(town_tris_size-item_1)/item_2;
}


// WHAT DISTANCE SHOULD THE BEAM GO TO CROSS THE SQUARE.
// FOR EXAMPLE, A SQUARE 5 BY 5 METERS. ON THE X-AXIS THE BEAM GOES FROM THE LOWER LEFT CORNER OF THE SQUARE TO THE RIGHT WITHOUT TILT, THAT IS A STRAIGHT AND THE DIRECTION IS EQUAL TO 1.
// THE BEAM WILL CROSS THE SQUARE IN 5/1=5 METERS. BUT IF THE BEAM GOES AT AN ANGLE, FOR EXAMPLE, IS DIRECTED TO THE UPPER RIGHT ANGLE, THAT IS DIAGONAL AND THE DIRECTION IS EQUAL TO 0.7,
// THEN THE BEAM HAS TO PASS 5/0.7=7.14 METERS TO CROSS THE SQUARE


var tDeltaX=town_tris_size/stepX/direction_2d_x;
var tDeltaZ=town_tris_size/stepZ/direction_2d_z;


var limit_x=distance+tDeltaX;
var limit_z=distance+tDeltaZ;


while(true){


// WITH WHICH FIRST AXIS THE BEAM INTERSECTS, THEN THE DISTANCE AND PLUS AND SELECT A CELL


if(tMaxX<tMaxZ){
tMaxX+=tDeltaX;
current_x+=stepX;
}else{
tMaxZ+=tDeltaZ;
current_z+=stepZ;
}


if(tMaxX>limit_x || tMaxZ>limit_z){ break; }


intersection_cell=town_tris_cell[current_x+"_"+current_z];
if(intersection_cell!=undefined){
var result=intersection_ray_triangle();
if(result!=0){ return result; }
}


}


return 0;


}