

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


// ������� �����������
var stepX=direction_2d_x>=0?1:-1;
var stepZ=direction_2d_z>=0?1:-1;


// ������� ��������� ������� �� X
var item_1=origin_x;
var item_2=direction_2d_x;
// ���������� ������, ����� ���������� X ����� ����� �� �������
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


// ������� ��������� ������� �� Z
var item_1=origin_z;
var item_2=direction_2d_z;
// ���������� ������, ����� ���������� Z ����� ����� �� �������
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


// ����� ���������� ������ ������ ���, ����� ������� �������.
// ��������, ������� 5 �� 5 ������. �� ��� X ��� �Ĩ� �� ������ ������� ���� �������� ������� ��� �������, �� ���� ������ � ����������� ����� 1.
// ��� ������ר� ������� ����� 5/1=5 ������. �� ���� ��� �Ĩ� ��� �����, ��������, ��������� � ������ ������� ����, �� ���� �� ��������� � ����������� ����� 0.7,
// �� ���� ��� ���� ������ 5/0.7=7.14 ������, ����� �������� �������


var tDeltaX=town_tris_size/stepX/direction_2d_x;
var tDeltaZ=town_tris_size/stepZ/direction_2d_z;


var limit_x=distance+tDeltaX;
var limit_z=distance+tDeltaZ;


while(true){


// � ����� ������ ���� ������ר��� ���, �� ���������� � ������� � �������� ������


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
