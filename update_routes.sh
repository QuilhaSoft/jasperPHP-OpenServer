#!/bin/bash

# Move routes from api.php to web.php
ROUTES_TO_MOVE=$(cat /home/supportti/jasperContainer/backend12/routes/api.php | grep -v '<?php' | grep -v 'use Illuminate' | grep -v 'Facades' | grep -v 'Route::middleware' | grep -v '});')

# Add a newline to the routes to move
ROUTES_TO_MOVE="
${ROUTES_TO_MOVE}"

# Escape for sed
ROUTES_TO_MOVE=$(sed -e 's/[/&]/\&/g' <<<"$ROUTES_TO_MOVE")

sed -i "/Route::middleware('auth:sanctum')->group(function () {/a ${ROUTES_TO_MOVE}" /home/supportti/jasperContainer/backend12/routes/web.php

# Add use statements to web.php
sed -i "/use App\Http\Controllers\Api\AuthController;/a use App\Http\Controllers\Api\DataSourceController;\nuse App\Http\Controllers\Api\ReportController;" /home/supportti/jasperContainer/backend12/routes/web.php

# Clear api.php
echo '<?php\n\nuse Illuminate\Http\Request;\nuse Illuminate\Support\Facades\Route;\n' > /home/supportti/jasperContainer/backend12/routes/api.php
