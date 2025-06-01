<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets\Commands;

use Illuminate\Console\Command;

class FilamentSiteSnippetsCommand extends Command
{
    public $signature = 'filament-site-snippets';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
