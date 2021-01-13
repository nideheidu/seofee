<?php

namespace app\common\model;
class SystemConfig extends Base
{
    protected $createTime = "ctime";
    protected $updateTime = "mtime";
    protected $autoWriteTimestamp = true;

    public static function getConfigData($name = '', $update = false)
    {
        $result = config('sys_config');
        if (!$result || $update == true) {
            $configs = self::column('value,type,group', 'name');
            $result = [];
            foreach ($configs as $config) {
                switch ($config['type']) {
                    case 'array':
                    case 'checkbox':
                        if ($config['name'] == 'config_group') {
                            $v = parse_attr($config['value']);
                            if (!empty($config['value'])) {
                                $result[$config['group']][$config['name']] = array_merge(config('hs_system.config_group'), $v);

                            } else {
                                $result[$config['group']][$config['name']] = config('hs_system.config_group');

                            }

                        } else {
                            $result[$config['group']][$config['name']] = parse_attr($config['value']);

                        }
                        break 1;
                    default:
                        $result[$config['group'] . '.' . $config['name']] = $config['value'];
                        break 1;

                }

            }
            cache('sys_config', $result);

        }
        return $name != '' ? $result[$name] : $result;

    }
}