<?php

/**
 * Class ScrapeBillboards
 *
 * Скрейпер.
 */
class ScrapeBillboards
{
    protected $config;

    /**
     * @var string хост от url страницы для парсинга
     */
    protected $host;

    public function __construct($config)
    {
        $this->config = $config;
        $this->host = parse_url($this->config['page_path'], PHP_URL_HOST);
    }

    public function scrape()
    {
        $doc = new DOMDocument();
        if (!$doc->loadHTMLFile($this->config['page_path']))
        {
            throw new Exception('Не удалось загрузить документ ' . $this->config['page_path']);
        }

        $xpath = new DOMXpath($doc);
        $rows = $xpath->query('//form[@name="sel"]/table/tr');

        foreach ($rows as $row)
        {
            $billboardInfo = [
                'bb_address' => null,   // Адрес щита
                'bb_side' => null,      // Сторона щита
                'bb_link_owner_site' => null, // Ссылка на карточку щита (всплывающее окно с фото и схемой щита)
                'bb_image' => null,     // Ссылка на фото щита
                'bb_shema' => null      // Ссылка на схему щита
            ];

            $cols = $row->getElementsByTagName('td');
            if ($cols->length == 3)
            {
                $billboardInfo['bb_side'] = isset($this->config['side_letter']['associations'][$cols->item(
                        1
                    )->textContent]) ?
                    $this->config['side_letter']['associations'][$cols->item(1)->textContent] :
                    $this->config['side_letter']['exclusive'];

                $billboardInfo['bb_address'] = $cols->item(2);

                if ($link = $cols->item(2)->getElementsByTagName('a')->item(0))
                {
                    $click = $link->getAttribute("onclick");
                    preg_match("/'+(.*)'+/U", $click, $matches);

                    $url = $this->host . $matches[1];
                    $billboardInfo['bb_link_owner_site'] = $url;

                    $this->getBoardContent($url, $billboardInfo);
                }
            }

            #$xpath = new DOMXpath($row);
            #$td = $xpath->query('td');

        }
        exit;

        foreach ($articles as $container)
        {
            print_r($container);
            /*$arr = $container->getElementsByTagName("a");
            foreach ($arr as $item)
            {
                $click = $item->getAttribute("onclick");
                preg_match("/'+(.*)'+/U", $click, $matches);

                print_r($matches);
                die();

                #$text = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));
                echo $click . "\n";
            }*/
        }
    }

    protected function getBoardContent($url, &$billboardInfo)
    {

    }
}