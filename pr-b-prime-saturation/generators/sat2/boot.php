<?php
require '/pu/vendor/autoload.php';
spl_autoload_register(function ($c) {
  if (preg_match('#^Symfony\\\\Component\\\\(\w+)\\\\(.*)$#', $c, $m)) { $p="/app/src/Symfony/Component/{$m[1]}/".str_replace('\\','/',$m[2]).'.php'; if (is_file($p)) require $p; }
  elseif (preg_match('#^Symfony\\\\Contracts\\\\(.*)$#', $c, $m)) { $p='/app/src/Symfony/Contracts/'.str_replace('\\','/',$m[1]).'.php'; if (is_file($p)) require $p; }
});
