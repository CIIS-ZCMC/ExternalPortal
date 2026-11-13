<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use TADPHP\TADFactory;
use App\Models\Devices;


class DeviceController extends Controller
{
    public static function Connect($device)
    {
        if (!$device) {
            return false;
        }

        try {
            ini_set('default_socket_timeout', '10');

            $options = [
                'ip' => (string)$device['ip_address'],
                'com_key' => (int)$device['com_key'],
                'description' => 'TAD1',
                'connection_timeout' => 3,
                'soap_port' => (int)$device['soap_port'],
                'udp_port' => (int)$device['udp_port'],
                'encoding' => 'utf-8'
            ];
            $tad_factory = new TADFactory($options);
            $tad = $tad_factory->get_instance();
            if ($tad->is_alive()) {
                return $tad;
            }

            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function getSNMAC($tad)
    {
        $sn = $tad->get_serial_number();
        $ma = $tad->get_mac_address();
        $devices_n =  simplexml_load_string($sn);
        $device_ma = simplexml_load_string($ma);
        $serial_number = '';
        $mac_address = '';
        foreach ($device_ma->Row as $dma) {
            $mac_address = (string) $dma->Information;
        }
        foreach ($devices_n->Row as $dsn) {
            $serial_number = (string) $dsn->Information;
        }
        return response()->json([
            'serialnumber' => $serial_number,
            'macaddress' => $mac_address
        ]);
    }
}
