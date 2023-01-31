

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


// мюундхл мюопюбкемхе
var stepX=direction_2d_x>=0?1:-1;
var stepZ=direction_2d_z>=0?1:-1;


// мюундхл акхфюиьсч цпюмхжс он X
var item_1=origin_x;
var item_2=direction_2d_x;
// хяопюбкъел яксвюи, йнцдю йннпдхмюрю X кефхр рнвмн мю цпюмхже
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


// мюундхл акхфюиьсч цпюмхжс он Z
var item_1=origin_z;
var item_2=direction_2d_z;
// хяопюбкъел яксвюи, йнцдю йннпдхмюрю Z кефхр рнвмн мю цпюмхже
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


// йюйне пюяярнъмхе днкфем опнирх ксв, врнаш оепеяев йбюдпюр.
// мюопхлеп, йбюдпюр 5 мю 5 лерпнб. он нях X ксв хд╗р нр кебнцн мхфмецн сцкю йбюдпюрю мюопюбн аег мюйкнмю, рн еярэ опълюъ х мюопюбкемхе пюбмн 1.
// ксв оепеяев╗р йбюдпюр вепег 5/1=5 лерпнб. мн еякх ксв хд╗р онд сцкнл, мюопхлеп, мюопюбкем б опюбши бепумхи сцнк, рн еярэ он дхюцнмюкх х мюопюбкемхе пюбмн 0.7,
// рн ксвс сфе мюдн опнирх 5/0.7=7.14 лерпнб, врнаш оепеяевэ йбюдпюр


var tDeltaX=town_tris_size/stepX/direction_2d_x;
var tDeltaZ=town_tris_size/stepZ/direction_2d_z;


var limit_x=distance+tDeltaX;
var limit_z=distance+tDeltaZ;


while(true){


// я йюйни оепбни няэч оепеяев╗ряъ ксв, рн пюяярнъмхе х окчясел х бшахпюел ъвеийс


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
