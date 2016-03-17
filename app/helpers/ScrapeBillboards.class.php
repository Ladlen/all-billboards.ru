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
        $scheme = parse_url($this->config['page_path'], PHP_URL_SCHEME);
        $this->host = $scheme . '://' . parse_url($this->config['page_path'], PHP_URL_HOST);
    }

    /**
     * Входная функция парсинга.
     *
     * @return array массив информации о щитах
     * @throws Exception
     */
    public function scrape()
    {
        $doc = new DOMDocument();
        if (!$doc->loadHTMLFile($this->config['page_path']))
        {
            throw new Exception('Не удалось загрузить документ ' . $this->config['page_path']);
        }

        $xpath = new DOMXpath($doc);
        $rows = $xpath->query('//form[@name="sel"]/table/tr');

        $boards = [];

        foreach ($rows as $row)
        {
            if ($billboardInfo = $this->parseBillboard($row))
            {
                $boards[] = $billboardInfo;
            }
        }

        return $boards;
    }

    /**
     * Парсит информацию об одном щите.
     *
     * @param DOMElement $row строка таблицы где предполагаемо находится описание щита
     * @return array массив с информацией о щите
     */
    protected function parseBillboard($row)
    {
        $billboardInfo = false;

        $cols = $row->getElementsByTagName('td');
        if ($cols->length == 3)
        {
            $billboardInfo = [
                'bb_address' => null,   // Адрес щита
                'bb_side' => null,      // Сторона щита
                'bb_link_owner_site' => null, // Ссылка на карточку щита (всплывающее окно с фото и схемой щита)
                'bb_image' => null,     // Ссылка на фото щита
                'bb_shema' => null      // Ссылка на схему щита
            ];

            $billboardInfo['bb_side'] = isset($this->config['side_letter']['associations'][$cols->item(
                    1
                )->textContent]) ?
                $this->config['side_letter']['associations'][$cols->item(1)->textContent] :
                $this->config['side_letter']['exclusive'];

            $billboardInfo['bb_address'] = $cols->item(2)->textContent;

            if ($link = $cols->item(2)->getElementsByTagName('a')->item(0))
            {
                $click = $link->getAttribute("onclick");
                preg_match("/'+(.*)'+/U", $click, $matches);

                $url = $this->host . $matches[1];
                $billboardInfo['bb_link_owner_site'] = $url;

                $this->getBoardContent($url, $billboardInfo);
            }
        }

        return $billboardInfo;
    }

    /**
     * Размещает в массиве описания щита пути к изображению и схеме.
     *
     * @param string $url путь к странице с изображением и схемой щита
     * @param array $billboardInfo массив описывающий щит
     */
    protected function getBoardContent($url, &$billboardInfo)
    {
        $doc = new DOMDocument();
        if (!$doc->loadHTMLFile($url))
        {
            echo('Не удалось загрузить документ ' . $this->config['page_path'] . "<br>\n");
            return;
        }

        $xpath = new DOMXpath($doc);
        $cols = $xpath->query('//table/tr/td');

        $this->putImageIntoBillboardInfo($billboardInfo, 'bb_image', $cols->item(0));
        $this->putImageIntoBillboardInfo($billboardInfo, 'bb_shema', $cols->item(1));
    }

    /**
     * Берет путь к изображению в элементе, и если путь к изображению существует, а также изображение доступно,
     * то помещает url в элемент ассоциативного массива по заданному ключу.
     *
     * @param array $billboardInfo массив для установки значений
     * @param string $arrayElementName название ключа массива $billboardInfo
     * @param DOMElement $column элемент с изображением
     */
    protected function putImageIntoBillboardInfo(&$billboardInfo, $arrayElementName, $column)
    {
        if ($img = $column->getElementsByTagName('img')->item(0))
        {
            if ($src = trim($img->getAttribute('src')))
            {
                $url = $this->host . $src;

                // Проверим доступность.
                if (file_get_contents($url))
                {
                    $billboardInfo[$arrayElementName] = $this->host . $src;
                }
            }
        }
    }
}
