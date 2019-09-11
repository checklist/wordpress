<?php 
header('Content-type: image/svg+xml');
$color = (preg_match("/^[0-9A-Fa-f]{8}_.*/",$_GET["fill"]) ? $_GET["fill"] : "03a9f4");
echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg
   xmlns:dc="http://purl.org/dc/elements/1.1/"
   xmlns:cc="http://creativecommons.org/ns#"
   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   width="1024"
   height="1024"
   viewBox="0 0 819.2 819.2"
   version="1.1"
   id="svg2"
   >
  <defs
     id="defs16" />
  <path
     d="m 0.31304348,-1.1826087 66.27199952,0 c -19.536,4.144 -38.304,13.9999997 -50.384,30.2879997 -7.3279995,10.928 -13.2959995,22.944 -15.88799952,35.952 l 0,-66.2399997 z"
     id="path4"
     fill="none" />
  <path
     d="m 66.585043,-1.1826087 686.671997,0 c 15.936,2.816 29.696,11.8399997 42.672,21.0559997 -106.672,119.119999 -211.44,239.983999 -318.208,359.023999 -49.6,-44.72 -98.928,-89.728 -149.04,-133.872 -35.712,40.208 -71.04,80.768 -106.88,120.864 89.456,81.088 179.984,161.008 269.968,241.504 15.424,-15.68 29.76,-32.368 44.224,-48.896 94.512,-106.912 188.848,-213.984 283.52,-320.736 l 0,513.984 c -3.472,14.592 -8.96,29.296 -19.712,40.16 -11.632,14.128 -29.008,22.224 -46.576,26.112 l -686.639997,0 c -14.592,-3.472 -29.312,-8.96 -40.16,-19.712 -14.128,-11.632 -22.2239995,-29.008 -26.11199952,-46.576 l 0,-686.671999 c 2.59200002,-13.008 8.56000002,-25.024 15.88799952,-35.952 12.08,-16.288 30.848,-26.1439997 50.384,-30.2879997 z"
     id="path6"
     class="checklist-main-color"
     style="fill:#'.$color.'" />
  <path
     d="m 753.25704,-1.1826087 66.256,0 0,238.9439987 c -94.672,106.752 -189.008,213.824 -283.52,320.736 -14.464,16.528 -28.8,33.216 -44.224,48.896 -89.984,-80.496 -180.512,-160.416 -269.968,-241.504 35.84,-40.096 71.168,-80.656 106.88,-120.864 50.112,44.144 99.44,89.152 149.04,133.872 106.768,-119.04 211.536,-239.904 318.208,-359.023999 -12.976,-9.216 -26.736,-18.2399997 -42.672,-21.0559997 z"
     id="path8"
     fill="none" />
  <path
     d="m 0.31304348,751.72939 c 3.88800002,17.568 11.98399952,34.944 26.11199952,46.576 10.848,10.752 25.568,16.24 40.16,19.712 l -66.27199952,0 0,-66.288 z"
     id="path10"
     fill="none" />
  <path
     d="m 799.80104,791.90539 c 10.752,-10.864 16.24,-25.568 19.712,-40.16 l 0,66.272 -66.288,0 c 17.568,-3.888 34.944,-11.984 46.576,-26.112 z"
     id="path12"
     fill="none" />
</svg>';
?>