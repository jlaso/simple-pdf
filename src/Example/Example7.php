<?php

namespace PHPfriends\SimplePdf\Example;

include __DIR__.'/../../vendor/autoload.php';

use PHPfriends\SimplePdf\Adaptor\SyllableAdaptor;
use PHPfriends\SimplePdf\Common\Config;
use PHPfriends\SimplePdf\Events\Events;
use PHPfriends\SimplePdf\Events\LineBreakEvent;
use PHPfriends\SimplePdf\Events\PageBreakEvent;
use PHPfriends\SimplePdf\LowLevelParts\Color;
use PHPfriends\SimplePdf\LowLevelParts\PdfDate;
use PHPfriends\SimplePdf\Main\HighLevelPdf;
use PHPfriends\SimplePdf\Main\MarkdownPdf;
use Solaris\MoonPhase;

class Example7 extends AbstractExample
{
    const DAY_START = 1;  // 0=>sunday, 1=>monday
    const LANGUAGE = 'es';
    const TWO_SHEETS_PER_MONTH = true;
    const FROM_MONTH = 1;
    const TO_MONTH = 12;
    const YEAR = 2017;
    const FONT_NAME = 'Lato';
    const FONT_STYLE = 'Regular';
    const FONT_DAY_SIZE = 40;
    const FONT_MOON_SIZE = 13;
    const FONT_HOLIDAY_SIZE = 11;
    const FONT_DOW_SIZE = 18;
    const FONT_MINI_DAY_SIZE = 12;
    const FONT_MONTH_SIZE = 50;
    const FONT_MINI_MONTH_SIZE = 15;

    /** @var HighLevelPdf */
    protected $pdf;
    protected $verbose = false;
    protected $currentMonth;
    protected $currentWeek;
    protected $currentDay;
    /** @var string[] */
    protected $months;
    /** @var string[] */
    protected $days;

    public function process()
    {
        $this->months = $this->getMonths(self::LANGUAGE);
        $this->days = $this->getDays(self::LANGUAGE);

        $pdf = new HighLevelPdf(792.0, 612.0, $this->verbose);
        $this->pdf = $pdf;
        $pdf->setTwoSided(false);

        $pdf->setMetadata('Title', 'Example 7 @ Markdown');
        $pdf->setMetadata('Author', '@PHPfriendsTK');
        $pdf->setMetadata('Creator', 'https://github.com/PHPfriends/simple-pdf');
        $pdf->setMetadata('Producer', 'https://packagist.org/packages/phpfriends/simple-pdf');
        $pdf->setMetadata('CreationDate', new PdfDate());
        $pdf->setMetadata('Keywords', ['simple-pdf', 'example', 'PHPfriends']);

        $pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_DAY_SIZE);
        $pdf->setCell();
        $pdf->writeText("Calendar");

        for($month = self::FROM_MONTH; $month <= self::TO_MONTH; $month++) {
            $this->newPage($month);
        }

