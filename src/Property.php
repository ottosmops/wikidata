<?php

namespace Wikidata;

use Wikidata\Value;
use Illuminate\Support\Collection;

class Property
{
  /**
   * @var string Property Id
   */
  public $id;

  /**
   * @var string Property label
   */
  public $label;

  /**
   * @var \Illuminate\Support\Collection Collection of property values
   */
  public $values;

  /**
   * @param Collection $data
   */
  public function __construct(Collection $data)
  {
    $this->parseData($data);
  }

  /**
   * Parse input data
   *
   * @param Collection $data
   */
  private function parseData(Collection $data) : void
  {
    $grouped = $data->groupBy('statement');
    $flatten = $grouped->flatten(1);

    $this->id = get_id($flatten[0]['prop']);
    $this->label = $flatten[0]['propertyLabel'];
    $this->values = $grouped->values()->map(fn($v)  => new Value($v->toArray()));
  }
}
