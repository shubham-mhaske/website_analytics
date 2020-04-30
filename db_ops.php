<?php
include 'dbcon.php';

$conn = OpenCon();


function visitorCount($conn,$uid){
    $get_uid_query = "SELECT uid FROM info where uid = '{$uid}' "  ;
    $result = mysqli_query($conn, $get_uid_query);

    if (mysqli_num_rows($result) > 0) { //check if uid present 
        
        $row = mysqli_fetch_assoc($result); 
        $rep_row =  $row["uid"];
        $get_count_query = "SELECT count FROM info where uid = '{$uid}' "  ;
        $uid_count = mysqli_fetch_assoc(mysqli_query($conn, $get_count_query))['count'];  //get count of visits for uid
        $uid_count++;
        $update_count_query = "UPDATE info SET count ={$uid_count} where uid ='{$uid}' "; // update visit count
        mysqli_query($conn, $update_count_query);
        
        
    } 

}

function check_present(){
    if(isset($_COOKIE["uid"])){
            
        $uid =  $_COOKIE["uid"];
        
        return $uid;
    }
    return false;
}
function set_uid(){
     

            $uid = uniqid();
            setCookie("uid",$uid,time()+(60*60*24));
            
        
        return $uid;
    
}

function get_pages(){
    $page_visited = $_SERVER['REQUEST_URI'];
    return $page_visited;
    
}

function add_pages($conn,$page,$uid){
    $get_page_query = "SELECT pages FROM info where uid = '{$uid}' "  ;
    $result = mysqli_query($conn, $get_page_query);
    $row = mysqli_fetch_assoc($result); 
    
    $temp = $row["pages"].",".$page;
     
    $update_page_query = "UPDATE info SET pages ='{$temp}' where uid ='{$uid}'";
    $stat = mysqli_query($conn, $update_page_query);
    
     
}


function get_location(){
    
    if(!empty($_POST['latitude']) && !empty($_POST['longitude'])){
        $lat = $_POST['latitude'];
        $lng = $_POST['longitude'];
        return array($lat,$lng);
    }
}

if(check_present()){
    visitorCount($conn,check_present());
 
    add_pages($conn,get_pages(),check_present());

}else{
    $uid = set_uid();
    list($lat,$lng) = get_location();
    $page_visited = get_pages();
    $new_visitor_query = "INSERT INTO info(uid,lat, lng, pages,count) VALUES ('{$uid}','{$lat}','{$lng}','{$page_visited}',1)";
    mysqli_query($conn, $new_visitor_query);

}




CloseCon($conn);
?>