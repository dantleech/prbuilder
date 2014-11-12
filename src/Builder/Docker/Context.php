<?php

namespace PrBuilder\Builder\Docker;

use Docker\Context\ContextInterface;
use Docker\Context\Context as BaseContext;

class Context extends BaseContext
{
    public function setEnv(array $env)
    {
        $dockerFname = $this->getDirectory() . '/Dockerfile';
        $dockerFile = $dockerFname;
        $res = fopen($dockerFile, 'r');
        $out = array();


        while ($line = fgets($res)) {
            if (substr($line, 0, 4) === 'FROM') {
                $out[] = $line;
                foreach ($env as $key => $value) {
                    $out[] = 'ENV ' . $key . ' ' . $value;
                }
                continue;
            }
            $out[] = $line;
        }

        file_put_contents($dockerFname, implode("\n", $out));
        fclose($res);
    }
}
