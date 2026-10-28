<?php
function sat($r,$g,$b){$r/=255.0;$g/=255.0;$b/=255.0;$v=max($r,$g,$b);if(0.0===$d=$v-min($r,$g,$b))return 0;return (int)($d*100/$v);}
function nw($r,$g,$b,$low){ if($low) return max($r,$g,$b)<128?0:7; return (int)((round($b/255)<<2)|(round($g/255)<<1)|round($r/255));}
$dec=0;$res=0;$ex=[];$ex2=[];
for($r=0;$r<256;$r++)for($g=0;$g<256;$g++)for($b=0;$b<256;$b++){
 $mx=max($r,$g,$b);$mn=min($r,$g,$b);
 $exactLow = $mx===0 || ($mx-$mn)*100 < 25*$mx;  // sat < 25% exactly
 $codeLow = 0.0===round(sat($r,$g,$b)/50);
 if($exactLow!==$codeLow){$dec++; if(nw($r,$g,$b,$exactLow)!==nw($r,$g,$b,$codeLow)){$res++; if(count($ex)<5)$ex[]=sprintf('#%02x%02x%02x code=%s exact=%s sat=%d',$r,$g,$b,$codeLow?'low':'high',$exactLow?'low':'high',sat($r,$g,$b));}}
}
echo "decision differs $dec, result differs $res\n"; print_r($ex);
