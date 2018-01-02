<?php
use App\{cache_events,filesystem};

if (!cache_events::get('clear_tmp_dir')) {
    cache_events::set('clear_tmp_dir', true, mt_rand(82800, 86400));
    filesystem::deleteOldTmpFiles();
}
