<?php ini_set('display_errors', 1); error_reporting(E_ALL); echo 'Testing write permissions...'; \ = __DIR__ . '/public/profile-photos/test.svg'; \ = '<svg xmlns=\
http://www.w3.org/2000/svg\ width=\200\ height=\200\ viewBox=\0
0
200
200\><rect width=\200\ height=\200\ fill=\#ff0000\/><text x=\100\ y=\115\ font-family=\Arial
sans-serif\ font-size=\80\ font-weight=\bold\ text-anchor=\middle\ fill=\#ffffff\>OK</text></svg>'; \ = dirname(\); if(!file_exists(\)) { echo 'Creating directory: ' . \; mkdir(\, 0777, true); } \ = file_put_contents(\, \); echo 'File written: ' . (\ ? 'YES' : 'NO'); chmod(\, 0644); echo 'File URL: /profile-photos/test.svg'; ?>
