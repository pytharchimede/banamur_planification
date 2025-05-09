<?php
require_once 'model/Auth.php';

if (Auth::isLoggedIn()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
