<?php
require_once("router.php");

/* Serve frontend */
if (!str_contains($_SERVER['REQUEST_URI'], 'API')) {
    echo include './Frontend/build/index.html';
} else {
    /* Routes */
    get('/entities', './API/Endpoints/getEntities.php');
    get('/entities/$entityId', './API/Endpoints/getEntity.php');
    get('/populateDB', './API/Endpoints/generateFakeData.php');

    /* If all else fails, this shall prevail. The viscous error will be released into the wild. */
    any('/404','./API/Endpoints/Errors/404.php');
}