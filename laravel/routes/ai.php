<?php

use App\Mcp\Servers\TuintekServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/tuintek', TuintekServer::class);
