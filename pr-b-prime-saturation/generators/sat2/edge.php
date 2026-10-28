<?php
spl_autoload_register(function ($c) { $p='/tmp/claude-0/-home-user-symfony/8adcc8db-592c-568b-8fe2-1cebba904b3e/scratchpad/wt-saturation/src/'.str_replace('\\','/',$c).'.php'; if (is_file($p)) require $p; });
use Symfony\Component\Console\Output\AnsiColorMode;
foreach(['#c89696','#ffbfbf','#ffc0c0','#7f7a7a','#807a7a','#ff8080','#ffcccc'] as $h) echo $h,' ',AnsiColorMode::Ansi4->convertFromHexToAnsiColorCode($h),"\n";
