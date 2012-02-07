<?php

/*
 * This file is part of the PublisherConsole package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stats;

class App
{
    protected $name;
    protected $types;
    protected $values;
    protected $is_event = false;
    protected $config;

    public function run($argv)
    {
        try
        {
            $this->loadConfig();

            $command = isset($argv[1]) ? $argv[1] : null;

            switch ($command)
            {
                case 'config':
                    $this->configure($argv);
                    break;
                default:
                    $this->publishMessage();
                    break;
            }
        }
        catch (\Exception $e)
        {
            echo $e->getMessage() . "\n";
        }
    }

    protected function configure($argv)
    {
        $config_command = isset($argv[2]) ? $argv[2] : null;
        switch ($config_command)
        {
            case 'set':
                $config_parameter = isset($argv[3]) ? $argv[3] : null;
                $config_value = isset($argv[4]) ? $argv[4] : null;
                $this->setConfigure($config_parameter, $config_value);
                break;
            default:
                print_r($this->config);
                break;
        }
    }

    protected function setConfigure($config_parameter, $config_value)
    {
        if ($config_parameter == null) return;

        $arr_config = explode('.', $config_parameter);
        $eval = "\$this->config['" . implode("']['", $arr_config) . "'] = \$config_value;";
        eval($eval);
        file_put_contents(__DIR__ . '/../../config/app.yml', \Symfony\Component\Yaml\Yaml::dump($this->config, 10));
    }

    protected function publishMessage()
    {
        // get options
        $this->getOptionsFromCommandLine();

        if ($this->is_event) $this->publishEventMessage();
        else $this->publishStatMessage();
    }

    protected function publishStatMessage()
    {
        $values = array();
        foreach ($this->types as $k => $type)
        {
            if ($this->values[$k] == 'STDIN') {
                $data = '';
                while (!feof(STDIN))
                {
                    $data .= fread(STDIN, 8192);
                }
                $values[] = array($type => $data);
            }
            else $values[] = array($type => $this->values[$k]);
        }
        $msg = array(
            'name' => $this->name,
            'ts' => date('Y-m-d H:i:s'),
            'values' => $values
        );
        //        print_r($msg);

        // publish
        $this->publish($msg, 'stats');
    }

    protected function publishEventMessage()
    {
        $msg = array(
            'event' => $this->name,
            'ts' => date('Y-m-d H:i:s'),
        );

        // publish
        $this->publish($msg, 'events');
    }

    protected function loadConfig()
    {
        // load yml config
        $this->config = \Symfony\Component\Yaml\Yaml::parse(__DIR__ . '/../../config/app.yml');
    }

    protected function publish($msg, $exhange)
    {
        $msg = json_encode($msg);
        foreach ($this->config['publishers'] as $publish_config)
        {
            $publisher = new $publish_config['class']($publish_config['config'], $exhange);
            $publisher->publish($msg);
        }
    }

    protected function getOptionsFromCommandLine()
    {
        $parameters = array(
            'h' => 'help',
            'n:' => 'name:',
            't:' => 'types:',
            'v:' => 'values:',
            'e' => 'event',
        );

        $config = array();

        $options = getopt(implode('', array_keys($parameters)), $parameters);
        if (count($options) == 0) {
            throw new \Exception('Parameters required.');
        }
        //        print_r($options);
        foreach ($options as $option => $value)
        {
            switch ($option)
            {
                case 'h':
                case 'help':
                    echo <<<EOT
publish stat:
    php publisher_cli.phar --name=[stat_name] --types=[type1,type2...] --values=[STDIN|value,STDIN|value...]

publish event:
    php publisher_cli.phar --name=[event_name] --event

configure:
  get current configuration:
    php publisher_cli.phar config
  set configuration:
    php publisher_cli.phar config set [param1.subparam1.subsubparam1] [new_value]


EOT;
                    exit();
                    break;
                case 'n':
                case 'name':
                    if (is_string($value)) {
                        //                        echo "$option: $value\n";
                        $this->name = $value;
                    } else
                    {
                        throw new \Exception('Illegal value for "' . $option . '" parameter.');
                    }
                    break;
                case 't':
                case 'types':
                    if (is_string($value)) {
                        //                        echo "$option: $value\n";
                        $types = explode(',', $value);
                        foreach ($types as $k => $v) $types[$k] = trim($v);
                        $this->types = $types;
                    } else
                    {
                        throw new \Exception('Illegal value for "' . $option . '" parameter.');
                    }
                    break;
                case 'v':
                case 'values':
                    if (is_string($value)) {
                        $values = explode(',', $value);
                        foreach ($values as $k => $v) $values[$k] = trim($v);
                        $this->values = $values;
                    } else
                    {
                        throw new \Exception('Illegal value for "' . $option . '" parameter.');
                    }
                    break;
                case 'e':
                case 'event':
                    $this->is_event = true;
                    break;
            }
        }
    }
}
