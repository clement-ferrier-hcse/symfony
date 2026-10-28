<?php
spl_autoload_register(function ($c) { $p='/tmp/claude-0/-home-user-symfony/8adcc8db-592c-568b-8fe2-1cebba904b3e/scratchpad/wt-saturation/src/'.str_replace('\\','/',$c).'.php'; if (is_file($p)) require $p; });
use Symfony\Component\Console\Output\AnsiColorMode;
$N=['black','red','green','yellow','blue','magenta','cyan','white'];
// standalone copy of the logic
function sat($r,$g,$b){$r/=255.0;$g/=255.0;$b/=255.0;$v=max($r,$g,$b);if(0.0===$d=$v-min($r,$g,$b))return 0;return (int)($d*100/$v);}
function nw($r,$g,$b){ if(0.0===round(sat($r,$g,$b)/50)) return max($r,$g,$b)<128?0:7; return (int)((round($b/255)<<2)|(round($g/255)<<1)|round($r/255));}
$trans=[];$ch=0;$mism=0;$n=0;$bl=0;$wh=0;$oldc=array_fill(0,8,0);$newc=array_fill(0,8,0);
for($r=0;$r<256;$r++)for($g=0;$g<256;$g++)for($b=0;$b<256;$b++){
 $old=(int)(round($b/255)<<2|(round($g/255)<<1)|round($r/255));
 $new=nw($r,$g,$b);
 $oldc[$old]++;$newc[$new]++;
 if($new!==$old){$ch++;$k="$old>$new";$trans[$k]=($trans[$k]??0)+1;}
 // verify vs real code on a sample + all of a stride
 if(($r*7+$g*13+$b)%5==0 || $r%17==0&&$g%17==0 ){ $real=(int)AnsiColorMode::Ansi4->convertFromHexToAnsiColorCode(sprintf('#%02x%02x%02x',$r,$g,$b)); $n++; if($real!==$new)$mism++; }
}
echo "changed $ch of 16777216 (".round(100*$ch/16777216,2)."%)\n"; ksort($trans); print_r($trans); echo "verified $n mismatches $mism\n"; echo json_encode([$oldc,$newc]),"\n";
