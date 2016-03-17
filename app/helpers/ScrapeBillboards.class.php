<?php

/**
 * Class ScrapeBillboards
 *
 * Скрейпер.
 */
class ScrapeBillboards
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function scrape()
    {
        $doc = new DOMDocument();
        if (!$doc->loadHTMLFile($this->config['page_path']))
        {
            throw new Exception('Не удалось загрузить документ ' . $this->config['page_path']);
        }

        $host = parse_url($this->config['page_path'], PHP_URL_HOST);

        $xpath = new DOMXpath($doc);
        $rows = $xpath->query('//form[@name="sel"]/table/tr');

        foreach($rows as $row)
        {
            #print_r($row);
            $td = $row->query('td');
            print_r($td);
        }
        exit;

        foreach($articles as $container)
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

    protected function getBoardContent($url)
    {

    }
}