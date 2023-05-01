<?php

/**
 * Class Sahibinden
 * @author Tayfun Erbilen
 * @blog http://www.erbilen.net
 * @mail tayfunerbilen@gmail.com
 * @date 14.2.2014
 * @update 9.8.2018
 * @updater_mail facfur3@gmail.com
 */
class Sahibinden
{

    static $data = array();

    /**
     * Tüm Kategorileri Listelemek İçin Kullanılır
     *
     * @param null $url
     * @return array
     */
    static function Kategori($url = NULL)
    {
        if ($url != NULL) {
            $serv = new self();
            $open = $serv->Curl('https://www.sahibinden.com/alt-kategori/' . $url);
            //       $open = self::Curl( 'https://www.sahibinden.com/alt-kategori/' . $url );
            preg_match_all('@<li>(.*?)<a href="/(.*?)">(.*?)</a>(.*?)<span>(.*?)</span>(.*?)</li>@si', $open, $result);

            unset($result[2][0]);
            unset($result[3][0]);
            unset($result[5][0]);
            for ($i = 0; $i < count($result[2]); $i++) {
                self::$data[] = array(
                    'title' => $result[3][$i],
                    'icerik' => trim($result[5][$i]),
                    'uri' => trim($result[2][$i]),
                    'url' => 'https://www.sahibinden.com/' . $result[2][$i]
                );
            }
        } else {
            $serv = new self();
            $open = $serv->Curl('https://www.sahibinden.com/');
            //  $open = self::Curl( 'https://www.sahibinden.com/' );
            preg_match_all('@<li class="">(.*?)<a href="/kategori/(.*?)">(.*?)</a>(.*?)<span>((.*?))(.*?)</span>(.*?)</li>@si', $open, $result);
            foreach ($result[2] as $key => $val) {
                self::$data[] = array(
                    'title' => trim($result[3][$key]),
                    'icerik' => trim($result[7][$key]),
                    'uri' => str_replace('/kategori/', '', $result[2][$key]),
                    'url' => 'https://www.sahibinden.com/kategori/' . $result[2][$key]
                );
            }
        }
        return self::$data;
    }

    /**
     * Kategoriye ait ilanları listeler.
     *
     * @param $kategoriLink
     * @param string $sayfa
     * @return array
     */
    static function Liste($kategoriLink, $sayfa = '0')
    {
        $items = array();
        $serv = new self();
        $open = $serv->Curl('https://www.hepsiemlak.com/' . $kategoriLink . "?sorting=date_desc");



        $titles = array();
        $hrefs = array();
        $regex = '/<div\s+class="links"[^>]*>.*?<a\s+[^>]*href="([^"]+)"[^>]*title="([^"]+)"/si';

        preg_match_all($regex, $open, $matches, PREG_PATTERN_ORDER);

        foreach ($matches[1] as $key => $match) {
            $hrefs[] = $match;
            $titles[] = $matches[2][$key];
        }

        $list = array();
        foreach ($titles as $key => $value) {
            $list[] = array('title' => $value, 'href' => 'https://www.hepsiemlak.com' . $hrefs[$key]);
        }
//konum
$pattern = '/<div\s+class="list-view-location".*?>\s*<span.*?>(.*?)<\/span>\s*<span.*?>(.*?)<\/span>/s';
$matches = array();
 
preg_match_all($pattern, $open, $matches, PREG_SET_ORDER);
 
$ilce = array();
$mahalle = array();

// Her bir eşleşme için ilçe değerini alıp ilce dizisine ekliyoruz
foreach ($matches as $match) {
    preg_match('/<\/svg><\/span><\/span>(.*)/', $match[1], $ilce_match);
    $ilce[] = $ilce_match[1];
    $mahalle[] = $ilce_match[0];
}
$a=0;
foreach ($matches as $match) {
      $mahalle[$a] =  $match[2];
      $a++;
}

$ilce_cikarilmis = array();

foreach ($ilce as $value) {
  $ilce_cikarilmis[] = substr($value, 0, -2);
}


 
   //foto
 preg_match_all('/<picture[^>]*>\s*<img[^>]*src=["\']?([^>"\']+)["\']?[^>]*>\s*<\/picture>/i', $open, $matches);
$src_values = $matches[1];
 
  
foreach ($hrefs as $key => $href) {
    $ilanid[$key] = preg_replace('/^.*daire\//', '', $href);
}

        $list = array();
        foreach ($titles as $key => $value) {
            
            $list[] = array('title' => $value, 'link' => 'https://www.hepsiemlak.com' . $hrefs[$key],'foto'=>$src_values[$key],'ilanNo'=>$ilanid[$key],'adresilçe'=>$ilce_cikarilmis[$key],'adresmahalle'=>$mahalle[$key]);  
        }
       

        return $list;
    }

