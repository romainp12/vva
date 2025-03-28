<?php
function is_logged_in() {
    return isset($_SESSION['user']);
}

function is_encadrant() {
    return is_logged_in() && $_SESSION['type'] === 'EN';
}

function is_admin() {
    return is_logged_in() && $_SESSION['type'] === 'AD';
}

function is_vacancier() {
    return is_logged_in() && $_SESSION['type'] === 'VA';
}
?>
