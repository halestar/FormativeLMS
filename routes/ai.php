<?php

use App\Mcp\Servers\FabLmsAiLearning;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('learning', FabLmsAiLearning::class);
Mcp::web('/mcp/learning', FabLmsAiLearning::class)->middleware('auth:sanctum');