    /**
     * İlan detaylarını listeler.
     *
     * @param null $url
     * @return array
     */
    static function Detay($url = NULL)
    {
        if ($url != NULL) {
            $serv = new self();
            $open = $serv->Curl($url);
            //    $open = self::Curl( $url );



            // fotograflar
            preg_match_all('@<img src="(.*?)" data-src="(.*?)" width="640" (.*?)>@si', $open, $ciktis);
            foreach ($ciktis[2] as $val) {
                $images[] = $val;
            }

            // aciklama 

            preg_match('/<div\s.*?id="app".*?>(.*?)<\/div>/s',  $open, $matchAc);
          //sehir
         
          preg_match('/<ul class="short-info-list"[^>]*>(.*?)<\/ul>/', $open, $matches111);

          if (count($matches111) > 0) {
              $ul_tag = $matches111[0];
              preg_match_all('/<li[^>]*>(.*?)<\/li>/', $ul_tag, $li_matches);
              if (count($li_matches[0]) >= 3) {
                $il = $li_matches[1][0];
                $ilce = $li_matches[1][1];
                $mahalle = $li_matches[1][2];
                $sehirr = array("il" => $il, "ilce" => $ilce, "mahalle" => $mahalle);
              
          }
        }
          
            //ozellik
             
            $pattern = '/<li\s+class="spec-item"[^>]*>(.*?)<\/li>/si';
            preg_match_all($pattern, $open, $matches);
            $ozelliklers = array();
            foreach ($matches[1] as $match) {
                $doc = new DOMDocument();
                $doc->loadHTML(mb_convert_encoding($match, 'HTML-ENTITIES', 'UTF-8'));

                $spans = $doc->getElementsByTagName('span');
                $as = $doc->getElementsByTagName('a');

                $text = null;

                if ($spans->length == 2) {
                    $ozelliklers[] = [
                        "ad" => $spans->item(0)->textContent,
                        "sonuc" => $spans->item(1)->textContent
                    ];
                } elseif ($spans->length == 3) {
                    $ozelliklers[] = [
                        "ad" => $spans->item(0)->textContent,
                        "sonuc" => $spans->item(1)->textContent . " " . $spans->item(2)->textContent
                    ];
                }
            }

            // baslik
            preg_match('/<div\s+class="left"[^>]*>\s*<h1\s+class="fontRB"[^>]*>\s*(.*?)\s*<\/h1>\s*<\/div>/', $open, $baslik);

            // fiyat
            preg_match('/<p\s+class="fontRB\s+fz24\s+price"[^>]*>\s*(.*?)\s*<\/p>/', $open, $fiyat);
            // kisibilgi

            preg_match('/<div\s+class="detail-sub"[^>]*>\s*<div\s+class="firm-link\s+fontRB"[^>]*>\s*([^<]+)/', $open, $eslesme);
            $div_icerik = trim($eslesme[1]);



            preg_match_all('/<a[^>]*\s+href="([^"]*)"/', $open, $kisitl);

            $filtreli_dizi = array_filter($kisitl[1], function ($eleman) {
                return substr($eleman, 0, 4) === "tel:";
            });
            $gelentel = array_values($filtreli_dizi);

            $kisib = array(
                'kisiAd' => $div_icerik,
                'kisitel' =>  substr($gelentel[0], 5)


            );

            /////////////////////////////////////////////////
            $detayanadizi = array(
                'url' => $url,
                'baslik' => $baslik[1],
                'adres' => $sehirr,
                'fiyat' => $fiyat[1],
                "kisi" => $kisib,
                'foto' => $images,
                'ozellik' => $ozelliklers,
                'aciklama' =>   $matchAc[1]

            );

            return $detayanadizi;
        }
    }

    /**
     * Gereksiz boşlukları temizler.
     *
     * @param $string
     * @return string
     */
    private function replaceSpace($string)
    {
        $string = preg_replace("/\s+/", " ", $string);
        $string = trim($string);
        return $string;
    }

    /**
     * @param $url
     * @param null $proxy
     * @return mixed
     */
    private function Curl($url, $proxy = NULL)
    {
        $_deneme = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36";
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT   => $_deneme,
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => true
        );

        $ch = curl_init("$url");

        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);



        curl_close($ch);


        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $string = '/searchResultsItem(\s+)"/';
        $links = "/(\s)classifiedTitle/";


        $header['content'] = preg_replace("/\s{2,}/", " ", $content);


        $header['content'] = str_replace(array("\n", "\r", "\t"), "", $header['content']);

        $header['content'] = preg_replace($string, 'searchResultsItem"', $header['content']);
        $header['content'] = preg_replace($links, 'classifiedTitle', $header['content']);

        return $header['content'];



        //return str_replace(array("\n", "\r", "\t"), "", $header['content']);
    }
}
