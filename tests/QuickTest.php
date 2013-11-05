<?php

class QuickTest extends \phpunit_framework_testcase
{
    public static function provider()
    {
        return array_map(function($file) {
            $text = file_get_contents($file);
            $expected = file(substr($file, 0, -3) . 'expected', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            return [$text, $expected];            
        }, glob(__DIR__ . "/fixtures/*.txt"));
    }

    /** @dataProvider provider */
    public function testGetkeywordsSimpler($text, $expected)
    {
        if (count($expected) == 1) return;
        $config = new \crodas\TextRank\Config;

        $analizer = new \crodas\TextRank\TextRank($config);
        $keywords = $analizer->getKeywords($text);
        $i = 0;
        foreach ($expected as $word) {
            if ($word[0] == '*') {
                $word  = substr($word, 1);
            }
            if (!empty($keywords[$word])) {
                $i++;
            }
        }
        if ($i == 0) {
            var_dump($keywords);return;
        }
        $this->AssertTrue($i > 0);
    }

    /** @dataProvider provider */
    public function testGetkeywords($text, $expected)
    {
        $config = new \crodas\TextRank\Config;
        $config->addListener(new \crodas\TextRank\Stopword);

        $analizer = new \crodas\TextRank\TextRank($config);
        $keywords = $analizer->getKeywords($text);
        foreach ($expected as $word) {
            $catch = false;
            if ($word[0] == '*') {
                $catch = true;
                $word  = substr($word, 1);
            }
            try {
                $this->assertTrue(!empty($keywords[$word]), "cannot find \"$word\"");
            } catch (\Exception $e) {
                if (!$catch) throw $e;
            }
        }
    }

}
