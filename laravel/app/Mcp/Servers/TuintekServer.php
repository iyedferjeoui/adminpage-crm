<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreateLeadTool;
use App\Mcp\Tools\CreateProjectTool;
use App\Mcp\Tools\FindLeadTool;
use App\Mcp\Tools\FindProjectTool;
use App\Mcp\Tools\UpdateLeadTool;
use App\Mcp\Tools\UpdateProjectTool;
use Laravel\Mcp\Server;

class TuintekServer extends Server
{
    protected array $tools = [
        FindProjectTool::class,
        CreateProjectTool::class,
        UpdateProjectTool::class,

        FindLeadTool::class,
        CreateLeadTool::class,
        UpdateLeadTool::class,
    ];
}