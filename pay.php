<?php
require('config.php');
require('razorpay-php/Razorpay.php');
session_start();
error_reporting(0);
include('includes/config.php');
include('gateway-config.php');
use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);
$pid=$_SESSION["pid"];
$name=$_SESSION["name"];
$amt=$_SESSION["amt"];
$pno=$_SESSION["phno"];
$txn=$_SESSION["txn"];
$email=$_SESSION['login'];
$webtittle='Travel book';
$displayCurrency='INR';
$orderData = [
    'receipt'         => 3456,
    'amount'          => $amt * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];
$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayAmount = $amount = $orderData['amount'];
if ($displayCurrency !== 'INR')
{
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}
$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => "TRAVEL BOOK",
    "description"       => "Travel is a way of life",
    "image"             => "",
    "prefill"           => [
    "name"              => $name,
    "email"             => $email,
    "contact"           => $pno,
    ],
    "notes"             => [
    "address"           => "kjc",
    "merchant_order_id" => $txn,
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];
if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);

require("checkout/{$checkout}.php");
?>
<!doctype html>
<html>
<head>
    <style>
        body{

background-color: #eee;
}

.container{

height: 100vh;

}


.card{
border:none;
}

.form-control {
border-bottom: 2px solid #eee !important;
border: none;
font-weight: 600
}

.form-control:focus {
color: #495057;
background-color: #fff;
border-color: #8bbafe;
outline: 0;
box-shadow: none;
border-radius: 0px;
border-bottom: 2px solid blue !important;
}



.inputbox {
position: relative;
margin-bottom: 20px;
width: 100%
}

.inputbox span {
position: absolute;
top: 7px;
left: 11px;
transition: 0.5s
}

.inputbox i {
position: absolute;
top: 13px;
right: 8px;
transition: 0.5s;
color: #3F51B5
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
-webkit-appearance: none;
margin: 0
}

.inputbox input:focus~span {
transform: translateX(-0px) translateY(-15px);
font-size: 12px
}

.inputbox input:valid~span {
transform: translateX(-0px) translateY(-15px);
font-size: 12px
}

.card-blue{

background-color: #492bc4;
}

.hightlight{

background-color: #5737d9;
padding: 10px;
border-radius: 10px;
margin-top: 15px;
font-size: 14px;
}

.yellow{

color: #fdcc49; 
}

.decoration{

text-decoration: none;
font-size: 14px;
}

.btn-success {
color: #fff;
background-color: #492bc4;
border-color:#492bc4;
}

.btn-success:hover {
color: #fff;
background-color:#492bc4;
border-color: #492bc4;
}


.decoration:hover{

text-decoration: none;
color: #fdcc49; 
}
    </style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<title> PARKLINE || Check Out</title>
</head>
<body>


    <div class="container mt-5 px-5">

<div class="mb-4">

    <h2>Confirm order and pay</h2>
<span>please make the payment, after that you can enjoy all the features and benefits.</span>
    
</div>

<div class="row">

<div class="col-md-8">
    


<div class="col-md-4">

    <div class="card card-blue p-3 text-white mb-3">

       <span>You have to pay</span>
        <div class="d-flex flex-row align-items-end mb-3">
            <h1 class="mb-0 yellow"><?php echo $amt; ?></h1> 
        </div>
        <div class="d-flex flex-row align-items-end mb-3">
            <h1 class="mb-0 yellow"><?php echo $txn; ?></h1> 
        </div>
        <div class="d-flex flex-row align-items-end mb-3">
            <h1 class="mb-0 yellow"><?php echo $pno; ?></h1> 
        </div>

        <span>Enjoy all the features and perk after you complete the payment</span>
        <a href="#" class="yellow decoration">Know all the features</a>

        <div class="hightlight">
<center>
<form action="verify.php" method="POST">
  <script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="<?php echo $data['key']?>"
    data-amount="<?php echo $data['amount']?>"
    data-currency="INR"
    data-name="<?php echo $data['name']?>"
    data-image="<?php echo $data['image']?>"
    data-description="<?php echo $data['description']?>"
    data-prefill.name="<?php echo $data['prefill']['name']?>"
    data-prefill.email="<?php echo $data['prefill']['email']?>"
    data-prefill.contact="<?php echo $data['prefill']['contact']?>"
    data-notes.shopping_order_id="<?php echo $txn; ?>"
    data-order_id="<?php echo $data['order_id']?>"
    <?php if ($displayCurrency !== 'INR') { ?> data-display_amount="<?php echo $data['display_amount']?>" <?php } ?>
    <?php if ($displayCurrency !== 'INR') { ?> data-display_currency="<?php echo $data['display_currency']?>" <?php } ?>
  >
  </script>
  <!-- Any extra fields to be submitted with the form but not sent to Razorpay -->
  <input type="hidden" name="shopping_order_id" value="<?php echo $txn; ?>">
</form>
</center>
          
            

        </div>
        
    </div>
    
</div>

</div>


</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>
