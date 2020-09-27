<?php

$v->layout('layout', ['title' => 'User Profile']) ?>

<div class="container">
    <h3>User Profile</h3>
    <p>Hello, <?=$v($name) // escape the $name variable ?></p>
</div>