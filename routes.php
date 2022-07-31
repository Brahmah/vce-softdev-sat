<?php
/**
 * This file defines all the routes for the API and their respective handlers.
 * Each route is also configured with the appropriate permissions.
 * This is used to determine if the user is authorized to access the route.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
require_once("router.php");

/* Serve frontend */
if (!str_contains($_SERVER['REQUEST_URI'], 'API')) {
    include './Frontend/build/index.html';
} else {
    /* Authentication */
    post('/authenticate', './API/Endpoints/authenticate.php', []);
    post('/logout', './API/Endpoints/logout.php', []);
    get('/me', './API/Endpoints/me.php', [RequiredPermission::authenticated]);
    /* Users */
    get('/users', './API/Endpoints/getUsers.php', [RequiredPermission::authenticated]);
    post('/users', './API/Endpoints/createUser.php', [RequiredPermission::authenticated, RequiredPermission::superuser]);
    delete('/users/$userId', './API/Endpoints/deleteUser.php', [RequiredPermission::authenticated, RequiredPermission::superuser]);
    post('/users/$userId/passwordReset', './API/Endpoints/resetUserPassword.php', [RequiredPermission::authenticated, RequiredPermission::superuser]);
    post('/users/$userId/role', './API/Endpoints/updateUserRole.php', [RequiredPermission::authenticated, RequiredPermission::superuser]);
    /* Entities */
    get('/entities', './API/Endpoints/getEntities.php', [RequiredPermission::authenticated]);
    get('/entities/$entityId', './API/Endpoints/getEntity.php', [RequiredPermission::authenticated]);
    delete('/entities/$entityId', './API/Endpoints/deleteEntity.php', [RequiredPermission::authenticated]);
    get('/entities/$entityId/breadcrumb', './API/Endpoints/getEntityBreadcrumb.php', [RequiredPermission::authenticated]);
    get('/entities/$entityId/uptime/$type', './API/Endpoints/getEntityUptime.php', [RequiredPermission::authenticated]);
    post('/entities/$entityId/fields/$fieldId/save', './API/Endpoints/saveEntityField.php', [RequiredPermission::authenticated]);
    get('/entities/$entityId/fields/$fieldId/options', './API/Endpoints/getEntityFieldOptions.php', [RequiredPermission::authenticated]);
    /* Areas */
    get('/areas/$areaId/nodes', './API/Endpoints/getAreaNodes.php', [RequiredPermission::authenticated]);
    get('/areas/$areaId/info', './API/Endpoints/getAreaInfo.php', [RequiredPermission::authenticated]);
    post('/areas/$areaId', './API/Endpoints/saveArea.php', [RequiredPermission::authenticated]); // PUT is ideal here, but not handled well by PHP
    post('/areas/$areaId/manipulate', './API/Endpoints/manipulateArea.php', [RequiredPermission::authenticated]);
    get('/activity/$context/$itemId', './API/Endpoints/getActivity.php', [RequiredPermission::authenticated]); // get activity for given context and item
    get('/activity/$context', './API/Endpoints/getActivity.php', [RequiredPermission::authenticated]); // get activity for all items
    /* Settings */
    get('/settings/entityTypes', './API/Endpoints/getEntityTypes.php', [RequiredPermission::authenticated]);
    post('/settings/entityTypes', './API/Endpoints/createEntityType.php', [RequiredPermission::authenticated]);
    get('/settings/entityTypes/$typeId', './API/Endpoints/getEntityType.php', [RequiredPermission::authenticated]);
    post('/settings/entityTypes/$typeId', './API/Endpoints/updateEntityDefinition.php', [RequiredPermission::authenticated]);
    delete('/settings/entityTypes/$typeId', './API/Endpoints/deleteEntityType.php', [RequiredPermission::authenticated]);
    /* Misc */
//    get('/populateDB', './API/Endpoints/generateFakeData.php', [RequiredPermission::authenticated]);
    /* If all else fails, this shall prevail. The viscous error will be released into the wild. */
    any('/404','./API/Endpoints/Errors/404.php', []);
}