        $pdf->saveToFile(__DIR__ . '/test7.pdf');
    }

    private function centerText($x,$y,$w,$h,$text)
    {
        $wText = $this->pdf->getRealWidth($text);
        $x += ($w - $wText)/2;
        $this->pdf->setCell($x,$y,$w,$h);
        $this->pdf->writeText($text);
    }

    private function rightText($x,$y,$w,$h,$text)
    {
        $wText = $this->pdf->getRealWidth($text);
        $x += $w - $wText;
        $this->pdf->setCell($x,$y,$w,$h);
        $this->pdf->writeText($text);
    }

    private function leftText($x,$y,$w,$h,$text)
    {
        $this->pdf->setCell($x,$y,$w,$h);
        $this->pdf->writeText($text);
    }

    private function newPage($month)
    {
        $this->currentMonth = $month;
        $weeks = $this->splitMonthInWeeks($month);

        $this->pdf->newPage();
        $marginLeft = $this->pdf->getLeftX();
        $pageWidth = $this->pdf->getRightX() - $marginLeft;
        $latWidth = $pageWidth/5;
        $monthWidth = $pageWidth-(2*$latWidth);
        $this->pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_MONTH_SIZE);
        $text = $this->months[$month].' '.self::YEAR;
        $this->centerText($marginLeft + $latWidth, null, $monthWidth,null, $text);

        $this->drawMiniMonth($month > 1 ? $month-1 : 12, $month > 1 ? self::YEAR : self::YEAR - 1, true);
        $this->drawMiniMonth($month < 12 ? $month+1 : 1, $month < 12 ? self::YEAR : self::YEAR + 1, false);
        $this->drawRows();

        if(!self::TWO_SHEETS_PER_MONTH) {
            $this->drawWeeks($weeks, 1);

            return;
        }
        $weeks1 = array_slice($weeks, 0, 3);
        $this->drawWeeks($weeks1, 1);

        $this->pdf->newPage();
        $this->drawRows(true);

        $weeks2 = array_slice($weeks, 3);
        $this->drawWeeks($weeks2);
    }

    private function drawMiniMonth($month, $year = self::YEAR, $leftSide = true)
    {
        $blocks = self::TWO_SHEETS_PER_MONTH ? 4 : 8;
        $blockHeight = 0.75 * $this->pdf->getMaxHeight() / $blocks;
        $marginTop = $this->pdf->getTopY();
        $marginLeft = $this->pdf->getLeftX();
        $pageWidth = $this->pdf->getRightX() - $marginLeft;
        $latWidth = $pageWidth/5;
        $this->pdf->rectangle(
            $leftSide ? $marginLeft : $this->pdf->getRightX()-$latWidth,
            $blockHeight + $marginTop,
            $latWidth,
            $blockHeight
        );
        $this->pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_MINI_MONTH_SIZE);
        $this->centerText(
            $leftSide ? $marginLeft : $this->pdf->getRightX()-$latWidth,
            $marginTop + 5 - $blockHeight,
            $latWidth,
            $blockHeight + 15,
            $this->months[$month].' '.$year
        );
        // draw days
        $this->pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_MINI_DAY_SIZE);
        $weeks = $this->splitMonthInWeeks($month, $year);
        $row = 1;
        $dayWidth = ($latWidth-10)/7;
        $top = $marginTop - 8;
        $left = $leftSide ? $marginLeft : $this->pdf->getRightX()-$latWidth;
        $dayHeight = $blockHeight/8;
        $fontH = $this->pdf->getCurrentFontHeight();
        foreach($weeks as $days){
            foreach($days as $dow => $day){
                $this->setColor($dow, $day, $month, $year);
                $this->rightText(
                    $left + $dow*$dayWidth,
                    $top+$row*$dayHeight+$fontH,
                    $dayWidth,
                    $dayHeight,
                    $day
                );
            }
            $row++;
        }
    }

    private function setColor($dow, $day = null, $month = null, $year = self::YEAR)
    {
        $text = $this->checkHoliday($month, $day, $year);

        if($text || ((0 === self::DAY_START) && ($dow === 0)) ||
            (1 === self::DAY_START) && ($dow === 6)) {
            $this->pdf->selectFontColor('red');
        }else{
            $this->pdf->selectFontColor('black');
        }

        return $text;
    }

    private function drawRows($isSecondPage = false)
    {
        $marginLeft = $this->pdf->getLeftX();
        $marginTop = $this->pdf->getTopY();
        $blocks = self::TWO_SHEETS_PER_MONTH ? 4 : 8;
        $blockHeight = $this->pdf->getMaxHeight() / $blocks;

        for($block = 1; $block <= $blocks; $block++){
            if(!$isSecondPage && (1===$block)){
                $this->pdf->rectangle(
                    $marginLeft,
                    $marginTop + $block * $blockHeight - $blockHeight/4,
                    null,
                    3*$blockHeight/4
                );
            }else {
                $this->pdf->rectangle(
                    $marginLeft,
                    $marginTop + $block * $blockHeight,
                    null,
                    $blockHeight
                );
            }
        }
    }

    /**
     * @param int $month
     * @param int $day
     * @param int $year
     * @return int
     */
    private function thisDate($month = null, $day = null, $year = self::YEAR)
    {
        return strtotime(
            sprintf(
                '%04d-%02d-%02d',
                $year,
                $month ?: $this->currentMonth,
                $day ?: $this->currentDay
            )
        );
    }

    /**
     * @param int $month
     * @param int $year
     * @return array
     */
    private function splitMonthInWeeks($month, $year = self::YEAR)
    {
        $weeks = [];
        $numDays = intval(date('t', $this->thisDate($month, 1, $year)));
        $startDay = intval(date('w', $this->thisDate($month, 1, $year)));
        if(self::DAY_START === 1){
            if(0==$startDay){
                $startDay = 6;
            }else{
                $startDay--;
            }
        }
        $week = 0;
        for($day = 1; $day <= $numDays; $day++){
            $weeks[$week][$startDay] = $day;
            $startDay++;
            if($startDay > 6){
                $startDay = 0;
                $week++;
            }
        }

        return $weeks;
    }

    private function drawWeeks($weeks, $offset = 0)
    {
        $blocks = self::TWO_SHEETS_PER_MONTH ? 4 : 8;
        $blockHeight = $this->pdf->getMaxHeight() / $blocks;
        $width = $this->pdf->getMaxWidth();
        $marginLeft = $this->pdf->getLeftX();
        $marginTop = $this->pdf->getTopY();
        $dayWidth = $width/7;
        if($offset) {
            $this->pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_DOW_SIZE);
            $dayWeekHeight = $blockHeight / 4;
            foreach ($this->days as $dow => $dayName) {
                $this->setColor($dow);
                $this->centerText(
                    $marginLeft + $dow * $dayWidth,
                    $marginTop + $blockHeight - $dayWeekHeight - 5,
                    $dayWidth - 5,
                    $dayWeekHeight,
                    $dayName
                );
            }
        }
        $this->pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_DAY_SIZE);
        $row = 1 + $offset;
        $fontH = $this->pdf->getCurrentFontHeight();
        $lastMoon = null;
        foreach($weeks as $days){
            foreach($days as $dow => $day){
                $this->pdf->rectangle(
                    $marginLeft + $dow*$dayWidth,
                    $marginTop+$row*$blockHeight,
                    $dayWidth,
                    $blockHeight
                );
                $holiday = $this->setColor($dow, $day, $this->currentMonth);
                $this->rightText(
                    $marginLeft + $dow*$dayWidth,
                    $marginTop+($row-2)*$blockHeight+$fontH,
                    $dayWidth - 5,
                    $blockHeight - 15,
                    $day
                );
                if($holiday){
                    $this->pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_HOLIDAY_SIZE);
                    $this->leftText(
                        5 + $marginLeft + $dow * $dayWidth,
                        $marginTop + $row * $blockHeight - $fontH - 5,
                        $dayWidth - 5,
                        $fontH,
                        $holiday
                    );
                    $this->pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_DAY_SIZE);
                }

                $moonPhase = new MoonPhase($this->thisDate(null, $day));
                $moon = $this->translateMoon($moonPhase->phase_name());
                if($moon != $lastMoon) {
                    $lastMoon = $moon;
                    $this->pdf->selectFontColor('black');
                    $this->pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_MOON_SIZE);
                    $this->leftText(
                        5 + $marginLeft + $dow * $dayWidth,
                        $marginTop + $row * $blockHeight - $fontH - 5,
                        $dayWidth - 5,
                        $fontH,
                        $moon
                    );
                    $this->pdf->setFont(self::FONT_NAME, self::FONT_STYLE, self::FONT_DAY_SIZE);
                }
            }
            $row++;
        }
    }

    private function getMonths($language)
    {
        if('es' == $language){
            return ['0','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
        }

        return range(0,12);
    }

    private function getDays($language)
    {
        if('es' == $language){
            $days = ['LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO'];
            if(1 === self::DAY_START) {
                $days[] = 'DOMINGO';
            }else{
                array_unshift($days, 'DOMINGO');
            }
            return $days;
        }

        return range(0,6);
    }

    private function checkHoliday($month, $day, $year = self::YEAR)
    {
        $holidays = [
            '2017-01-06' => 'Reyes',
            '2017-04-13' => 'J. Santo',
            '2017-04-14' => 'V. Santo',
            '2017-04-17' => 'L. Pascua',
            '2017-05-01' => 'D. trabajo',
            '2017-08-15' => 'Vgen Agosto',
            '2017-10-09' => 'C. Valenciana',
            '2017-10-12' => 'Hispanidad',
            '2017-11-01' => 'Tots sants',
            '2017-12-06' => 'Const.Esp.',
            '2017-12-08' => 'Inmac.Cpcion',
            '2017-12-25' => 'Natividad',
        ];

        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

        return isset($holidays[$date]) ? $holidays[$date] : false;
    }


    private function translateMoon($moon)
    {
        if(self::LANGUAGE == 'es'){
            return [
                'New Moon' => 'Nueva',
                'Full Moon' => 'Llena',
                'Waning Crescent' => '', //'Creciente',
                'Waxing Crescent' => '', //'Creciente',
                'Waning Gibbous' => '', //'Menguante',
                'Waxing Gibbous' => '', //'Menguante',
                'Third Quarter' => '', //'Menguante',
                'First Quarter' => '', //'Creciente',
            ][$moon];
        }
        return $moon;
    }

}

Example7::main();
