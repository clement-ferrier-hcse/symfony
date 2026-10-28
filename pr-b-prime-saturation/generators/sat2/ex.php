<?php
spl_autoload_register(function ($c) { $p='/app/src/'.str_replace('\\','/',$c).'.php'; if (is_file($p)) require $p; });
use Symfony\Component\Console\Output\AnsiColorMode;
$N=['black','red','green','yellow','blue','magenta','cyan','white'];
function sat($r,$g,$b){$r/=255.0;$g/=255.0;$b/=255.0;$v=max($r,$g,$b);if(0.0===$d=$v-min($r,$g,$b))return 0;return (int)($d*100/$v);}
foreach (['#66886d','#88666d','#777792','#997792','#a08a8a','#a09090','#907878','#8a7a7a','#ffffff','#eeeeee','#c0c0c0','#808080','#7f7f7f','#2a2a2a','#000000','#eec7fa','#f5f5dc','#ffcccc','#ff8080','#87ceeb','#606702','#1565c0','#e65100','#ffd700','#795548','#607d8b','#8b4513','#bc8f8f','#9e9e9e','#757575','#795e5e','#a1887f'] as $h) {
 [$r,$g,$b]=sscanf($h,'#%2x%2x%2x');
 $old=(int)(round($b/255)<<2|(round($g/255)<<1)|round($r/255));
 $new=(int)AnsiColorMode::Ansi4->convertFromHexToAnsiColorCode($h);
 printf("%s sat=%d old=%s new=%s %s\n",$h,sat($r,$g,$b),$N[$old],$N[$new],$old!==$new?'CHANGED':'');
}
