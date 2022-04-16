<?php

function checkAuth($session): bool
{
    if(!isset($_SESSION['username'])) {
        return false;
    } else {
        return true;
    }
}