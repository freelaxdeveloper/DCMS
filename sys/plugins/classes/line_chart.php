<?php
namespace App;

/**
 * Class line_chart_series
 * @property int[] data
 */
class line_chart_series
{
    public $name;
    public $data = array();

    function __construct($name)
    {
        $this->name = $name;
    }
}

/**
 * Class line_chart
 * @property line_chart_series series
 * @property string[] categories
 */
class line_chart extends ui
{
    public $series = array();
    public $categories = array();
    public $y_text = '';
    public $value_suffix = '';
    public $title;
    public $description = '';

    public function __construct($title)
    {
        $this->title = $title;
        parent::__construct();
        $this->_tpl_file = 'chart.line.tpl';
    }

    public function fetch()
    {
        $id = mt_rand(1111, 9999);
        $value_suffix = $this->value_suffix;
        $categories = json_encode($this->categories);
        $series = json_encode($this->series);
        $title = $this->title;
        $description = $this->description;
        $y_text = $this->y_text;

        return view('chart', compact('categories', 'series', 'id', 'title', 'y_text', 'value_suffix', 'description'));
    }

} 