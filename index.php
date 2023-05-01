<?php

header('Content-type: text/html; charset=utf8');
require 'sahibinden.class.php';

/*// ana kategoriler
print_r( Sahibinden::Kategori() );

// alt kategoriler

Sahibinden::Kategori('emlak');


// kategori içerikleri
 
Sahibinden::Liste('emlak', 20); // 2. sayfa
 
 */
// içerik detayı (henüz tamamlanmadı)

 //$link = Sahibinden::Detay("https://www.sahibinden.com/ilan/vasita-otomobil-bmw-rmz-den-2015-m-sport-e.bagaj-recore-k.isitma-g.gorus-hafiza-1074337470/detay");
                 



  /* foreach ($link as $row) {
    echo "<a href='" . $row['url'] . "' target='_blank'>" . $row['title'] . "</a></br>";
    $test++;
}  
 $link= Sahibinden::Liste('otomobil');
  foreach ($link as $row) {

 $detay = Sahibinden::Detay($row['url']);
    print_r($detay);


    echo "<a href='" . $row['url'] . "' target='_blank'>" . $row['title'] . "</a></br>";
} 
*/






  $link = Sahibinden::Detay("https://www.hepsiemlak.com/sakarya-karasu-aziziye-satilik/daire/122716-485");
  print_r($link);
 //$link= Sahibinden::Liste('satilik', 20);
 //print_r($link);