<?php

if (isset($data['discount'])) {
    $price = ceil(($data['price'] * $data['discount']) / 100);
    $data['current_price'] = $data['price'] - $price;
}