<?php

Schedule::command('migrate:fresh --seed')->daily();
Schedule::command('migrate:fresh --seed')->dailyAt('00:30');

