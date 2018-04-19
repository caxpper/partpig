<?php
header("Access-Control-Allow-Origin: *");
require_once('mysqlConnect.php');
//basic output format, all data gets pushed into data[]
$output = [
    'success'=> false,
    'error' => [],
    'data' => []
];

$user_id = $_GET['user_id'];


$query =  "SELECT s.buyer_id,
                  p.id, 
                  p.brand, 
                  p.part_name, 
                  c.name AS category, 
                  p.make, 
                  p.model, 
                  p.year,                               
                  p.part_number, 
                  p.price_usd, 
                  i.url AS images
            FROM `shoppingcart` AS s
            JOIN `part` AS p
                ON s.buyer_id = $user_id AND s.part_id = p.id
            JOIN `category` AS c
                ON p.category_id = c.id 
            JOIN `image` AS i 
                ON i.id=
                (
                    SELECT MIN(im.id) 
                    FROM `image` as im 
                    WHERE im.part_id=p.id
                )";

$result = mysqli_query($conn, $query);
$display = new stdClass();
$display->brand = 'true';
$display->price = 'true';

if($result){
    if(mysqli_num_rows($result)> 0){
        while($row = mysqli_fetch_assoc($result)){
            $row['images'] = array($row['images']); 
            $row['display'] = $display;
            $row['price_usd'] = (float)$row['price_usd'];
            $row['year'] = (int)$row['year'];
            $output['data'][] = $row;
        }
    }
    else{
        $output['errors'][] = 'NO Data available';
    }
    $output['success'] = true;
}
else{
    $output['errors'][] = 'Error in database query';
}

$json_output = json_encode($output);
print($json_output);
?>