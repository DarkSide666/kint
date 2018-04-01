<?php

namespace Kint\Renderer\Rich;

use Kint\Object\Representation\MicrotimeRepresentation;
use Kint\Object\Representation\Representation;

class MicrotimePlugin extends Plugin implements TabPluginInterface
{
    public function renderTab(Representation $r)
    {
        if (!($r instanceof MicrotimeRepresentation)) {
            return false;
        }

        // '@' is used to prevent the dreaded timezone not set error
        $out = @date('Y-m-d H:i:s', $r->seconds).'.'.str_pad($r->microseconds, 6, '0', STR_PAD_LEFT);
        if ($r->lap !== null) {
            $out .= "\n<b>SINCE LAST CALL:</b> <b class=\"kint-microtime-lap\">".round($r->lap, 4).'</b>s.';
        }
        if ($r->total !== null) {
            $out .= "\n<b>SINCE START:</b> ".round($r->total, 4).'s.';
        }
        if ($r->avg !== null) {
            $out .= "\n<b>AVERAGE DURATION:</b> <span class=\"kint-microtime-avg\">".round($r->avg, 4).'</span>s.';
        }

        $unit = array('B', 'KB', 'MB', 'GB', 'TB');

        $out .= "\n<b>MEMORY USAGE:</b> ".$r->mem.' bytes (';
        $i = floor(log($r->mem, 1024));
        $out .= round($r->mem / pow(1024, $i), 3).' '.$unit[$i].')';
        $i = floor(log($r->mem_real, 1024));
        $out .= ' (real '.round($r->mem_real / pow(1024, $i), 3).' '.$unit[$i].')';

        if ($r->mem_peak !== null && $r->mem_peak_real !== null) {
            $out .= "\n<b>PEAK MEMORY USAGE:</b> ".$r->mem_peak.' bytes (';
            $i = floor(log($r->mem_peak, 1024));
            $out .= round($r->mem_peak / pow(1024, $i), 3).' '.$unit[$i].')';
            $i = floor(log($r->mem_peak_real, 1024));
            $out .= ' (real '.round($r->mem_peak_real / pow(1024, $i), 3).' '.$unit[$i].')';
        }

        return '<pre data-kint-microtime-group="'.$r->group.'">'.$out.'</pre>';
    }

    public static function renderJs()
    {
        return file_get_contents(KINT_DIR.'/resources/compiled/rich_microtime.js');
    }
}
