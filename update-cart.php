<?php
session_start();
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$productId = $data['product_id'] ?? null;
$size      = $data['size'] ?? null;
$qty       = (int)($data['qty'] ?? 1);

if(!$productId && $qty !== 0){
    echo json_encode(["success"=>false,"msg"=>"missing fields"]);
    exit;
}

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if($qty === 0){
    foreach($_SESSION['cart'] as $k=>$item){
        if($item['product_id']==$productId && $item['size']==$size){
            unset($_SESSION['cart'][$k]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}else{
    $found = false;
    foreach($_SESSION['cart'] as &$item){
        if($item['product_id']==$productId && $item['size']==$size){
            $item['qty'] = $qty;
            $found = true;
            break;
        }
    }
    unset($item);
}

$subtotal = 0;
foreach($_SESSION['cart'] as $i){
    $subtotal += $i['price']*$i['qty'];
}
$shipping = 50.00;
$grand_total = $subtotal+$shipping;

echo json_encode([
    "success"=>true,
    "subtotal"=>$subtotal,
    "shipping"=>$shipping,
    "grand_total"=>$grand_total
]);
