
<?php

// __DIR__ = '/var/www/api/common';

$host = str_replace('common', '', __DIR__);

//Common Methods
$pathGeneralMethods = $host . '/common/generalMethods.php';
$pathPushNotifications = $host . '/common/pushNotifications.php';
$pathProtocols = $host . '/common/protocols.php';
$pathMiddleware = $host . '/common/middleware.php';
$pathConstants = $host . '/common/constants.php';
$pathCommunications = $host . '/common/communications.php';
$pathUrlParams = $host . '/common/urlParams.php';
$pathServices = $host . '/common/services.php';

//Configurations
$db = $host . '/config/db.php';
$pathResponse = $host . '/config/response.php';

//Models
$pathCredentials = $host . '/models/credentials.php';
$pathCustomers = $host . '/models/customers.php';
$pathUsers = $host . '/models/users.php';
$pathIProtect = $host . '/models/iProtect.php';
$pathSites = $host . '/models/sites.php';
$pathSiteUsers = $host . '/models/siteUsers.php';
$pathSettings = $host . '/models/settings.php';
$pathCalculations = $host . '/models/calculations.php';

//Controllers
$pathUsersController = $host . '/controllers/usersController.php';
$pathSitesController = $host . '/controllers/sitesController.php';
$pathIProtectController = $host . '/controllers/iProtectController.php';
$pathAdminController = $host . '/controllers/adminController.php';
$pathCustomersController = $host . '/controllers/customersController.php';
$pathSitesController = $host . '/controllers/sitesController.php';
$pathSiteUsersController = $host . '/controllers/siteUsersController.php';
$pathSettingsController = $host . '/controllers/settingsController.php';
$pathCallingController = $host . '/controllers/callingController.php';
$pathCronController = $host . '/controllers/cronController.php';
$pathSiteMeteringController = $host . '/controllers/siteMeteringController.php';
$pathServicesController = $host . '/controllers/servicesController.php';
$pathSpawnController = $host . '/controllers/spawnController.php';
$pathEmailController = $host . '/controllers/emailController.php';
$pathAlarmsReportController = $host . '/controllers/alarmsReportController.php';
$pathUserReportsController = $host . '/controllers/userReportsController.php';
?>