<?php
spl_autoload_register(function ($c) { $p='/tmp/claude-0/-home-user-symfony/8adcc8db-592c-568b-8fe2-1cebba904b3e/scratchpad/wt-saturation/src/'.str_replace('\\','/',$c).'.php'; if (is_file($p)) require $p; });
use Symfony\Component\Console\Output\AnsiColorMode;
$N=['black','red','green','yellow','blue','magenta','cyan','white'];
foreach (['#606702','#6d0000','#2e7d32','#00796b','#795548','#607d8b','#eec7fa','#f5f5dc','#1565c0','#e65100','#c0c0c0','#ffffff','#000000','#808080','#a8a8a8','#2a2a2a','#eeeeee','#ff8080','#ffcccc','#ffa500','#ffd700','#87ceeb','#f0f8ff','#ff0000','#ffc0cb','#90ee90','#add8e6','#8b4513'] as $h) {
 [$r,$g,$b]=sscanf($h,'#%2x%2x%2x');
 $old=(int)(round($b/255)<<2|(round($g/255)<<1)|round($r/255));
 $new=(int)AnsiColorMode::Ansi4->convertFromHexToAnsiColorCode($h);
 $mx=max($r,$g,$b);$mn=min($r,$g,$b);
 printf("%s old=%s new=%s sat=%d%%\n",$h,$N[$old],$N[$new],$mx?round(100*($mx-$mn)/$mx):0);
}
