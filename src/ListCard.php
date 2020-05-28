<?php

namespace Everestmx\NovaListCard;

use Laravel\Nova\Card;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

/**
 * Class ListCard
 * @package Everestmx\NovaListCard
 */
class ListCard extends Card
{
    /**
     * @var string
     */
    public $width = '1/3';
    public $resource;
    public $relationship;
    public $aggregate;
    public $aggregateColumn;
    public $limit = 5;
    public $orderColumn = 'created_at';
    public $orderDirection = 'desc';
    public $heading = [];
    public $subtitleEnabled = false;
    public $subtitleColumn;
    public $valueColumn;
    public $valueFormat;
    public $valueFormatter;
    public $timestampEnabled = false;
    public $timestampColumn;
    public $timestampFormat;
    public $footerLinkText;
    public $footerLinkType;
    public $footerLinkParams = [];
    public $classes;

    /**
     * @return string
     */
    public function component()
    {
        return 'nova-list-card';
    }

    /**
     * @param $resource
     * @return $this
     */
    public function resource($resource)
    {
        $this->resource = $resource;
        $this->classes($resource::uriKey());

        return $this;
    }

    /**
     * @param $relationship
     * @return $this
     */
    public function withCount($relationship)
    {
        $this->aggregate = 'count';
        $this->relationship = $relationship;

        return $this;
    }

    /**
     * @param $relationship
     * @param $column
     * @return $this
     */
    public function withSum($relationship, $column)
    {
        $this->aggregate = 'sum';
        $this->relationship = $relationship;
        $this->aggregateColumn = $column;

        return $this;
    }

    /**
     * @param $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->orderColumn = $column;
        $this->orderDirection = $direction;

        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param $left
     * @param null $right
     * @return $this
     */
    public function heading($left, $right = null)
    {
        $this->heading = ['left' => $left, 'right' => $right];
        $this->classes(Str::slug($left));

        return $this;
    }

    /**
     * @param null $column
     * @return $this
     */
    public function subtitle($column = null)
    {
        $this->subtitleEnabled = true;
        $this->subtitleColumn = $column;

        return $this;
    }

    /**
     * @param $column
     * @param null $format
     * @param string $formatter
     * @return $this
     */
    public function value($column, $format = null, $formatter = 'numerial')
    {
        $this->valueColumn = $column;
        $this->valueFormat = $format;
        $this->valueFormatter = $formatter;

        return $this;
    }

    /**
     * @param string $column
     * @param string $format
     * @return $this
     */
    public function timestamp($column = 'created_at', $format = 'MM/DD/YYYY')
    {
        $this->timestampEnabled = true;
        $this->timestampColumn = $column;
        $this->timestampFormat = $format;

        return $this;
    }

    /**
     * @return $this
     */
    public function viewAll()
    {
        return $this->footerRoute(__('View All'), 'index');
    }

    /**
     * @param $uriKey
     * @return $this
     */
    public function viewAllLens($uriKey)
    {
        return $this->footerRoute(__('View All'), 'lens', ['lens' => $uriKey]);
    }

    /**
     * @param $text
     * @param $href
     * @param string $target
     * @return $this
     */
    public function footerLink($text, $href, $target = '_blank')
    {
        return $this->footerRoute($text, 'href', [
            'text' => $text,
            'href' => $href,
            'target' => $target,
        ]);
    }

    /**
     * @param $text
     * @param $type
     * @param array $params
     * @return $this
     */
    public function footerRoute($text, $type, $params = [])
    {
        $this->footerLinkText = $text;
        $this->footerLinkType = $type;
        $this->footerLinkParams = $params;

        return $this;
    }

    /**
     * @return $this
     */
    public function zebra()
    {
        return $this->classes('zebra');
    }

    /**
     * @param $classes
     * @return $this
     */
    public function classes($classes)
    {
        $this->classes = $this->classes .= ' ' . $classes;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge([
            'limit' => $this->limit,
            'uri_key' => $this->uriKey(),
            // 'uri_key' => $this->resource::uriKey(),
            'relationship' => $this->relationship,
            'aggregate' => $this->aggregate,
            'aggregate_column' => $this->aggregateColumn,
            'order_column' => $this->orderColumn,
            'order_direction' => $this->orderDirection,
            'classes' => $this->classes,
            'heading' => $this->heading,
            'subtitle_enabled' => $this->subtitleEnabled,
            'subtitle_column' => $this->subtitleColumn,
            'value_column' => $this->valueColumn,
            'value_format' => $this->valueFormat,
            'value_formatter' => $this->valueFormatter,
            'timestamp_column' => $this->timestampColumn,
            'timestamp_enabled' => $this->timestampEnabled,
            'timestamp_format' => $this->timestampFormat,
        ], $this->footerLinkSettings(), parent::jsonSerialize());
    }

    /**
     * @return array
     */
    protected function footerLinkSettings()
    {
        $settings = [
            'footer_link_text' => $this->footerLinkText,
            'footer_link_type' => $this->footerLinkType,
        ];

        if (!is_null($this->footerLinkType) && 'href' != $this->footerLinkType && !isset($this->footerLinkParams['resourceName'])) {
            $this->footerLinkParams['resourceName'] = $this->resource::uriKey();
        }

        $settings['footer_link_params'] = $this->footerLinkParams;

        return $settings;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        return true; // authorizeToViewAny
    }
}
