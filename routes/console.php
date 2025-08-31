<?php

Schedule::command('fablms:fresh-db')->daily();
Schedule::command('fablms:clear-local-documents')
        ->everyFifteenMinutes();

